<?php
//Put me to whmcs root directory

require_once('./modules/servers/onecontrol/getvmstate.php');

if(isset($_GET['id']) && $_GET['a']=="get_vm_state")
{
   echo get_vm_state($_GET['id']);
}    

?>