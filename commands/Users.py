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


class Users(object):
    

    def user_group_allocate(self, group_name, one, config_params):
        """
        Allocate users group with name like user name.
        """
        
        try:
            group_id = one.group.allocate(group_name)
        except Exception as e:
            return {"error": str(e)}
            
        return group_id    
     
     
    def user_allocate(self, json_dict, one, config_params):
        """
        Allocate user.
        """
        
        if not (json_dict.get('user_name') is None):
            user_name = json_dict['user_name']
        else:
            return {"error": "not set user name"}
        if not (json_dict.get('user_password') is None):
            user_password = json_dict['user_password']
        else:
            return {"error": "not set user password"}
        if (json_dict.get('user_group_id_array')):
            user_group_id_array = json_dict.get('user_group_id_array')
        else:    
            user_group_id_array = []
            try:
                user_group_id_array.append(one.group.allocate(user_name))
            except Exception as e:
                return {"error": str(e)}        
       
        try:
            return_message = {
                    "user_name": user_name,
                    "user_id": one.user.allocate(user_name, user_password, '', user_group_id_array),
                    "user_group_id_array": user_group_id_array
                }
        except Exception as e:
            return {"error": str(e)}        
        
        return return_message


    def user_delete(self, json_dict, one, config_params):
        """    
        Deletes the given user from the pool.
        """
        
        if not (json_dict.get('user_id') is None):
            user_id = json_dict['user_id']
            if type(user_id) is not int:
                return {"error": "Parameter user_id that is supposed to be integer is not"}            
        else:
            return {"error": "not set user id"}
            
        try:
            vms_used = one.user.info(user_id).VM_QUOTA.VM.VMS_USED
            if vms_used == "0":
                try:
                    user_group_id = one.user.info(user_id).GID
                    one.user.delete(user_id)           
                    if one.group.info(user_group_id).USERS.ID:
                        return {"action": "user deleted", "user_id": user_id}
                    else:
                        one.group.delete(user_group_id)
                        return {"action": "user and user group deleted", "user_id": user_id, "user_group_id": user_group_id}
                except Exception as e:
                    return {"error": str(e)}              
        except Exception as e:
            if "[one.user.info]" in str(e):
                return {"error": str(e)}
            try:
                user_group_id = one.user.info(user_id).GID
                one.user.delete(user_id)           
                if one.group.info(user_group_id).USERS.ID:
                    return {"action": "user deleted", "user_id": user_id}
                else:
                    one.group.delete(user_group_id)
                    return {"action": "user and user group deleted", "user_id": user_id, "user_group_id": user_group_id}
            except Exception as e:
                return {"error": str(e)}              
                    
        return {"error": "vm allocated", "user_id": user_id, "vms_used": vms_used}                 


    def get_user_info(self, json_dict, one, config_params):
        """
        Getting information about user by ID
        """
        
        if not (json_dict.get('user_id') is None):
            user_id = json_dict['user_id']
            if type(user_id) is not int:
                return {"error": "Parameter user_id that is supposed to be integer is not"}            
        else:
            return {"error": "not set user id"} 

        try:
            vms = one.user.info(user_id).VM_QUOTA.VM.VMS
            vms_used = one.user.info(user_id).VM_QUOTA.VM.VMS_USED
            running_vms_used = one.user.info(user_id).VM_QUOTA.VM.RUNNING_VMS_USED
        except Exception as e:
            return {"error": str(e)}

        return {"user_id": user_id, "vms": vms, "vms_used": vms_used,"running_vms_used": running_vms_used}
