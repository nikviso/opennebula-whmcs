import pyone
import base64

"""
Create Opennebula connection
"""
one_auth_file = open("/var/lib/one/.one/one_auth","r")
session = one_auth_file.read().replace('\n', '')
one_auth_file.close()
one = pyone.OneServer("http://localhost:2633/RPC2", session)

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
server_ip = "10.3.3.3"
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