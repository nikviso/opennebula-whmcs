<?php
/**
 * WHMCS SDK Sample Provisioning Module
 *
 * Provisioning Modules, also referred to as Product or Server Modules, allow
 * you to create modules that allow for the provisioning and management of
 * products and services in WHMCS.
 *
 * This sample file demonstrates how a provisioning module for WHMCS should be
 * structured and exercises all supported functionality.
 *
 * Provisioning Modules are stored in the /modules/servers/ directory. The
 * module name you choose must be unique, and should be all lowercase,
 * containing only letters & numbers, always starting with a letter.
 *
 * Within the module itself, all functions must be prefixed with the module
 * filename, followed by an underscore, and then the function name. For this
 * example file, the filename is "provisioningmodule" and therefore all
 * functions begin "onecontrol_".
 *
 * If your module or third party API does not support a given function, you
 * should not define that function within your module. Only the _ConfigOptions
 * function is required.
 *
 * For more information, please refer to the online documentation.
 *
 * @see https://developers.whmcs.com/provisioning-modules/
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license https://www.whmcs.com/license/ WHMCS Eula
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

// Require any libraries needed for the module to function.
// require_once __DIR__ . '/path/to/library/loader.php';
//
// Also, perform any initialization required by the service's library.

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related abilities and
 * settings.
 *
 * @see https://developers.whmcs.com/provisioning-modules/meta-data-params/
 *
 * @return array
 */
function onecontrol_MetaData()
{
    return array(
        'DisplayName' => 'Open Nebula Control(R)',
        'APIVersion' => '1.0', // Use API Version 1.1
        'RequiresServer' => true, // Set true if module requires a server to work
        'DefaultNonSSLPort' => '1111', // Default Non-SSL Connection Port
        'DefaultSSLPort' => '1112', // Default SSL Connection Port
        'ServiceSingleSignOnLabel' => 'Login to Panel as User',
        'AdminSingleSignOnLabel' => 'Login to Panel as Admin',
    );
}

/**
 * Define product configuration options.
 *
 * The values you return here define the configuration options that are
 * presented to a user when configuring a product for use with the module. These
 * values are then made available in all module function calls with the key name
 * configoptionX - with X being the index number of the field from 1 to 24.
 *
 * You can specify up to 24 parameters, with field types:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each and their possible configuration parameters are provided in
 * this sample function.
 *
 * @see https://developers.whmcs.com/provisioning-modules/config-options/
 *
 * @return array
 */
function onecontrol_ConfigOptions()
{
    return array(
        'ONE Templates ID' => array(
            'Type' => 'text',
            'Size' => '25',
            'Default' => '0',
            'Description' => 'Example: 110,350,390',
        ),    
    
    /*
        // a text field type allows for single line text input
        'Text Field' => array(
            'Type' => 'text',
            'Size' => '25',
            'Default' => '1024',
            'Description' => 'Enter in megabytes',
        ),
        // a password field type allows for masked text input
        'Password Field' => array(
            'Type' => 'password',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter secret value here',
        ),
        // the yesno field type displays a single checkbox option
        'Checkbox Field' => array(
            'Type' => 'yesno',
            'Description' => 'Tick to enable',
        ),
        // the dropdown field type renders a select menu of options
        'Dropdown Field' => array(
            'Type' => 'dropdown',
            'Options' => array(
                'option1' => 'Display Value 1',
                'option2' => 'Second Option',
                'option3' => 'Another Option',
            ),
            'Description' => 'Choose one',
        ),
        // the radio field type displays a series of radio button options
        'Radio Field' => array(
            'Type' => 'radio',
            'Options' => 'First Option,Second Option,Third Option',
            'Description' => 'Choose your option!',
        ),
        // the textarea field type allows for multi-line text input
        'Textarea Field' => array(
            'Type' => 'textarea',
            'Rows' => '3',
            'Cols' => '60',
            'Description' => 'Freeform multi-line text input field',
        ),
    */    
    );
}

