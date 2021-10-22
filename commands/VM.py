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

import random
import time
import base64
import json

class VM(object):

    @staticmethod
    def password_generator(size = 16, complexity = True):
        """
        VM user password generator.
        """    
        if not complexity:
            s = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"
        else:
            s = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()?{}[]\/<>.,~"
        return "".join(random.sample(s,size ))


    @staticmethod
    def user_generator():
        """
        VM user name generator.
        """    
        s = "abcdefghijklmnopqrstuvwxyz"
        digits = "0123456789"
        return "".join(random.sample(s,5 ) + random.sample(digits,2 ))


    def template_instantiate(self, json_dict, one, config_params):
        """
        Instantiates a new virtual machine from a template.
        """

        if not (json_dict.get('vm_name') is None):
            vm_name = json_dict['vm_name']
            try:
                one.vmpool.info(-2,-1,-1,-1,vm_name).VM[0]
                return {"error": "vm with this name already allocated"}
            except IndexError:
                pass
        else:
            return {"error": "not set vm name"} 
            
        if not (json_dict.get('user_id') is None):
            user_id = json_dict['user_id']
            if type(user_id) is not int:
                return {"error": "Parameter user_id that is supposed to be integer is not"}
            try:
                vms = one.user.info(user_id).NAME
            except Exception as e:
                return {"error": str(e)}    
        else:
            return {"error": "not set user id"}
            
        if not (json_dict.get('template_id') is None):
            template_id = json_dict['template_id']
            if type(template_id) is not int:
                return {"error": "Parameter template_id that is supposed to be integer is not"}        
        else:
            return {"error": "not set template id"}
            
        if not (json_dict.get('ip_address') is None):
            ip_address = json_dict['ip_address']
        else:
            return {"error": "not set ip address"}
            
        if not (json_dict.get('network_mask') is None):
            network_mask = json_dict['network_mask']
        else:
            return {"error": "not set network mask"}  
            
        if not (json_dict.get('dns_ip_address') is None):
            dns_ip_address = json_dict['dns_ip_address']
        else:
            return {"error": "not set dns ip address"} 
            
        if not (json_dict.get('gw_ip_address') is None):
            gw_ip_address = json_dict['gw_ip_address']
        else:
            return {"error": "not set gateway ip address"}
            
        if not (json_dict.get('network_id') is None):
            network_id = json_dict['network_id']
            if type(network_id) is not int:
                return {"error": "Parameter network_id that is supposed to be integer is not"}        
        else:
            return {"error": "not set network id"}
            
        if not (json_dict.get('network_address') is None):
            network_address = json_dict['network_address']
        else:
            return {"error": "not set network address"}   
        
        if config_params['vm_user'] == '':
            vm_user = self.user_generator()
        else:
            vm_user = config_params['vm_user']
        
        try: 
            password_size = config_params['password_size']
            password_complexity = config_params['password_complexity']
            vm_root_password = self.password_generator(password_size, password_complexity) # Generating password for root user.
            vm_user_password = self.password_generator(password_size, password_complexity) # Generating password for a simple user.
        except Exception as e:
            return {"error": str(e)}
            
        # Instantiate a new VM from a tempate
        try:    
            vm_id = one.template.instantiate(template_id, vm_name, False, 
            {
            'TEMPLATE':{
            'CONTEXT':{
              'SSH_PUBLIC_KEY': '',
              'NETWORK': "YES",
            #  'START_SCRIPT_BASE64': base64.b64encode('echo -e "' + vm_root_password + '\n' + vm_root_password + '" | passwd root; echo -e "'
            #                         + vm_user_password + '\n' + vm_user_password + '" | passwd ' + vm_user),
               'START_SCRIPT_BASE64': base64.b64encode(self.create_start_script(vm_root_password, vm_user_password, vm_user))
            },
            'NIC': {
              'IP': ip_address,
              'DNS': dns_ip_address,
              'GATEWAY': gw_ip_address,
              'NETWORK_ID': network_id,
              'NETWORK_ADDRESS': network_address,
              'NETWORK_MASK': network_mask          
            }
            }}, 
            True)   
        except Exception as e:
            self.template_terminate(vm_name, one, True) 
            return {"error": str(e)}

        #Removing VM template.
        self.template_terminate(vm_name, one, False)
            
        #Changing VM owner.
        try:
            one.vm.chown(vm_id, user_id, one.user.info(user_id).GID)
        except Exception as e:
            return {"error": str(e)}
        
        #Getting information about VM disks
        template_disk_info = one.vm.info(vm_id).TEMPLATE["DISK"]
        
        #Changing images owner.
        try:
            if type(template_disk_info) is list:
                #If the disk is not one
                i = 0
                while i < len(template_disk_info):
                    one.image.chown(int(template_disk_info[i]["IMAGE_ID"]),user_id, one.user.info(user_id).GID)
                    i += 1
            else:
                #If the disk is one        
                one.image.chown(int(template_disk_info["IMAGE_ID"]),user_id, one.user.info(user_id).GID)
        except Exception as e:
            return {"error": str(e)}
            
        return_message = {
                "user_id": user_id,
                "vm_id": vm_id,
                "vm_root_password": vm_root_password,
                "vm_user": vm_user,
                "vm_user_password": vm_user_password,
            }
        
        return return_message


    def template_instantiate_user(self, json_dict, one, config_params):
        """
        Instantiates a new virtual machine from a template.
        """
        
        if not (json_dict.get('vm_name') is None):
            vm_name = json_dict['vm_name']
            try:
                one.vmpool.info(-2,-1,-1,-1,vm_name).VM[0]
                return {"error": "vm with this name already allocated"}
            except IndexError:
                pass        
        else:
            return {"error": "not set vm name"}
            
        if not (json_dict.get('user_id') is None):
            user_id = json_dict['user_id']
            if type(user_id) is not int:
                return {"error": "Parameter user_id that is supposed to be integer is not"}
            try:
                vms = one.user.info(user_id).NAME
            except Exception as e:
                return {"error": str(e)}  
        else:
            return {"error": "not set user id"}
            
        if not (json_dict.get('user_name') is None):
            user_name = json_dict['user_name']
        else:
            return {"error": "not set user name"}
            
        if not (json_dict.get('user_password') is None):
            user_password = json_dict['user_password']
        else:
            return {"error": "not set user password"} 
            
        if not (json_dict.get('template_id') is None):
            template_id = json_dict['template_id']
            if type(template_id) is not int:
                return {"error": "Parameter template_id that is supposed to be integer is not"}            
        else:
            return {"error": "not set template id"}
            
        if not (json_dict.get('ip_address') is None):
            ip_address = json_dict['ip_address']
        else:
            return {"error": "not set ip address"}
            
        if not (json_dict.get('dns_ip_address') is None):
            dns_ip_address = json_dict['dns_ip_address']
        else:
            return {"error": "not set dns ip address"}
        
        if not (json_dict.get('network_mask') is None):
            network_mask = json_dict['network_mask']
        else:
            return {"error": "not set network mask"}
            
        if not (json_dict.get('gw_ip_address') is None):
            gw_ip_address = json_dict['gw_ip_address']
        else:
            return {"error": "not set gateway ip address"}
            
        if not (json_dict.get('network_id') is None):
            network_id = json_dict['network_id']
            if type(network_id) is not int:
                return {"error": "Parameter network_id that is supposed to be integer is not"}           
        else:
            return {"error": "not set network id"}
            
        if not (json_dict.get('network_address') is None):
            network_address = json_dict['network_address']
        else:
            return {"error": "not set network address"}   
        
        if config_params['vm_user'] == '':
            vm_user = self.user_generator()
        else:
            vm_user = config_params['vm_user']
        
        session=user_name+":"+user_password
        one_user = pyone.OneServer("http://localhost:2633/RPC2", session)
        
        try: 
            password_size = config_params['password_size']
            password_complexity = config_params['password_complexity']
            vm_root_password = self.password_generator(password_size, password_complexity) # Generating password for root user.
            vm_user_password = self.password_generator(password_size, password_complexity) # Generating password for a simple user.
        except Exception as e:
            return {"error": str(e)}

        # Instantiate a new VM from a tempate
        try:    
            vm_id = one_user.template.instantiate(template_id, vm_name, False, 
            {
                'TEMPLATE':{
                    'CONTEXT':{
                      'SSH_PUBLIC_KEY': '',
                      'NETWORK': "YES",
        #              'START_SCRIPT_BASE64': base64.b64encode('echo -e "' + vm_root_password + '\n' + vm_root_password + '" | passwd root; echo -e "'
        #                                     + vm_user_password + '\n' + vm_user_password + '" | passwd ' + vm_user),
                       'START_SCRIPT_BASE64': base64.b64encode(self.create_start_script(vm_root_password,vm_user_password,vm_user)) 
                    },
                    'NIC': {
                      'IP': ip_address,
                      'DNS': dns_ip_address,
                      'GATEWAY': gw_ip_address,
                      'NETWORK_ID': "0",
                      'NETWORK_ADDRESS': network_address,
                      'NETWORK_MASK': network_mask          
                    }
                }
            }, 
            True)
        except Exception as e:
            self.template_terminate(vm_name, one, True)
            return {"error": str(e)}
        
        #Removing VM template.
        self.template_terminate(vm_name, one, False)
        
        return_message = {
                "user_id": user_id,    
                "vm_id": vm_id,
                "vm_root_password": vm_root_password,
                "vm_user": config_params['vm_user'],
                "vm_user_password": vm_user_password,
            }

        return return_message


    def vm_terminate(self, json_dict, one, config_params):
        """
        Terminate VM
        """
        
        if not (json_dict.get('vm_id') is None):
            vm_id = json_dict['vm_id']
            if type(vm_id) is not int:
                return {"error": "Parameter vm_id that is supposed to be integer is not"}        
        else:
            return {"error": "not set vm id"}
        if not (json_dict.get('user_id') is None):
            user_id = json_dict['user_id']
            if type(user_id) is not int:
                return {"error": "Parameter user_id that is supposed to be integer is not"}          
        else:
            return {"error": "not set user id"}    
        
        #Checking VM availability  
        try:
            one.vmpool.info(user_id,vm_id,vm_id,-1).VM[0]
        except IndexError:
            return {"error": "list vm index out of range"}

        #Getting information about VM disks
        template_disk_info = one.vm.info(vm_id).TEMPLATE["DISK"]
        
        #Terminating VM
        try:
            one.vm.action("terminate-hard", vm_id)
        except Exception as e:
            return {"error": str(e)}
            
        #Deleting disks images of VM
        try:
            if type(template_disk_info) is list:
                #If the disk is not one
                i = 0
                while one.image.info(int(template_disk_info[i]["IMAGE_ID"])).STATE <> 1:
                    time.sleep(1)
                while i < len(template_disk_info):
                    one.image.delete(int(template_disk_info[i]["IMAGE_ID"]))
                    i += 1
            else:
                #If the disk is one        
                while one.image.info(int(template_disk_info["IMAGE_ID"])).STATE <> 1:
                    time.sleep(1)        
                one.image.delete(int(template_disk_info["IMAGE_ID"]))
        except Exception as e:
            return {"error": str(e)}
            
        return {"action": "vm terminated"}


    def vm_action(self, json_dict, one, config_params):
        """
        VM action:
        poweroff-hard
        poweroff
        reboot-hard
        reboot
        resume
        """
        
        if not (json_dict.get('vm_id') is None):
            vm_id = json_dict['vm_id']
            if type(vm_id) is not int:
                return {"error": "Parameter vm_id that is supposed to be integer is not"}               
        else:
            return {"error": "not set vm id"}
        if not (json_dict.get('user_id') is None):
            user_id = json_dict['user_id']
            if type(user_id) is not int:
                return {"error": "Parameter user_id that is supposed to be integer is not"}               
        else:
            return {"error": "not set user id"}
        if not (json_dict.get('action') is None):
            action = json_dict['action']
            if action in ("poweroff-hard", "poweroff", "reboot-hard", "reboot", "resume"):
               pass
            else:
               return {"error": "vm action not available"}  
        else:
            return {"error": "not set vm action"}           
        
        #Checking VM availability  
        try:
            one.vmpool.info(user_id,vm_id,vm_id,-1).VM[0]
        except IndexError:
            return {"error": "list vm index out of range"}
        
        
        try:
            one.vm.action(action, vm_id)
        except Exception as e:
            return {"error": str(e)}
            
        
        return {"vm_action": action, "vm_id": vm_id,}

    @staticmethod   
    def get_template_id(template_name, one):
        """
        Getting TEMPLATE ID by TEMPLATE NAME.
        """
        
        templatepoolInfo = one.templatepool.info(-2, -1, -1)
        templatelist = templatepoolInfo.get_VMTEMPLATE()
        for template in templatelist:
            if template.NAME == template_name:
                return template.ID


    def template_terminate(self, template_name, one, image_remove = False):
        """
        Terminating VM template
        """
        
        template_id = self.get_template_id(template_name, one)
        if template_id:
            try:
                return one.template.delete(template_id, image_remove)
            except Exception as e:
                return {"error": str(e)}  
        else:
            return {"error": "not found template name"}

    @staticmethod
    def get_user_id(user_name, one):
        """
        Getting USER ID by USER NAME.
        """
        
        user_id = ''
        userpoolInfo = one.userpool.info()
        userlist = userpoolInfo.get_USER()
        for user in userlist:
            if user.NAME == user_name:
                user_id = user.ID
                #print(user.ID, user.NAME)
                break
        if  user_id:
            return {"user_id": user_id,}
        else:
            return {"error": "error user name",}        


    @staticmethod
    def switch_vm_state(state):
        """
        See http://docs.opennebula.io/5.10/operation/references/vm_states.html#list-of-states
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


    def get_vm_state(self, json_dict, one, config_params):
        """
        Getting state of one VM of user by VM NAME or VM ID.
        """
        
        if not (json_dict.get('user_id') is None):
            user_id = json_dict['user_id']
            if type(user_id) is not int:
                return {"error": "Parameter user_id that is supposed to be integer is not"}          
        elif not (json_dict.get('user_name') is None):
            username_to_userid = self.get_user_id(json_dict['user_name'], one)
            if username_to_userid.get('error'):
                return username_to_userid
            else:
                user_id = username_to_userid['user_id']
        else:
            return {"error": "error user name or user id"}        
        
        if not (json_dict.get('vm_name') is None):
            vm_name = json_dict['vm_name']
        else:
            vm_name = ''
        if not (json_dict.get('vm_id') is None):
            vm_id = json_dict['vm_id']
            if type(vm_id) is not int:
                return {"error": "Parameter vm_id that is supposed to be integer is not"}          
        else:
            vm_id = ''
            
        if vm_name and not vm_id:
            try:
                vm_state = one.vm.info(one.vmpool.info(user_id,-1,-1,-1,vm_name).VM[0].get_ID()).STATE
            except IndexError:
                return {"error": "list index out of range"} 
        elif vm_id and not vm_name:
            try:
        #        vm_state = one.vm.info(vm_id).STATE
                vm_state = one.vm.info(one.vmpool.info(user_id,vm_id,vm_id,-1).VM[0].get_ID()).STATE
            except IndexError:
                return {"error": "list index out of range"}   
        else:
            return {"error": "error vm id or vm name"}
        

        if vm_name and not vm_id:
            return_message =  {
                "vm_name": vm_name,
                "vm_state": self.switch_vm_state(vm_state)
            }
        elif vm_id and not vm_name:
            return_message =  {
                "vm_id": vm_id,
                "vm_state": self.switch_vm_state(vm_state),
            }
        else:
            return_message = {"error": "error vm state return"}
        
        return return_message


    def get_all_vm_state(self, json_dict, one, config_params):
        """
        Getting state all VMs of user by USERNAME or USER ID.
        """
        
        if not (json_dict.get('user_id') is None):
            user_id = json_dict['user_id']
            if type(user_id) is not int:
                return {"error": "Parameter user_id that is supposed to be integer is not"}          
        elif not (json_dict.get('user_name') is None):
            username_to_userid = self.get_user_id(json_dict['user_name'], one)
            if username_to_userid.get('error'):
                return username_to_userid
            else:
                user_id = username_to_userid['user_id']
        else:
            return {"error": "error user name or user id"}

   
        try:
            vmpoolInfo = one.vmpool.info(user_id, -1, -1, -1)
            vmlist = vmpoolInfo.get_VM()
        except IndexError:
            return {"error": "list index out of range"}
        
        return_message = []
        for vm in vmlist:
            return_message.append({
                "vm_id": vm.ID,
                "vm_name": vm.NAME,
                "vm_state": self.switch_vm_state(vm.STATE),
            },)
        return_message = '{"vms_state": ' + json.dumps(return_message) + '}'
        
        return json.loads(return_message)


    @staticmethod
    def create_start_script(vm_root_password, vm_user_password, vm_user):
        """
        """
        
        script = '#!/bin/sh\n\
if [ -e /etc/wdont ];\n\
then\n\
rm -r /var/run/one-context/context*;\n\
rm /var/run/one-context/one_env;\n\
exit;\n\
else\n\
echo \"1\">/etc/wdont;\n\
chmod 644 /etc/wdont;\n\
fi\n'\
        'vm_root_password="' + vm_root_password + '"\n' + \
        'vm_user_password="' + vm_user_password + '"\n' + \
        'vm_user="' + vm_user + '"\n' + \
        'OS="`uname`"\n\
case $OS in\n\
\'Linux\')\n\
check=`id $vm_user`\n\
if ! [ "$check" ];\n\
then\n\
useradd -m -s /bin/sh $vm_user\n\
fi\n\
echo $vm_root_password\'\\n\'$vm_root_password | passwd root;\n\
echo $vm_user_password\'\\n\'$vm_user_password | passwd $vm_user;\n\
;;\n\
\'FreeBSD\')\n\
check=`pw usershow $vm_user`\n\
if ! [ "$check" ];\n\
then\n\
pw user add -n $vm_user -G wheel;\n\
fi\n\
echo $vm_root_password | pw mod user root -h 0;\n\
echo $vm_user_password | pw mod user $vm_user -h 0;\n\
;;\n\
*) ;;\n\
esac\n\
rm -r /var/run/one-context/context*\n\
rm /var/run/one-context/one_env\n\
'


        return script
        
    @staticmethod           
    def remove_start_script(vm_id):
        """ 
        #Removing from VM template "START SCRIPT" for setting a passwords. 
        """
        try:
            one.vm.updateconf(vm_id, 
                {
                'TEMPLATE':{
                    'CONTEXT':{
        #             'DISK_ID': '1',
                      'NETWORK': "YES",
                      'START_SCRIPT_BASE64': '',
        #             'TARGET': 'hda'
                     },  
                    'GRAPHICS':{ 
                      'LISTEN': '0.0.0.0',
                      'PORT': '5980',
                      'TYPE': 'VNC'
                    },
                    'CPU_MODEL':{'host-passthrough'},
                    'OS':{'ARCH': 'x86_64','MACHINE': 'pc'}
                },
                }
                )
        except Exception as e:
            return {"error": str(e)}
