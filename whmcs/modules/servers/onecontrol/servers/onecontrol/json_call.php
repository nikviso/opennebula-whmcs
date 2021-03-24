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
require_once(__DIR__ . '/onecontrol.php');

#use WHMCS\ClientArea;
#use WHMCS\Database\Capsule;
#use WHMCS\Smarty ;
$params['serviceid'] = $_POST['id'];
$params['userid'] = $_SESSION['uid'];
$params['os_select'] = $_POST['os_select'];
$params['osid_select'] = $_POST['osid_select'];
$params['configoption3'] = $_POST['configoption3'];
$params['pid'] = $_POST['pid'];
$params['token'] = $_POST['token'];

if(isset($_POST['check'])){
	if ($_POST['check'] == "checkstatus"){

		$status = onecontrol_checkVMStatus($params);
		// if ($status['vm_state'] == "POWEROFF"){
	  // 	$statusbulb = "red";
		// }elseif ($status['vm_state'] == "ACTIVE"){
	  // 	$statusbulb == "green";
	  // }else{
		//   $statusbulb == "inprogress";
		// }
		echo $status['vm_state'];

	} else {
	  echo '<p style="color:red;"> Oops! An error in status! </p>';
	}
} elseif(isset($_POST['a'])){
	if($_POST['a'] == "PowerOff"){
		onecontrol_poweroff($params);
	} elseif($_POST['a'] == "PowerOffHard"){
		onecontrol_PowerOffHard($params);
	} elseif($_POST['a'] == "RebootHard"){
		onecontrol_RebootHard($params);
	} elseif($_POST['a'] == "Reboot"){
		onecontrol_Reboot($params);
	} elseif($_POST['a'] == "ClientResume"){
		onecontrol_resume($params);
	} elseif($_POST['a'] == "ClientRebuild"){
		onecontrol_ClientRebuild($params);
	} else {
		echo '<p style="color:red;"> Oops! The command is not right! </p>';
	}
} else {
    echo '<p style="color:red;"> Oops! Something went wrong! </p>';
}
