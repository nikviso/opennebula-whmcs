<?php

#define("ADMINAREA", true);
define("CLIENTAREA", true);
#define("SHOPPING_CART", true);
require_once(__DIR__ . '/../../../init.php');
require_once(ROOTDIR . '/includes/dbfunctions.php');
#require(ROOTDIR . "/includes/orderfunctions.php");
#require(ROOTDIR . "/includes/domainfunctions.php");
#require(ROOTDIR . "/includes/configoptionsfunctions.php");
#require(ROOTDIR . "/includes/customfieldfunctions.php");
#require(ROOTDIR . "/includes/clientfunctions.php");
#require(ROOTDIR . "/includes/invoicefunctions.php");
#require(ROOTDIR . "/includes/processinvoices.php");
#require(ROOTDIR . "/includes/gatewayfunctions.php");
#require(ROOTDIR . "/includes/modulefunctions.php");
#require(ROOTDIR . "/includes/ccfunctions.php");
#require(ROOTDIR . "/includes/cartfunctions.php");
#include_once(ROOTDIR . '/includes/clientareafunctions.php');

#use WHMCS\ClientArea;
use WHMCS\Database\Capsule;
#use WHMCS\Smarty ;

function get_vm_state($id, $token)
{
    require_once('lib/OneConnector.php');
    $oneconnector = new OneConnector;
    
    $result_onevm = Capsule::table('mod_onecontrol_onevm')
            ->select('vm_id','user_id')
            ->where('service_id',$id)
            ->where('vm_token',$token)
            ->first();
    
    if(!$result_onevm){
        return '<div style="color:red;"> Oops! Something went wrong! </div>';
    }
    
    //var_dump($result_onevm);
    
    $arr = array(
        "cmd" => "get_vm_state",
        "vm_id" => $result_onevm->{'vm_id'},
        "user_id" => $result_onevm->{'user_id'}    
    );
    
    $one_reply = $oneconnector->connector($arr);  
      
    if($one_reply->{'error'}){
        return '<div style="color:red;"> Oops! Something went wrong! </div>';
    } else {
        return $one_reply->{'vm_state'};
    }
    
    /*
    if ($one_reply->{'vm_state'} == 'ACTIVE') {
        return '<p style="color:green;">'.$one_reply->{'vm_state'}.'</p>';
    } elseif ($one_reply->{'vm_state'} == 'POWEROFF') {
        return '<p style="color:red;">'.$one_reply->{'vm_state'}.'</p>';
    } else {
        return '<p style="color:#CC9933;">'.$one_reply->{'vm_state'}.'</p>';
    }
    */
}
?>