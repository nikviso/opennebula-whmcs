import random
import time
import base64

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
               'START_SCRIPT_BASE64': base64.b64encode(create_start_script(vm_root_password, vm_user_password, vm_user))
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
            template_terminate(vm_name, one, True) 
            return {"error": str(e)}

        
        """ 
        #Removing from VM template "START SCRIPT" for setting a passwords. 
        try:
            vm_id = one.vm.updateconf(80, 
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
        """ 
        #Removing VM template.
        template_terminate(vm_name, one, False)
            
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
                       'START_SCRIPT_BASE64': base64.b64encode(create_start_script(vm_root_password,vm_user_password,vm_user)) 
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
            template_terminate(vm_name, one, True)
            return {"error": str(e)}
        
        """ 
        #Removing from VM template "START SCRIPT" for setting a passwords. 
        try:
            vm_id = one_user.vm.updateconf(80, 
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
        """   

        #Removing VM template.
        template_terminate(vm_name, one, False)
        
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

    @staticmethod
    def template_terminate(template_name, one, image_remove = False):
        """
        Terminating VM template
        """
        
        template_id = get_template_id(template_name, one)
        if template_id:
            try:
                return one.template.delete(template_id, image_remove)
            except Exception as e:
                return {"error": str(e)}  
        else:
            return {"error": "not found template name"}
     
    @staticmethod     
    def create_start_script(vm_root_password,vm_user_password,vm_user):
        """
        """
        
        script = '#!/bin/sh\n' +\
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
esac'
        
        #Linux
        #script = 'echo -e "' + vm_root_password + '\n' + vm_root_password + '" | passwd root; echo -e "' \
        #          + vm_user_password + '\n' + vm_user_password + '" | passwd ' + vm_user
       
        return script