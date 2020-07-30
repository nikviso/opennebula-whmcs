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
from config import *
from security.aes_cbc import *
from commands.commands import *

def session_id_generator(size = 8):
    """
    Generating session id for writing to log file
    """
    s = "0123456789ABCDEF"
    return "".join(random.sample(s,size ))  
  
def worker_routine(worker_url, key, worker_number, context=None):
    """
    Worker routine
    """
    logger = logging.getLogger(__name__)    
    AESobj = AESCipher(key)
    one = pyone.OneServer("http://localhost:2633/RPC2", session)
    
    context = context or zmq.Context.instance()
    # Socket to talk to dispatcher
    socket = context.socket(zmq.REP)

    socket.connect(worker_url)
    logger.info(("Worker %s started") % worker_number) 

    while True:

#        json_receive  = socket.recv()
        json_receive  = AESobj.decrypt(socket.recv())
        session_id = session_id_generator()
        logger.info(("Worker %s received  session ID: %s") % (worker_number, session_id))         
       
        json_reply = json.dumps(command_switcher(json_receive, session_id, one))
        
        #send reply back to client
#        socket.send(json_reply)
        socket.send(AESobj.encrypt(json_reply))
       
            

def main(workers_quantity, server_ip, server_port, key):
    """
    Routing server
    """
    #Configuration for logging
    logging.config.fileConfig(fname=loggerconf_file, disable_existing_loggers=False)
    logger = logging.getLogger(__name__)
    logger.info("Routing server started")

    url_worker = "inproc://workers"
    url_client = "tcp://" + server_ip + ":" + server_port

    # Prepare our context and sockets
    context = zmq.Context.instance()

    # Socket to talk to clients
    clients = context.socket(zmq.ROUTER)
    clients.bind(url_client)

    # Socket to talk to workers
    workers = context.socket(zmq.DEALER)
    workers.bind(url_worker)

    # Launch pool of worker threads
    for worker_number in range(workers_quantity):
        thread = threading.Thread(target=worker_routine, args=(url_worker, key, worker_number))
        thread.start()

    zmq.proxy(clients, workers)

    # We never get here but clean up anyhow
    clients.close()
    workers.close()
    context.term()

if __name__ == "__main__":
    pid = str(os.getpid())
    file(pidfile, 'w').write(pid)

    main(workers_quantity, server_ip, server_port, key)
  
