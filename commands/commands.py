import json
from config import *

def command_switcher(json_message):
    """
    Get command from JSON message. Selecting by command and execute function.  
    """
    
    try:
        json_dict = json.loads(json_message)
        cmd =  json_dict['cmd']
        switcher = {
            'get_vm_state': get_vm_state,
            'get_all_vm_state': get_all_vm_state,
            'get_user_info': get_user_info,
            'user_allocate': user_allocate,
        }
        # Get the function from switcher dictionary
        cmd_execute = switcher.get(cmd, lambda null_argument: {"error": "invalid command"})
        # Execute the function
        return cmd_execute(json_message)
    except ValueError:
        return {"error": "string could not be converted to json"}
        
def get_user_id(user_name):
    """
    Get USER ID by USER NAME. (Asymptotic O(n), FIXME!!!!)
    """
    userpoolInfo = one.userpool.info()
    userlist = userpoolInfo.get_USER()
    for user in userlist:
        if user.NAME == user_name:
            user_id = user.ID
#            print(user.ID, user.NAME)
            break
    return user_id

def switch_vm_state(state):
    """
    VM STATE:
    0	INIT                init    Internal initialization state right after VM creation, this state is not visible for the end users. 
                                    And hence this state cannot be used to hook any action.
    1	PENDING             pend    By default a VM starts in the pending state, waiting for a resource to run on. 
                                    It will stay in this state until the scheduler decides to deploy it, or the user deploys it using the onevm deploy command.
    2	HOLD                hold    The owner has held the VM and it will not be scheduled until it is released. It can be, however, deployed manually.
    3	ACTIVE              init    Internal initialization state, not visible for the end users
    4	STOPPED             stop    The VM is stopped. VM state has been saved and it has been transferred back along with the disk images to the system datastore
    5	SUSPENDED           susp    Same as stopped, but the files are left in the host to later resume the VM there (i.e. there is no need to re-schedule the VM)
    6	DONE                done    The VM is done. VMs in this state wont be shown with onevm list but are kept in the database for accounting purposes.
                                    You can still get their information with the onevm show command
    8	POWEROFF            poff    Same as suspended, but no checkpoint file is generated. 
                                    Note that the files are left in the host to later boot the VM there. When the VM guest is shutdown, OpenNebula will put the VM in this state
    9	UNDEPLOYED          unde    The VM is shut down. Similar to STOPPED, but no checkpoint file is generated. 
                                    The VM disks are transfered to the system datastore. The VM can be resumed later
    10	CLONING             clon    The VM is waiting for one or more disk images to finish the initial copy to the repository (image state still in lock)
    11	CLONING_FAILURE     fail    Failure during a CLONING. One or more of the images went into the ERROR state
    """
    switcher = {
        0: "INIT",
        1: "PENDING",
        2: "HOLD",
        3: "ACTIVE",
        4: "STOPPED",
        5: "SUSPENDED",
        6: "DONE",
        8: "POWEROFF",
        9: "UNDEPLOYED",
        10: "CLONING",
        11: "CLONING_FAILURE"
    }
    return switcher.get(state, "Invalid STATE")
    
def get_vm_state(json_message):
    """
    Get state of one VM of user by VM NAME or VM ID.
    """
    
    json_dict = json.loads(json_message)
    if not (json_dict.get('user_name') is None):
        user_name = json_dict['user_name']
    else:
        user_name = ''
    if not (json_dict.get('user_id') is None):
        user_id =  json_dict['user_id']
    else:
        user_id = ''
    if not (json_dict.get('vm_name') is None):
        vm_name = json_dict['vm_name']
    else:
        vm_name = ''
    if not (json_dict.get('vm_id') is None):
        vm_id = json_dict['vm_id']
    else:
        vm_id = ''
        
    if vm_name and not vm_id:
        if user_name and not user_id:
            try:
                vm_state = one.vm.info(one.vmpool.info(get_user_id(user_name),-1,-1,-1,vm_name).VM[0].get_ID()).STATE
            except IndexError:
                return {"error": "list index out of range"}
        elif user_id and not user_name:
            try:
                vm_state = one.vm.info(one.vmpool.info(user_id,-1,-1,-1,vm_name).VM[0].get_ID()).STATE
            except IndexError:
                return {"error": "list index out of range"}     
        else:
            return {"error": "error user name or user id"}
    elif vm_id and not vm_name:
        if user_name and not user_id:
            try:
        #        vm_state = one.vm.info(vm_id).STATE
                vm_state = one.vm.info(one.vmpool.info(get_user_id(user_name),vm_id,vm_id,-1).VM[0].get_ID()).STATE
            except IndexError:
                return {"error": "list index out of range"}
        elif user_id and not user_name:        
            try:
        #        vm_state = one.vm.info(vm_id).STATE
                vm_state = one.vm.info(one.vmpool.info(user_id,vm_id,vm_id,-1).VM[0].get_ID()).STATE
            except IndexError:
                return {"error": "list index out of range"}
        else:
            return {"error": "error user name or user id"}        
    else:
        return {"error": "error vm id or vm name"}
    

    if vm_name and not vm_id:
        return_message =  {
            "vm_name": vm_name,
            "vm_state": switch_vm_state(vm_state)
        }
    elif vm_id and not vm_name:
        return_message =  {
            "vm_id": vm_id,
            "vm_state": switch_vm_state(vm_state)
        }
    else:
        return_message = {"error": "error vm state return"}
        
    return return_message

def get_all_vm_state(json_message):
    """
    Get state all VMs of user by USERNAME or USER ID.
    """
    
    json_dict = json.loads(json_message)
    if not (json_dict.get('user_name') is None):
        user_name = json_dict['user_name']
    else:
        user_name = ''
    if not (json_dict.get('user_id') is None):
        user_id = json_dict['user_id']
    else:
        user_id = '' 
    
    if user_name and not user_id:
        try:
            vmpoolInfo = one.vmpool.info(get_user_id(user_name), -1, -1, -1)
            vmlist = vmpoolInfo.get_VM()
        except IndexError:
            return {"error": "list index out of range"}
    elif user_id and not user_name:        
        try:
            vmpoolInfo = one.vmpool.info(user_id, -1, -1, -1)
            vmlist = vmpoolInfo.get_VM()
        except IndexError:
            return {"error": "list index out of range"}
    else:
        return {"error": "error user name or user id"}        

    return_message=[]
    for vm in vmlist:
        return_message.append({
            "vm_id": vm.ID,
            "vm_name": vm.NAME,
            "vm_state": switch_vm_state(vm.STATE)
        },)
    return_message = '{"vms_state": ' + json.dumps(return_message) + '}'

    return json.loads(return_message)    

def get_user_info(json_message):
    return "NOT DEFINED"
    
def user_allocate(json_message):
    """
    Allocate user.
    """
    user_group_id_array = [1]
    json_dict = json.loads(json_message)
    if not (json_dict.get('user_name') is None):
        user_name = json_dict['user_name']
    else:
        return {"error": "not set user name"}
    if not (json_dict.get('user_password') is None):
        user_password = json_dict['user_password']
    else:
        return {"error": "not set user password"} 
    
    return one.user.allocate(user_name, user_password, '', user_group_id_array)
