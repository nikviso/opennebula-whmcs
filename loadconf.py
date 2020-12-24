#!/usr/bin/env python
# -*- coding: utf-8 -*-
#
import configparser

def config_parser(config_file='config.ini'):
    config = configparser.ConfigParser()
    config.read(config_file)
    cinfig_dict = {'one_auth_file': config.get('auth_file','one_auth_file'),
                   'key_file': config.get('auth_file','key_file'),
                   'workers_quantity': int(config.get('zmq_workers_quantity','workers_quantity')),
                   'server_ip': config.get('ip_address_port','server_ip'),
                   'server_port': config.get('ip_address_port','server_port'),
                   'pidfile': config.get('pid_file','pidfile'),
                   'vm_user': config.get('vm_user_name','vm_user'),
                   'password_size': config.get('password_vm_users','password_size'),
                   'password_complexity': config.getboolean('password_vm_users','password_complexity'),
                   'loggerconf_file': config.get('logger_config_file','loggerconf_file')
                  }
    
    return cinfig_dict

if __name__ == "__main__":
    print config_parser()