#!/usr/bin/env python
# -*- coding: utf-8 -*-
#
import sys
import os
import threading
import zmq
import json
import logging
import pyone
import logging.config
import configparser
import argparse
import base64
import random
from security.aes_cbc import *
from commands.Dispatcher import CommandsDispatcher

def args_parse():
    """
    Arguments parsing:
    -c [/path/to/file]- configuration file path (default - current directory, file config.ini)  
    """
    
    parser = argparse.ArgumentParser()
    parser.add_argument('-c', default='config.ini')
    args = parser.parse_args()
    
    return args
    
    
def config_parser(config_file):
    """
    Configuration file parsing.
    Returns dictionary with configuration parameters:
    'one_auth_file' - Opennebula sessions credential file path,
    'key_file' - AES key file path,
    'workers_quantity' - ZMQ workers quantity,
    'server_ip' - IP address for ZMQ routing server binding,
    'server_port' - Port number for ZMQ routing server binding,
    'pidfile' - PID file path,
    'vm_user' - VM user name,
    'password_size' - Size password for VM users,
    'password_complexity' - Complexity password for VM users(bool),
    'loggerconf_file' - Logger configuration file path. 
    """
    
    config = configparser.ConfigParser()
    config.read(config_file)
    cinfig_dict = {'one_auth_file': config.get('auth_file','one_auth_file'),
                   'key_file': config.get('auth_file','key_file'),
                   'workers_quantity': int(config.get('zmq_workers_quantity','workers_quantity')),
                   'server_ip': config.get('ip_address_port','server_ip'),
                   'server_port': config.get('ip_address_port','server_port'),
                   'pidfile': config.get('pid_file','pidfile'),
                   'vm_user': config.get('vm_user_name','vm_user'),
                   'password_size': int(config.get('password_vm_users','password_size')),
                   'password_complexity': config.getboolean('password_vm_users','password_complexity'),
                   'loggerconf_file': config.get('logger_config_file','loggerconf_file')
                  }
    
    return cinfig_dict


def session_id_generator(size = 8):
    """
    Generating session id
    """
    
    s = "0123456789ABCDEF"
    return "".join(random.sample(s,size ))  


def worker_routine(url_worker, key, worker_number, config_params, context=None):
    """
    Worker routine
    """
    
    logger = logging.getLogger(__name__)    
    AESobj = AESCipher(key)
    CDobj = CommandsDispatcher(config_params)
    
    # Getting Opennebula sessions credential
    one_auth_file = open(config_params['one_auth_file'],"r")
    session = one_auth_file.read().replace('\n', '')
    one_auth_file.close()
    one = pyone.OneServer("http://localhost:2633/RPC2", session)
    
    context = context or zmq.Context.instance()
    # Socket to talk to dispatcher
    socket = context.socket(zmq.REP)

    socket.connect(url_worker)
    logger.info(("Worker %s started") % worker_number) 

    while True:
        json_receive  = AESobj.decrypt(socket.recv())
        session_id = session_id_generator()
        if json_receive:
            logger.info(("Worker %s received  session ID: %s") % (worker_number, session_id))         
            json_reply = json.dumps(CDobj.command_switcher(json_receive, session_id, one, config_params))
            socket.send(AESobj.encrypt(json_reply))
        else:
            logger.info(("Session ID: %s. Worker %s received a message with an unsupported encryption method. ") % (session_id, worker_number))
            json_reply = json.dumps({"error": "unsupported encryption method"})
            socket.send(json_reply)
        #send reply back to client
        #socket.send(json_reply)
        #socket.send(AESobj.encrypt(json_reply))


def main(config_params):
    """
    Routing server
    """

    # Getting AES key
    key_file = open(config_params['key_file'],"r")
    key = base64.b64decode(key_file.read())
    key_file.close() 

    # Configuration for logging
    logging.config.fileConfig(fname=config_params['loggerconf_file'], disable_existing_loggers=False)
    logger = logging.getLogger(__name__)
    logger.info("Routing server started")

    url_worker = "inproc://workers"
    url_client = "tcp://" + config_params['server_ip'] + ":" + config_params['server_port']

    # Prepare our context and sockets
    context = zmq.Context.instance()

    # Socket to talk to clients
    clients = context.socket(zmq.ROUTER)
    clients.bind(url_client)

    # Socket to talk to workers
    workers = context.socket(zmq.DEALER)
    workers.bind(url_worker)

    # Launch pool of worker threads
    for worker_number in range(config_params['workers_quantity']):
        thread = threading.Thread(target=worker_routine, args=(url_worker, key, worker_number, config_params))
        thread.start()

    zmq.proxy(clients, workers)

    # We never get here but clean up anyhow
    clients.close()
    workers.close()
    context.term()


if __name__ == "__main__":
    config_params = config_parser(args_parse().c)
    pid = str(os.getpid())
    file(config_params['pidfile'], 'w').write(pid)

    main(config_params)

  