/**
 * Provision a new instance of a product/service.
 *
 * Attempt to provision a new instance of a given product/service. This is
 * called any time provisioning is requested inside of WHMCS. Depending upon the
 * configuration, this can be any of:
 * * When a new order is placed
 * * When an invoice for a new order is paid
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function onecontrol_CreateAccount(array $params)
{
    try {
        // Call the service's provisioning function, using the values provided
        // by WHMCS in `$params`.
        //
        // A sample `$params` array may be defined as:
        //
        // ```
        // array(
        //     'domain' => 'The domain of the service to provision',
        //     'username' => 'The username to access the new service',
        //     'password' => 'The password to access the new service',
        //     'configoption1' => 'The amount of disk space to provision',
        //     'configoption2' => 'The new services secret key',
        //     'configoption3' => 'Whether or not to enable FTP',
        //     ...
        // )
        // ```
    
        $function_return = instantiate_vm($params);
        if ($function_return != 'success') {
            logModuleCall(
                'onecontrol',
                __FUNCTION__,
                $params,
                $function_return
            );            
            return $function_return;
        }    

    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'onecontrol',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }
    
    return 'success';
}

/**
 * Suspend an instance of a product/service.
 *
 * Called when a suspension is requested. This is invoked automatically by WHMCS
 * when a product becomes overdue on payment or can be called manually by admin
 * user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function onecontrol_SuspendAccount(array $params)
{
    try {
        // Call the service's suspend function, using the values provided by
        // WHMCS in `$params`.
        $function_return = onecontrol_poweroff_vm($params);
        if ($function_return != 'success') {
            logModuleCall(
                'onecontrol',
                __FUNCTION__,
                $params,
                $function_return
            );            
            return $function_return;
        }    
        
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'onecontrol',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Un-suspend instance of a product/service.
 *
 * Called when an un-suspension is requested. This is invoked
 * automatically upon payment of an overdue invoice for a product, or
 * can be called manually by admin user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function onecontrol_UnsuspendAccount(array $params)
{
    try {
        // Call the service's unsuspend function, using the values provided by
        // WHMCS in `$params`.
        $function_return = onecontrol_resume_vm($params);
        if ($function_return != 'success') {
            logModuleCall(
                'onecontrol',
                __FUNCTION__,
                $params,
                $function_return
            );            
            return $function_return;
        }    
        
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'onecontrol',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Terminate instance of a product/service.
 *
 * Called when a termination is requested. This can be invoked automatically for
 * overdue products if enabled, or requested manually by an admin user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function onecontrol_TerminateAccount(array $params)
{
    try {
        // Call the service's terminate function, using the values provided by
        // WHMCS in `$params`.
        
        if ($params['status'] != 'Cancelled') {
            $function_return = terminate_vm($params);
            if ($function_return != 'success') {
                logModuleCall(
                    'onecontrol',
                    __FUNCTION__,
                    $params,
                    $function_return
                );            
                return $function_return;
            }    
        } else {
            logModuleCall(
                'onecontrol',
                __FUNCTION__,
                $params,
                '!Cancelled'
            );          
        }    
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'onecontrol',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Change the password for an instance of a product/service.
 *
 * Called when a password change is requested. This can occur either due to a
 * client requesting it via the client area or an admin requesting it from the
 * admin side.
 *
 * This option is only available to client end users when the product is in an
 * active status.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
*/ 
/*
function onecontrol_ChangePassword(array $params)
{
    try {
        // Call the service's change password function, using the values
        // provided by WHMCS in `$params`.
        //
        // A sample `$params` array may be defined as:
        //
        // ```
        // array(
        //     'username' => 'The service username',
        //     'password' => 'The new service password',
        // )
        // ```
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'onecontrol',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}
*/

