<?php
/**
 * WHMCS SDK Sample Provisioning Module Hooks File
 *
 * Hooks allow you to tie into events that occur within the WHMCS application.
 *
 * This allows you to execute your own code in addition to, or sometimes even
 * instead of that which WHMCS executes by default.
 *
 * WHMCS recommends as good practice that all named hook functions are prefixed
 * with the keyword "hook", followed by your module name, followed by the action
 * of the hook function. This helps prevent naming conflicts with other addons
 * and modules.
 *
 * For every hook function you create, you must also register it with WHMCS.
 * There are two ways of registering hooks, both are demonstrated below.
 *
 * @see https://developers.whmcs.com/hooks/
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license https://www.whmcs.com/license/ WHMCS Eula
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// Require any libraries needed for the module to function.
// require_once __DIR__ . '/path/to/library/loader.php';
//
// Also, perform any initialization required by the service's library.

use WHMCS\Database\Capsule;

/**
 * Client edit sample hook function.
 *
 * This sample demonstrates making a service call whenever a change is made to a
 * client profile within WHMCS.
 *
 * @param array $params Parameters dependant upon hook function
 *
 * @return mixed Return dependant upon hook function
 */
function hook_onecontrol_clientedit(array $params)
{
    try {
        // Call the service's function, using the values provided by WHMCS in
        // `$params`.
    } catch (Exception $e) {
        // Consider logging or reporting the error.
    }
}

/**
 * Register a hook with WHMCS.
 *
 * add_hook(string $hookPointName, int $priority, string|array|Closure $function)
 */
 
add_hook('ClientEdit', 1, 'hook_onecontrol_clientedit');

/**
 * Insert a service item to the client area navigation bar.
 *
 * Demonstrates adding an additional link to the Services navbar menu that
 * provides a shortcut to a filtered products/services list showing only the
 * products/services assigned to the module.
 *
 * @param \WHMCS\View\Menu\Item $menu
 */
/* 
add_hook('ClientAreaPrimaryNavbar', 1, function ($menu)
{
    // Check whether the services menu exists.
    if (!is_null($menu->getChild('Services'))) {
        // Add a link to the module filter.
        $menu->getChild('Services')
            ->addChild(
                'Provisioning Module Products_servers_hook',
                array(
                    'uri' => 'clientarea.php?action=services&module=onecontrol',
                    'order' => 15,
                )
            );
    }
});
*/
/**
 * Render a custom sidebar panel in the secondary sidebar.
 *
 * Demonstrates the creation of an additional sidebar panel on any page where
 * the My Services Actions default panel appears and populates it with a title,
 * icon, body and footer html output and a child link.  Also sets it to be in
 * front of any other panels defined up to this point.
 *
 * @param \WHMCS\View\Menu\Item $secondarySidebar
 */
/* 
add_hook('ClientAreaSecondarySidebar', 1, function ($secondarySidebar)
{
    // determine if we are on a page containing My Services Actions
    if (!is_null($secondarySidebar->getChild('My Services Actions'))) {

        // define new sidebar panel
        $customPanel = $secondarySidebar->addChild('Provisioning Module Sample Panel');

        // set panel attributes
        $customPanel->moveToFront()
            ->setIcon('fa-user')
            ->setBodyHtml(
                'Your HTML output goes here...'
            )
            ->setFooterHtml(
                'Footer HTML can go here...'
            );

        // define link
        $customPanel->addChild(
                'Sample Link Menu Item',
                array(
                    'uri' => 'clientarea.php?action=services&module=onecontrol',
                    'icon'  => 'fa-list-alt',
                    'order' => 2,
                )
            );

    }
});

*/

/*
* Remove Sidebars
*/
add_hook('ClientAreaPrimarySidebar', 1, function($primarySidebar)
{
/*     
   if (!is_null($primarySidebar->getChild('Service Details Overview'))) {
            $primarySidebar->removeChild('Service Details Overview');
   }
*/  
   if (!is_null($primarySidebar->getChild('Service Details Actions'))) {
            $primarySidebar->removeChild('Service Details Actions');
   }    

});

/*
*
*Runs prior to any templated email being sent.
*
*/
add_hook('EmailPreSend', 1, function($vars) {
    
    $result_onevm = Capsule::table('mod_onecontrol_onevm')
                    ->select('vm_user','vm_user_password','vm_root_password')
                    ->where('service_id',$vars[mergefields][service_id])
                    ->first();
               
    if($result_onevm){
        $merge_fields = array(
            "vm_user" => $result_onevm->{'vm_user'},
            "vm_user_password" => $result_onevm->{'vm_user_password'},
            "vm_root_password" => $result_onevm->{'vm_root_password'},
        );
    }
    
    Capsule::table('mod_onecontrol_onevm')
        ->where('service_id',$vars[mergefields][service_id])
        ->update(
            array(
                'vm_user'=>'sent',
                'vm_user_password'=>'sent',
                'vm_root_password'=>'sent',                        
                )
        );

    return $merge_fields;     
});
