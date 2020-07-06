#!/usr/bin/env python
# -*- coding: utf-8 -*-
#

import sys
import os
import time
import threading
import zmq
import json
from config import *
from security.aes_cbc import *
from commands.commands import *

def worker_routine(worker_url, key, context=None):
    """
    Worker routine
    """

    AESobj = AESCipher(key)
    
    context = context or zmq.Context.instance()
    # Socket to talk to dispatcher
    socket = context.socket(zmq.REP)

    socket.connect(worker_url)

    while True:

#        json_recive  = socket.recv()
        json_recive  = AESobj.decrypt(socket.recv())
            
        print("Received request: [ %s ]" % (json_recive))

        #time.sleep(1)
        
        json_reply = json.dumps(command_switcher(json_recive))
        
        #send reply back to client
#        socket.send(json_reply)
        socket.send(AESobj.encrypt(json_reply))
        
            

def main(workers_quantity, server_ip, server_port, key):
    """
    Server routine
    """

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
    for i in range(workers_quantity):
        thread = threading.Thread(target=worker_routine, args=(url_worker, key,))
        thread.start()

    zmq.proxy(clients, workers)

    # We never get here but clean up anyhow
    clients.close()
    workers.close()
    context.term()

if __name__ == "__main__":
    pid = str(os.getpid())
    file(pidfile, 'w').write(pid)

#    print json.dumps(command_switcher('{"cmd": "get_all_vm_state", "user_id": 4, "vm_id": 33}'))
    main(workers_quantity, server_ip, server_port, key)
  
