import base64

"""
Getting Opennebula sessions credential
"""
one_auth_file = open("/var/lib/one/.one/one_auth","r")
session = one_auth_file.read().replace('\n', '')
one_auth_file.close()

"""
Getting AES key
"""
key_file = open("/var/lib/one/.one/key_aes","r")
key = base64.b64decode(key_file.read())
key_file.close() 

"""
ZMQ workers quantity
"""
workers_quantity = 5

"""
IP address and port number for ZMQ routing server binding
"""
server_ip = "*"
server_port = "5555"

"""
PID file path
"""
pidfile = "/var/run/mtszmq.pid"

"""
VM user name
"""
vm_user = "debian"

"""
Size and complexity password for VM users
"""
password_size = 6
password_complexity = 0

"""
Logger configuration file path 
"""
loggerconf_file = '/root/zmq-one-rpc-xml/logger.conf'