/**
 * Upgrade or downgrade an instance of a product/service.
 *
 * Called to apply any change in product assignment or parameters. It
 * is called to provision upgrade or downgrade orders, as well as being
 * able to be invoked manually by an admin user.
 *
 * This same function is called for upgrades and downgrades of both
 * products and configurable options.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function onecontrol_ChangePackage(array $params)
{
    try {
        // Call the service's change password function, using the values
        // provided by WHMCS in `$params`.
        //
        // A sample `$params` array may be defined as:
        //
        // ```
        // array(
        //     'username' => 'The service username',
        //     'configoption1' => 'The new service disk space',
        //     'configoption3' => 'Whether or not to enable FTP',
        // )
        // ```
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'onecontrol',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Test connection with the given server parameters.
 *
 * Allows an admin user to verify that an API connection can be
 * successfully made with the given configuration parameters for a
 * server.
 *
 * When defined in a module, a Test Connection button will appear
 * alongside the Server Type dropdown when adding or editing an
 * existing server.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function onecontrol_TestConnection(array $params)
{
    try {
        // Call the service's connection test function.

        $success = true;
        $errorMsg = '';
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'onecontrol',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        $success = false;
        $errorMsg = $e->getMessage();
    }

    return array(
        'success' => $success,
        'error' => $errorMsg,
    );
}

/**
 * Additional actions an admin user can invoke.
 *
 * Define additional actions that an admin user can perform for an
 * instance of a product/service.
 *
 * @see onecontrol_buttonOneFunction()
 *
 * @return array
 */
function onecontrol_AdminCustomButtonArray()
{
    return array(
        "Reboot VPS" => "reboot_vm",
        "Resume VPS" => "resume_vm",
        "Power off VPS" => "poweroff_vm",
    );
}

/**
 * Additional actions a client user can invoke.
 *
 * Define additional actions a client user can perform for an instance of a
 * product/service.
 *
 * Any actions you define here will be automatically displayed in the available
 * list of actions within the client area.
 *
 * @return array
 */

function onecontrol_ClientAreaCustomButtonArray()
{
    return array(
        "Reboot VPS" => "reboot_vm",
        "Resume VPS" => "resume_vm",
        "Poweroff VPS" => "poweroff_vm",        
    );
}

/**
 * Custom function for performing an additional action.
 *
 * You can define an unlimited number of custom functions in this way.
 *
 * Similar to all other module call functions, they should either return
 * 'success' or an error message to be displayed.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see onecontrol_AdminCustomButtonArray()
 *
 * @return string "success" or an error message
 */
