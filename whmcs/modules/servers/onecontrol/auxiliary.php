<?php
//Put me to whmcs root directory

require_once('./modules/servers/onecontrol/getvmstate.php');

if(isset($_GET['id']) && $_GET['token'] && $_GET['a']=="get_vm_state") {
    echo get_vm_state($_GET['id'], $_GET['token']);
} else {
    echo '<p style="color:red;"> Oops! Something went wrong! </p>';
}    

?>