#
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

import json
import logging
import copy
from VM import *
from Users import *

class CommandsDispatcher(object):
    """
    Commands dispatcher class
    """
    
    def __init__(self, config_params):
        self.logger = logging.getLogger(__name__)


    def command_switcher(self, json_message, session_id, one, config_params):
        """
        Getting command from JSON message. Selecting by command and execute function.  
        """
        
        UsersObj = Users()
        VMobj = VM()
        
        try:
            json_dict = json.loads(json_message)
            if not json_dict:
                self.logger.info("Session ID: %s, error: string could not be converted to json" %session_id)
                return {"error": "string could not be converted to json"}
            self.logging_local("Received request" , json_dict, session_id)
            cmd =  json_dict['cmd']
            switcher = {
                'user_allocate': UsersObj.user_allocate,
                'user_delete': UsersObj.user_delete,
                'get_user_info': UsersObj.get_user_info,
                'get_all_vm_state': VMobj.get_all_vm_state,
                'get_vm_state': VMobj.get_vm_state,
                'template_instantiate': VMobj.template_instantiate,
                # 'template_instantiate_user': VMobj.template_instantiate_user,
                'vm_terminate': VMobj.vm_terminate,
                'vm_action': VMobj.vm_action,
            }
            # Get the function from switcher dictionary
            cmd_execute = switcher.get(cmd, lambda null_arg0,null_arg1,null_arg2: {"error": "invalid command"})
            # Execute the function
            json_reply = cmd_execute(json_dict, one, config_params)
            self.logging_local("Sended reply" , json_reply, session_id)
            return json_reply
        except ValueError:
            self.logging_local("Sended reply" , {"error": "string could not be converted to json"}, session_id)
            return {"error": "string could not be converted to json"}


    def logging_local(self, sendreceive, in_message, session_id, ):
        """
        Logging messages
        """

        out_message = copy.copy(in_message)
        if 'error' in out_message:
            self.logger.error("Session ID: %s, %s: %s" % (session_id, sendreceive, out_message))
        else:    
            if 'user_password' in out_message:
                out_message['user_password'] = u'*******'
            if 'vm_user_password' in out_message or 'vm_root_password' in out_message:
                out_message['vm_user_password'] = u'*******'
                out_message['vm_root_password'] = u'*******'            
            self.logger.info("Session ID: %s, %s: %s" % (session_id, sendreceive, out_message))
            

 