function onecontrol_buttonOneFunction(array $params)
{
    try {
        // Call the service's function, using the values provided by WHMCS in
        // `$params`.
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'onecontrol',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}


function instantiate_vm(array $params)
{
    try {
            // Call the service's function, using the values provided by WHMCS in
            // `$params`.

            $result_onevm_check = Capsule::table('mod_onecontrol_onevm')
                                ->select('vm_id')
                                ->where('service_id',$params['serviceid'])
                                ->first();
            if($result_onevm_check) {
                return "A VPS has already been created for this product. VMID: ".$result_onevm_check->{'vm_id'}; 
            }
            
            $result_oneuser = Capsule::table( 'mod_onecontrol_oneuser' )
                        ->select('one_user_id')
                        ->where('whmcs_user_id',$params['clientsdetails']['id'])
                        ->first();
                        
            if($result_oneuser) {
                $one_user_id = $result_oneuser->{'one_user_id'};
                $result_onevm = Capsule::table('mod_onecontrol_onevm')
                                ->select('id','vm_name','vm_ip_address','vm_network_mask','vm_network_address',
                                         'vm_gw_ip_address','vm_dns_ip_address','one_network_id')
                                ->where('vm_id',0)
                                ->where('user_id',0)
                                ->where('service_id',0)
                                ->first();
                               
                if($result_onevm){

                    $arr = array(
                        "cmd" => "template_instantiate",
                        "user_id" => $one_user_id,
                        "vm_name" => $result_onevm->{'vm_name'},
                        "template_id" => get_onevmtemplate($params),
                        "ip_address" => $result_onevm->{'vm_ip_address'},
                        "dns_ip_address" => $result_onevm->{'vm_dns_ip_address'},
                        "gw_ip_address" => $result_onevm->{'vm_gw_ip_address'},
                        "network_id" => $result_onevm->{'one_network_id'},
                        "network_address" => $result_onevm->{'vm_network_address'},
                        "network_mask" => $result_onevm->{'vm_network_mask'},
                    );
 
                    $one_reply = send_to_one($arr);
                    if($one_reply->{'error'}){

                        $result_onevm_user = Capsule::table('mod_onecontrol_onevm')
                                    ->select('vm_id')
                                    ->where('user_id',$result_oneuser->{'one_user_id'})
                                    ->first();                

                        if (!$result_onevm_user){
                            $arr = array(
                                "cmd" => "user_delete",
                                "user_id" => $result_oneuser->{'one_user_id'},    
                            );
                            $one_reply = send_to_one($arr);              
                            if($one_reply->{'error'}){
                                return '. Oops! Something went wrong!';
                            }  
                            
                            Capsule::table('mod_onecontrol_oneuser')
                                ->where('whmcs_user_id',$params['clientsdetails']['id'])
                                ->delete();
                        }        

                        return '. Oops! Something went wrong!';
                    }

                    Capsule::table('mod_onecontrol_onevm')->where('id',$result_onevm->{'id'})
                        ->update(
                            array(
                                'vm_user'=>$one_reply->{'vm_user'},
                                'vm_user_password'=>$one_reply->{'vm_user_password'},
                                'vm_root_password'=>$one_reply->{'vm_root_password'}
                                )
                        ); 


                    Capsule::table('mod_onecontrol_onevm')->where('id',$result_onevm->{'id'})
                        ->update(
                            array(
                                'user_id'=>$one_user_id,
                                'service_id'=>$params['serviceid']
                                )
                        );    
                    
                    Capsule::table('mod_onecontrol_onevm')->where('id',$result_onevm->{'id'})
                        ->update(
                            array(
                                'vm_id'=>$one_reply->{'vm_id'}
                                )
                        );
                } else {
                    logModuleCall(
                        'onecontrol',
                        __FUNCTION__,
                        $params,
                        $arr
                    );
                    return '. Oops! Something went wrong!';
                }
                
            } else {
                require_once('lib/SecGenerators.php');
                $secgenerators = new SecGenerators;
                $one_user_password =  $secgenerators->generate_password();
                $arr = array(
                            "cmd" => "user_allocate",
                            "user_name" => $params["clientsdetails"]["email"],
                            "user_password" => $one_user_password,
                            );
                
                $one_reply = send_to_one($arr);
                if($one_reply->{'error'}){
                    return '. Oops! Something went wrong!';
                }

                $one_user_group_id_array = '';
                foreach($one_reply->{'user_group_id_array'} as $key => $value)
                    {
                        $one_user_group_id_array = $one_user_group_id_array . $value . ',';
                    }

                $result_oneuser = Capsule::table( 'mod_onecontrol_oneuser' )->insert(
                                    array(
                                        'whmcs_user_id' => $params['clientsdetails']['id'],
                                        'one_user_id' => $one_reply->{'user_id'},
                                        'one_user_group_id' => $one_user_group_id_array,
                                        'one_user_name' => $one_reply->{'user_name'},
                                        'one_user_password' => password_hash($one_user_password, PASSWORD_BCRYPT),
                                        'one_user_token' => '')               
                                );
                
                instantiate_vm($params);
            }    
        } catch (Exception $e) {
        // Record the error in WHMCS's module log.
            logModuleCall(
                'onecontrol',
                __FUNCTION__,
                $params,
                $e->getMessage(),
                $e->getTraceAsString()
            );
            //return $e->getMessage();        
            return '. Oops! Something went wrong!';
        }

    return 'success';
}


function terminate_vm(array $params)
{
    try {
            // Call the service's function, using the values provided by WHMCS in
            // `$params`.
           
            $result_oneuser = Capsule::table('mod_onecontrol_oneuser')
                ->select('one_user_id')
                ->where('whmcs_user_id',$params['clientsdetails']['id'])
                ->first();
                
            //$one_user_id = $result_oneuser->{'one_user_id'};

            $result_onevm = Capsule::table('mod_onecontrol_onevm')
                ->select('vm_id')
                ->where('service_id',$params['serviceid'])
                ->where('user_id',$result_oneuser->{'one_user_id'})
                ->first();
                
            $arr = array(
                "cmd" => "vm_terminate",
                "vm_id" => $result_onevm->{'vm_id'},
                "user_id" => $result_oneuser->{'one_user_id'}    
            );

            $one_reply = send_to_one($arr);              
            if($one_reply->{'error'}){
                return '. Oops! Something went wrong!';
            }

            Capsule::table('mod_onecontrol_onevm')
                ->where('vm_id',$result_onevm->{'vm_id'})
                ->update(
                    array(
                        'vm_id'=>0,
                        'service_id'=>0,
                        'user_id'=>0,
                        'vm_token'=>'',
                        'vm_user'=>'',
                        'vm_user_password'=>'',
                        'vm_root_password'=>''                        
                        )
                );
                
            $result_onevm_user = Capsule::table('mod_onecontrol_onevm')
                ->select('vm_id')
                ->where('user_id',$result_oneuser->{'one_user_id'})
                ->first();                

            if (!$result_onevm_user){
                $arr = array(
                    "cmd" => "user_delete",
                    "user_id" => $result_oneuser->{'one_user_id'},    
                );
                
                $one_reply = send_to_one($arr);              
                if($one_reply->{'error'}){
                    return '. Oops! Something went wrong!';
                }  
                
                Capsule::table('mod_onecontrol_oneuser')
                    ->where('whmcs_user_id',$params['clientsdetails']['id'])
                    ->delete();
            }
            
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'onecontrol',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        //return $e->getMessage();        
        return '. Oops! Something went wrong!';
    }    

    return 'success';
}

/**
 * Custom function for performing an additional action.
 *
 * You can define an unlimited number of custom functions in this way.
 *
 * Similar to all other module call functions, they should either return
 * 'success' or an error message to be displayed.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see onecontrol_ClientAreaCustomButtonArray()
 *
 * @return string "success" or an error message
 */
 
function onecontrol_poweroff_vm(array $params)
{
    return action_vm($params,'poweroff');
}

function onecontrol_reboot_vm(array $params)
{
    return action_vm($params,'reboot');
}    

function onecontrol_resume_vm(array $params)
{
    return action_vm($params,'resume');
}

function action_vm(array $params,$action)
{
    try {
        // Call the service's function, using the values provided by WHMCS in
        // `$params`.
    
        //var_dump($params);
        $result_onevm = Capsule::table('mod_onecontrol_onevm')
                ->select('vm_id','user_id')
                ->where('service_id',$params['serviceid'])
                ->first();
        
        $arr = array(
            "cmd" => "vm_action",
            "vm_id" => $result_onevm->{'vm_id'},
            "user_id" => $result_onevm->{'user_id'},
            "action" => $action, 
        );
        
        $one_reply = send_to_one($arr);  
        if($one_reply->{'error'}){
            return '. Oops! Something went wrong!';
        }

    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'onecontrol',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        //return $e->getMessage();        
        return '. Oops! Something went wrong!';
    }    
    
    return 'success';
}




/**
 * Admin services tab additional fields.
 *
 * Define additional rows and fields to be displayed in the admin area service
 * information and management page within the clients profile.
 *
 * Supports an unlimited number of additional field labels and content of any
 * type to output.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see onecontrol_AdminServicesTabFieldsSave()
 *
 * @return array
 */
function onecontrol_AdminServicesTabFields(array $params)
{
    try {
        // Call the service's function, using the values provided by WHMCS in
        // `$params`.
        $response = array();

        // Return an array based on the function's response.
        return array(
            'Number of Apples' => (int) $response['numApples'],
            'Number of Oranges' => (int) $response['numOranges'],
            'Last Access Date' => date("Y-m-d H:i:s", $response['lastLoginTimestamp']),
            'Something Editable' => '<input type="hidden" name="onecontrol_original_uniquefieldname" '
                . 'value="' . htmlspecialchars($response['textvalue']) . '" />'
                . '<input type="text" name="onecontrol_uniquefieldname"'
                . 'value="' . htmlspecialchars($response['textvalue']) . '" />',
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'onecontrol',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        // In an error condition, simply return no additional fields to display.
    }

    return array();
}

/**
 * Execute actions upon save of an instance of a product/service.
 *
 * Use to perform any required actions upon the submission of the admin area
 * product management form.
 *
 * It can also be used in conjunction with the AdminServicesTabFields function
 * to handle values submitted in any custom fields which is demonstrated here.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see onecontrol_AdminServicesTabFields()
 */
function onecontrol_AdminServicesTabFieldsSave(array $params)
{
    // Fetch form submission variables.
    $originalFieldValue = isset($_REQUEST['onecontrol_original_uniquefieldname'])
        ? $_REQUEST['onecontrol_original_uniquefieldname']
        : '';

    $newFieldValue = isset($_REQUEST['onecontrol_uniquefieldname'])
        ? $_REQUEST['onecontrol_uniquefieldname']
        : '';

    // Look for a change in value to avoid making unnecessary service calls.
    if ($originalFieldValue != $newFieldValue) {
        try {
            // Call the service's function, using the values provided by WHMCS
            // in `$params`.
        } catch (Exception $e) {
            // Record the error in WHMCS's module log.
            logModuleCall(
                'onecontrol',
                __FUNCTION__,
                $params,
                $e->getMessage(),
                $e->getTraceAsString()
            );

            // Otherwise, error conditions are not supported in this operation.
        }
    }
}

/**
 * Perform single sign-on for a given instance of a product/service.
 *
 * Called when single sign-on is requested for an instance of a product/service.
 *
 * When successful, returns a URL to which the user should be redirected.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function onecontrol_ServiceSingleSignOn(array $params)
{
    try {
        // Call the service's single sign-on token retrieval function, using the
        // values provided by WHMCS in `$params`.
        $response = array();

        return array(
            'success' => true,
            'redirectTo' => $response['redirectUrl'],
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'onecontrol',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return array(
            'success' => false,
            'errorMsg' => $e->getMessage(),
        );
    }
}

/**
 * Perform single sign-on for a server.
 *
 * Called when single sign-on is requested for a server assigned to the module.
 *
 * This differs from ServiceSingleSignOn in that it relates to a server
 * instance within the admin area, as opposed to a single client instance of a
 * product/service.
 *
 * When successful, returns a URL to which the user should be redirected to.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function onecontrol_AdminSingleSignOn(array $params)
{
    try {
        // Call the service's single sign-on admin token retrieval function,
        // using the values provided by WHMCS in `$params`.
        $response = array();

        return array(
            'success' => true,
            'redirectTo' => $response['redirectUrl'],
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'onecontrol',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return array(
            'success' => false,
            'errorMsg' => $e->getMessage(),
        );
    }
}

/**
 * Client area output logic handling.
 *
 * This function is used to define module specific client area output. It should
 * return an array consisting of a template file and optional additional
 * template variables to make available to that template.
 *
 * The template file you return can be one of two types:
 *
 * * tabOverviewModuleOutputTemplate - The output of the template provided here
 *   will be displayed as part of the default product/service client area
 *   product overview page.
 *
 * * tabOverviewReplacementTemplate - Alternatively using this option allows you
 *   to entirely take control of the product/service overview page within the
 *   client area.
 *
 * Whichever option you choose, extra template variables are defined in the same
 * way. This demonstrates the use of the full replacement.
 *
 * Please Note: Using tabOverviewReplacementTemplate means you should display
 * the standard information such as pricing and billing details in your custom
 * template or they will not be visible to the end user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function onecontrol_ClientArea(array $params)
{
    // Determine the requested action and set service call parameters based on
    // the action.
        
    $requestedAction = isset($_REQUEST['customAction']) ? $_REQUEST['customAction'] : '';

    require_once('lib/SecGenerators.php');
    $secgenerators = new SecGenerators;

    if ($requestedAction == 'manage') {
        $serviceAction = 'get_usage';
        $templateFile = 'templates/manage.tpl';
        $token = $secgenerators->generate_token();
        Capsule::table('mod_onecontrol_onevm')->where('service_id',$params['serviceid'])
            ->update(
                array(
                    'vm_token'=>$token,
                    )
        ); 
        $result_onevm = Capsule::table('mod_onecontrol_onevm')
            ->select('vm_name','vm_ip_address')
            ->where('service_id',$params['serviceid'])
            ->first();
    } else {
        $serviceAction = 'get_stats';
        $templateFile = 'templates/overview.tpl';
        $token = $secgenerators->generate_token();
        Capsule::table('mod_onecontrol_onevm')->where('service_id',$params['serviceid'])
            ->update(
                array(
                    'vm_token'=>$token,
                    )
        );   

        //echo "SID:".$params['serviceid']."; OID:".$params['attributes']['orderid'];
        //var_dump($params);
    }

    try {
        // Call the service's function based on the request action, using the
        // values provided by WHMCS in `$params`.
        $response = array();

        //$extraVariable1 = 'abc';
        //$extraVariable2 = '123';
        //$extraVariable1 = var_dump($params);
        //$extraVariable1 = $params['status'];
        //$extraVariable1 = $params['configoption1'];
        //$extraVariable2 = $params["configoptions"]["Operating System"]. ", " . $params["clientsdetails"]["email"];
        //$extraVariable1 = $_SESSION['uid'];
        //$extraVariable2 = $params["clientsdetails"]["email"];

        if ($params['status'] == 'Cancelled' or $params['status'] == 'Terminated' or $params['status'] == 'Pending')
        {
            $productstatuscancelled = true;
        } 
        
        return array(
            'tabOverviewReplacementTemplate' => $templateFile,
            'templateVariables' => array(
                'token' => $token,
                'productstatuscancelled' => $productstatuscancelled,
                'vmostype' => $params["configoptions"]['Operating System'],
                'vmname' => $result_onevm->{'vm_name'},
                'vmipaddress' => $result_onevm->{'vm_ip_address'},
            ),
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'onecontrol',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        // In an error condition, display an error page.
        return array(
            'tabOverviewReplacementTemplate' => 'error.tpl',
            'templateVariables' => array(
                'usefulErrorHelper' => $e->getMessage(),
            ),
        );
    }
}

function get_onevmtemplate(array $params)
{
    $result_onevmtemplate = Capsule::table('mod_onecontrol_onetemplate')
                            ->select('one_template_id')
                            ->where('one_image_os',$params['configoptions']['Operating System'])
                            ->get();           
    
    foreach($result_onevmtemplate as $value){
        if (in_array ($value->one_template_id,explode (',', $params['configoption1']))) return $value->one_template_id;
    } 
}

function send_to_one($arr) 
{
    require_once('lib/OneConnector.php');
    $oneconnector = new OneConnector;
    
    return $oneconnector->connector($arr);
}    
    

