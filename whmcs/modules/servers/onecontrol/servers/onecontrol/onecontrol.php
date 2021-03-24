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
 * containing only letters & numbers, always starting waith a letter.
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
//use WHMCS\Module\Server\onecontrol\OneConnector;


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
        'language' => 'english'
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

 function onecontrol_ConfigOptions() {
   return array(
       // a text field type allows for single line text input
       'templateid' => array(
           'Type' => 'text',
           'Size' => '3',
           'Default' => '',
           'Description' => 'Enter in Template ID',
       ),
       'usergroupid' => array(
           'Type' => 'text',
           'Size' => '3',
           'Default' => '',
           'Description' => 'Enter in User Group ID',
       ),
       'operating_system' => [
            'FriendlyName' => 'Operating System',
           'Type' => 'yesno',
           'Description' => 'Enable configurable options OS function',
       ],
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
  require_once('lib/SecGenerators.php');
  $secgenerators = new SecGenerators;

  $password = $secgenerators->generate_password();
  if (!Capsule::table('on_user')->where('user_id', $params['userid'])->first()) {
    if((int)$params['configoption2'] > '0'){
      $user_group_id_array = array((int)$params['configoption2']);
      $arr = array(
          "cmd" => "user_allocate",
          // "user_name" => $params['username'],
          "user_name" => $params['clientsdetails']['email'],
          "user_password" => $password,
          "user_group_id_array" => $user_group_id_array
        );
    }
    else{
      $arr = array(
          "cmd" => "user_allocate",
          // "user_name" => $params['username'],
          "user_name" => $params['clientsdetails']['email'],
          "user_password" => $password
        );
    }
    // $data = send_data_on($arr);
    $data = send_to_one($arr);

      Capsule::table('on_user')->insert([
          'user_id' => $params['userid'],
          'on_user_id' => $data['user_id'],
      ]);
  }

  $on_user_data=Capsule::table( 'on_user' )
      ->select('on_user_id')
      ->where('user_id',$params['userid'])
      ->first();

  $ip_data=Capsule::table('mod_onecontrol_onevm')
      ->where('vm_active', '0')
      ->first();

  $user_id = (int)$on_user_data->on_user_id;
  if($params['configoption3'] == "on"){
    $on_user_data=Capsule::table( 'on_templates' )
      ->select('on_tid')
      ->where('os',$params['configoptions']['Choose your Operating System'])
      ->where('pid',$params['pid'])
      ->first();
    $template_id = (int)$on_user_data->on_tid;
  }
  else{
    $template_id = (int)$params['configoption1']; //admin panel configuration paramether
  }

  $vm_name = $ip_data->vm_name;
  $ip_address = $ip_data->vm_ip_address; //192.168.58.11
  $dns_ip_address = $ip_data->vm_dns_ip_address; //8.8.8.8
  $gw_ip_address = $ip_data->vm_gw_ip_address; //192.168.58.1
  $network_id = $ip_data->one_network_id;    //????????
  $network_address = $ip_data->vm_network_address; //92.168.58.0
  $subnet_mask = $ip_data->vm_network_mask; //255.255.255.0

  $arr = array(
      "cmd" => "template_instantiate",
      "user_id" => $user_id,
      "vm_name" => $vm_name,
      "template_id" => $template_id,
      "ip_address" => $ip_address,
      "dns_ip_address" => $dns_ip_address,
      "gw_ip_address" => $gw_ip_address,
      "network_id" => $network_id,
      "network_address" => $network_address,
      "network_mask" => $subnet_mask,
  );

  // $data = send_data_on($arr);
  $data = send_to_one($arr);

  $GLOBALS['on_username'] = $data['vm_user'];
  $GLOBALS['on_password'] = $data['vm_user_password'];
  $GLOBALS['on_root_password'] = $data['vm_root_password'];

  Capsule::table('tblhosting')
    ->where('id', (int)$params['serviceid'])
    ->update([
      'dedicatedip' => $ip_address
    ]);

  Capsule::table('mod_onecontrol_onevm')
    ->where('id', $ip_data->id)
    ->update([
      'user_id' => $user_id,
      'vm_active' => '1',
      'vm_id' => $data['vm_id'],
      'prod_id' => $params['serviceid']
    ]);
return 'success';
}

function onecontrol_ClientRebuild(array $params)
{
    try {
       $arr = array(
         "cmd" => "vm_terminate",
         // "action" => "terminate",
         "vm_id" => (int)get_vm_id($params),
         "user_id" => (int)get_user_id($params),
       );
     // $atbilde = send_data_on($arr);
     $atbilde = send_to_one($arr);

     if($atbilde['action'] == 'vm terminated')
       {
         $ip_data=Capsule::table('mod_onecontrol_onevm')
               ->where('prod_id',(int)$params['serviceid'])
               ->first();

         if($params['configoption3'] == "on"){
           $on_user_data=Capsule::table( 'on_templates' )
               ->select('on_tid')
               ->where('os',$params['os_select'])
               ->where('pid',$params['pid'])
                ->first();
            $template_id = (int)$on_user_data->on_tid;
           // $template_id = 105;
         }
         else{
           //admin panel paramether
           $template_id = (int)$params['configoption1'];
         }

         $vm_name = $ip_data->vm_name;
         $ip_address = $ip_data->vm_ip_address;
         $dns_ip_address = $ip_data->vm_dns_ip_address;
         $gw_ip_address = $ip_data->vm_gw_ip_address;
         $network_id = $ip_data->one_network_id;
         $network_address = $ip_data->vm_network_address;
         $subnet_mask = $ip_data->vm_network_mask;

         $arr = array(
             "cmd" => "template_instantiate",
             "user_id" => (int)get_user_id($params),
             "vm_name" => $vm_name,
             "template_id" => $template_id,
             "ip_address" => $ip_address,
             "dns_ip_address" => $dns_ip_address,
             "gw_ip_address" => $gw_ip_address,
             "network_id" => $network_id,
             "network_address" => $network_address,
             "network_mask" => $subnet_mask,
         );

         // $data = send_data_on($arr);
         $data = send_to_one($arr);

         $GLOBALS['on_username'] = $data['vm_user'];
         $GLOBALS['on_password'] = $data['vm_user_password'];
         $GLOBALS['on_root_password'] = $data['vm_root_password'];

         Capsule::table('on_test')->insert([
             'parole' => $data['vm_root_password']
         ]);

         Capsule::table('tblhosting')
             ->where('id', (int)$params['serviceid'])
             ->update([
               'dedicatedip' => $ip_address
             ]);

         Capsule::table('mod_onecontrol_onevm')
             ->where('prod_id', (int)$params['serviceid'])
             ->update([
               // 'vm_active' => '1',
               'vm_id' => $data['vm_id']
             ]);

         Capsule::table('tblhostingconfigoptions')
             ->where('id', (int)$params['serviceid'])
             ->update([
               // 'vm_active' => '1',
               'optionid' => $params['osid_select']
             ]);

      }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'opennebula',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }

      // header('Content-Type: application/json');
      // return 'success';
    $someArray = [
      "name" => "11111"
    ];
    echo $someJSON;
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
        $arr = array(
            "cmd" => "vm_action",
            "action" => "poweroff",
            "vm_id" => (int)get_vm_id($params),
            "user_id" => (int)get_user_id($params),
        );
        send_to_one($arr);
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
        $arr = array(
            "cmd" => "vm_action",
            "action" => "resume",
            "vm_id" => (int)get_vm_id($params),
            "user_id" => (int)get_user_id($params),
        );
        // send_data_on($arr);
        send_to_one($arr);
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
        $arr = array(
            "cmd" => "vm_terminate",
            // "action" => "terminate",
            "vm_id" => (int)get_vm_id($params),
            "user_id" => (int)get_user_id($params),
        );
        // send_data_on($arr);
        send_to_one($arr);
        //change data in db (write 0 - to IP is available)
        Capsule::table('mod_onecontrol_onevm')
            ->where('vm_id', (int)get_vm_id($params))
            ->update([
              'user_id' => '0',
              'vm_active' => '0',
              'vm_id' => '0',
              'prod_id' => '0'
            ]);
        $count_products=Capsule::table( 'mod_onecontrol_onevm' )
            ->select('id')
            ->where('user_id',(int)get_user_id($params))
            ->get();
             $skaits = count($count_products);

          if($skaits == '0'){
            $arr = array(
              "cmd" => "user_delete",
              "user_id" => (int)get_user_id($params)
            );
            // $action_delete = send_data_on($arr);
            $action_delete = send_to_one($arr);
            if($action_delete['action'] == 'user and user group deleted'){
              Capsule::table('on_user')
                ->where('on_user_id', $action_delete['user_id'])
                ->delete();
            }
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
function onecontrol_AdminCustomButtonArray($params)
{
    return array(
          "PowerOFF Hard" => "PowerOffHard",
          "Poweroff" => "Poweroff",
          "Reboot" => "Reboot",
          "Reboot Hard" => "RebootHard",
          // "Terminate" => "Terminate",
          // "Suspend" => "Suspend",
          "Resume" => "Resume",
        //  "Rebuild" => "Rebuild"
        // "Button 1 Display Value" => "buttonOneFunction",
        // "Button 2 Display Value" => "buttonTwoFunction",
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

function onecontrol_checkVMStatus($params){
 $status = onecontrol_info($params);
 return $status;

}

function onecontrol_ClientAreaCustomButtonArray($params)
{
    $arrayResume = array(
        "Resume" => "ClientResume",
    );
    $arrayPowerOff = array(
      "Poweroff" => "Poweroff",
      "Power Off Hard" => "PowerOffHard",
      "Reboot" => "Reboot",
    );


    $requestedAction = isset($_REQUEST['customAction']) ? $_REQUEST['customAction'] : '';
  //  $status = onecontrol_info($params);

    if($status['vm_state'] == "POWEROFF"){
      return $arrayResume;
    }
    else{
      if ($requestedAction == 'rebuild') {
        return array(
        "Rebuild" => "ClientRebuild",
        );
      }else{
        return $arrayPowerOff;
      }
    }
}

function get_vm_id($params){
  try {
    if($_SESSION['adminid']=='1'){
      $on_vm_id=Capsule::table( 'mod_onecontrol_onevm' )
          ->select('vm_id')
          ->where('prod_id',(int)$params['serviceid'])
          ->first();
    }
    else{
      $on_vm_id=Capsule::table( 'mod_onecontrol_onevm' )
          ->select('vm_id')
          ->where('prod_id',(int)$params['serviceid'])
          ->where('vm_token',(int)$params['token'])
          ->first();
    }

  } catch (\Exception $e) {
      echo $e->getMessage();
      return $e->getMessage();
  }
  return $on_vm_id->vm_id;
}

function get_user_id($params){
  try {
    $on_user_data=Capsule::table( 'on_user' )
        ->select('on_user_id')
        ->where('user_id',$params['userid'])
        ->first();
  } catch (\Exception $e) {
      echo $e->getMessage();
      return $e->getMessage();
  }
  return $on_user_data->on_user_id;
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

function onecontrol_info(array $params)
{

    try {
      $arr = array(
          "cmd" => "get_vm_state",
          "vm_id" => (int)get_vm_id($params),
          "user_id" => (int)get_user_id($params),
      );
    $data = send_to_one($arr);

    // $data = send_data_on($arr);
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'opennebula',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
    return $data;
}

// function onecontrol_ClientPoweroff(array $params)
// {
//     try {
//       $arr = array(
//           "cmd" => "vm_action",
//           "action" => "poweroff",
//           "vm_id" => (int)get_vm_id($params),
//           "user_id" => (int)get_user_id($params),
//       );
//       send_data_on($arr);
//     } catch (Exception $e) {
//         // Record the error in WHMCS's module log.
//         logModuleCall(
//             'clouds365',
//             __FUNCTION__,
//             $params,
//             $e->getMessage(),
//             $e->getTraceAsString()
//         );
//         return $e->getMessage();
//     }
//     return 'success';
// }

function onecontrol_ClientResume(array $params)
{
    try {
      $arr = array(
          "cmd" => "vm_action",
          "action" => "resume",
          "vm_id" => (int)get_vm_id($params),
          "user_id" => (int)get_user_id($params),
      );
      // send_data_on($arr);
      send_to_one($arr);
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'opennebula',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
    return 'success';
}

function onecontrol_PowerOffHard(array $params)
{
    try {
      $arr = array(
          "cmd" => "vm_action",
          "action" => "poweroff-hard",
          "vm_id" => (int)get_vm_id($params),
          "user_id" => (int)get_user_id($params),
      );
      // send_data_on($arr);
      send_to_one($arr);
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'opennebula',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
    return 'success';
}

function onecontrol_Poweroff(array $params)
{
    try {
      $arr = array(
          "cmd" => "vm_action",
          "action" => "poweroff",
          "vm_id" => (int)get_vm_id($params),
          "user_id" => (int)get_user_id($params),
      );
      // send_data_on($arr);
      send_to_one($arr);
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'clouds365',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
    return 'success';
}

function onecontrol_Reboot(array $params)
{
    try {
      $arr = array(
          "cmd" => "vm_action",
          "action" => "reboot",
          "vm_id" => (int)get_vm_id($params),
          "user_id" => (int)get_user_id($params),
      );
      // send_data_on($arr);
      send_to_one($arr);
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'opennebula',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
    return 'success';
}

function onecontrol_RebootHard(array $params)
{
    try {
      $arr = array(
        "cmd" => "vm_action",
        "action" => "reboot-hard",
        "vm_id" => (int)get_vm_id($params),
        "user_id" => (int)get_user_id($params),
      );
      // send_data_on($arr);
      send_to_one($arr);
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'opennebula',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
    return 'success';
}

function onecontrol_Resume(array $params)
{
    try {
      $arr = array(
          "cmd" => "vm_action",
          "action" => "resume",
          "vm_id" => (int)get_vm_id($params),
          "user_id" => (int)get_user_id($params),
      );
      // send_data_on($arr);
            send_to_one($arr);
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'opennebula',
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

    require_once('lib/OneConnector.php');
    $oneconnector = new OneConnector;

    $token = $oneconnector->generate_token();

    $params['token'] = $token;
    Capsule::table('mod_onecontrol_onevm')
        ->where('prod_id', (int)$params['serviceid'])
        ->update([
          // 'vm_active' => '1',
          'vm_token' => $token
        ]);


// var_dump($token);
// die();
    // echo $token;
    // Determine the requested action and set service call parameters based on
    // the action.
    $status = onecontrol_info($params);

    if($status['vm_state'] == "POWEROFF"){
      $action = 'ClientResume';
      $action_name = 'Resume';
      $statusbulb = "red";
    }
    elseif ($status['vm_state'] == "ACTIVE"){
	  	$statusbulb = "green";
      $action = "Poweroff";
      $action_name = 'Power Off';
	  }else{
		  $statusbulb = "inprogress";
		}

    $requestedAction = isset($_REQUEST['customAction']) ? $_REQUEST['customAction'] : '';



    // $status = onecontrol_info($params);
    $serverstatus = $status['vm_state'];

    if ($requestedAction == 'manage') {
        $serviceAction = 'get_usage';
        $templateFile = 'templates/manage.tpl';
        $productDetailTitle = 'Manage Product';
    } elseif ($requestedAction == 'rebuild') {
        $serviceAction = 'get_rebuild';
        $templateFile = 'templates/rebuild.tpl';
        $productDetailTitle = 'Rebuild server';
    } else {
        $serviceAction = 'get_stats';
        $templateFile = 'templates/overview.tpl';
        $productDetailTitle = 'Server overview';
    }

    try {
        // Call the service's function based on the request action, using the
        // values provided by WHMCS in `$params`.
        $response = array();
        $extraVariable1 = $params['configoption1'];
        $extraVariable2 = $params["configoptions"]["OS Type"]. ", " . $params["clientsdetails"]["email"];
        $user_email = $params["clientsdetails"]["email"];

   $testvar = $params['templatevars']['configurableoptions']['0']['selectedname'];

   $osnames = $params['templatevars']['configurableoptions']['0']['options'];

        return array(
    	    'overrideDisplayTitle' => $productDetailTitle,
          'tabOverviewReplacementTemplate' => $templateFile,
          'templateVariables' => array(
            'extraVariable1' => $extraVariable1,
            'extraVariable2' => $extraVariable2,
            'extraVariable3' => $extraVariable3,
            'user_email' => $user_email,
            'osnames' => $osnames,
            'testvar' => $testvar,
            'serverstatus' => $serverstatus,
            'action' => $action,
            'action_name' => $action_name,
            'params' => $params,
            'token' => $token,
            'statusbulb' => $statusbulb,
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

function send_to_one($arr)
{
    require_once('lib/OneConnector.php');
    $oneconnector = new OneConnector;

    return (array) $oneconnector->connector($arr);
}
