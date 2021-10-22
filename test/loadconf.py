#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Copyright 2021 the original author or authors.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
#

import configparser
import argparse

def args_parse():
    parser = argparse.ArgumentParser()
    parser.add_argument('-c', default='config.ini')
    args = parser.parse_args()
    
    return args
    
    
def config_parser(config_file):
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
    print config_parser(args_parse().c)