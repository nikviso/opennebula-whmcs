import json
import logging
import copy

class CommandsDispatcher()
    """
    Commands dispatcher class
    """

    def __init__(self):
    """
    Configuration for logging
    """
    """
    class CustomFilter(logging.Filter):
        def filter(self, record):
            if 'commands.commands' in getattr(record, 'name', ''):
                return True
            return False
    """
    logger = logging.getLogger(__name__)
    """
    f = CustomFilter()
    logger.addFilter(f)
    """

    def command_switcher(self,json_message, session_id, one, config_params):
        """
        Getting command from JSON message. Selecting by command and execute function.  
        """
        try:
            json_dict = json.loads(json_message)
            logging_local("Received request" , json_dict, session_id)
            cmd =  json_dict['cmd']
            switcher = {
                'get_vm_state': get_vm_state,
                'get_all_vm_state': get_all_vm_state,
                'get_user_info': get_user_info,
                'user_allocate': user_allocate,
                'user_delete': user_delete,
                'template_instantiate': template_instantiate,
                #'template_instantiate_user': template_instantiate_user,
                'vm_terminate': vm_terminate,
                'vm_action': vm_action,
            }
            # Get the function from switcher dictionary
            cmd_execute = switcher.get(cmd, lambda null_argument: {"error": "invalid command"})
            # Execute the function
            json_reply = cmd_execute(json_dict, one, config_params)
            logging_local("Sended reply" , json_reply, session_id)
            return json_reply
        except ValueError:
            logging_local("Sended reply" , {"error": "string could not be converted to json"}, session_id)
            return {"error": "string could not be converted to json"}

    @staticmethod
    def logging_local(sendreceive, in_message, session_id):
        out_message = copy.copy(in_message)
        if 'error' in out_message:
            logger.error("Session ID: %s, %s: %s" % (session_id, sendreceive, out_message))
        else:    
            if 'user_password' in out_message:
                out_message['user_password'] = u'*******'
            if 'vm_user_password' in out_message or 'vm_root_password' in out_message:
                out_message['vm_user_password'] = u'*******'
                out_message['vm_root_password'] = u'*******'            
            logger.info("Session ID: %s, %s: %s" % (session_id, sendreceive, out_message))

 