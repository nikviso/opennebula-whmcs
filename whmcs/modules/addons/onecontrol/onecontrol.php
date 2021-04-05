<?php
/**
 * WHMCS SDK Sample Addon Module
 *
 * An addon module allows you to add additional functionality to WHMCS. It
 * can provide both client and admin facing user interfaces, as well as
 * utilise hook functionality within WHMCS.
 *
 * This sample file demonstrates how an addon module for WHMCS should be
 * structured and exercises all supported functionality.
 *
 * Addon Modules are stored in the /modules/addons/ directory. The module
 * name you choose must be unique, and should be all lowercase, containing
 * only letters & numbers, always starting with a letter.
 *
 * Within the module itself, all functions must be prefixed with the module
 * filename, followed by an underscore, and then the function name. For this
 * example file, the filename is "addonmodule" and therefore all functions
 * begin "addonmodule_".
 *
 * For more information, please refer to the online documentation.
 *
 * @see https://developers.whmcs.com/addon-modules/
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */

/**
 * Require any libraries needed for the module to function.
 * require_once __DIR__ . '/path/to/library/loader.php';
 *
 * Also, perform any initialization required by the service's library.
 */

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\OneControl\Admin\AdminDispatcher;
use WHMCS\Module\Addon\OneControl\Client\ClientDispatcher;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Define addon module configuration parameters.
 *
 * Includes a number of required system fields including name, description,
 * author, language and version.
 *
 * Also allows you to define any configuration parameters that should be
 * presented to the user when activating and configuring the module. These
 * values are then made available in all module function calls.
 *
 * Examples of each and their possible configuration parameters are provided in
 * the fields parameter below.
 *
 * @return array
 */
    
function onecontrol_config()
{
    return [
        // Display name for your module
        'name' => 'Open Nebula Control(R)',
        // Description displayed within the admin interface
        'description' => 'Open Nebula Control(R)',
        // Module author name
        'author' => 'A.S.Pushkin',
        // Default language
        'language' => 'english',
        // Version number
        'version' => '1.0',
        'fields' => [
            'one_ip_address' => [
                'FriendlyName' => 'ONE IP address',
                'Type' => 'text',
                'Size' => '16',
                'Default' => '',
                'Description' => 'Enter Opennebula IP address here',
            ],
            'one_tcp_port' => [
                'FriendlyName' => 'ONE TCP port',
                'Type' => 'text',
                'Size' => '5',
                'Default' => '',
                'Description' => 'Enter Opennebula TCP port here',
            ],
            'one_user_password_length' => [
                'FriendlyName' => 'ONE user password length',
                'Type' => 'text',
                'Size' => '2',
                'Default' => '',
                'Description' => 'Enter Opennebula user password length',
            ],
            'one_user_password_strong' => [
                'FriendlyName' => 'ONE user password strong',
                'Type' => 'yesno',
                'Description' => 'Tick to enable Opennebula user password strong',
            ],
            'request_retries' => [
                'FriendlyName' => 'ONE request retries',
                'Type' => 'text',
                'Size' => '1',
                'Default' => '3',
                'Description' => 'Before we abandon',
            ],            
            'request_timeout' => [
                'FriendlyName' => 'ZMQ request timeout',
                'Type' => 'text',
                'Size' => '5',
                'Default' => '20000',
                'Description' => 'msecs, (> 1000!)',
            ],
            'cipher' => [
                'FriendlyName' => 'AES encryption method',
                'Type' => 'text',
                'Size' => '64',
                'Default' => 'AES-256-CBC',
                'Description' => 'Enter AES encryption method',
            ],
            'key_file' => [
                'FriendlyName' => 'AES key file path',
                'Type' => 'text',
                'Size' => '256',
                'Default' => '',
                'Description' => 'Enter AES key file path',
            ],
        ]
    ];
}

/**
 * Activate.
 *
 * Called upon activation of the module for the first time.
 * Use this function to perform any database and schema modifications
 * required by your module.
 *
 * This function is optional.
 *
 * @see https://developers.whmcs.com/advanced/db-interaction/
 *
 * @return array Optional success/failure message
 */
function onecontrol_activate()
{
    // Create custom tables and schema required by your module
    try {
        Capsule::schema()
            ->create(
                'mod_onecontrol_onevm',
                function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->increments('id');
                    $table->integer('service_id');
                    $table->integer('user_id');
                    $table->integer('vm_id');
                    $table->text('vm_name');
                    $table->text('vm_ip_address');
                    $table->text('vm_network_mask');
                    $table->text('vm_network_address');
                    $table->text('vm_gw_ip_address');                    
                    $table->text('vm_dns_ip_address');
                    $table->integer('one_network_id');
                    $table->text('vm_token');
                    $table->boolean('vm_active');
                    $table->text('vm_user');
                    $table->text('vm_user_password');
                    $table->text('vm_root_password');
                 }
            );
        Capsule::schema()
            ->create(
                'mod_onecontrol_oneuser',
                function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->increments('id');
                    $table->integer('whmcs_user_id');
                    $table->integer('one_user_id');
                    $table->integer('one_user_group_id');
                    $table->text('one_user_name');
                    $table->text('one_user_password');
                    $table->text('one_user_token');
                }
            );
/*            
    Capsule::schema()
            ->create(
                'mod_onecontrol_config',
                function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
/*
                    $table->text('one_ip_address');
                    $table->text('one_tcp_port');
                    $table->integer('request_timeout');
                    $table->integer('request_retries');
                    $table->integer('one_user_password_length');
                    $table->boolean('one_user_password_strong');                    
                }
            );
*/            
    Capsule::schema()
            ->create(
                'mod_onecontrol_onetemplate',
                function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->increments('id');                    
                    $table->integer('one_template_id');
                    $table->integer('product_id');
                    $table->text('one_image_os');
                }
            );             
        return [
            // Supported values here include: success, error or info
            'status' => 'success',
            'description' => 'Open Nebula Control module activated.',
        ];
    } catch (\Exception $e) {
        return [
            // Supported values here include: success, error or info
            'status' => "error",
            'description' => 'Unable to create mod_oneaddon tables: ' . $e->getMessage(),
        ];
    }
}

/**
 * Deactivate.
 *
 * Called upon deactivation of the module.
 * Use this function to undo any database and schema modifications
 * performed by your module.
 *
 * This function is optional.
 *
 * @see https://developers.whmcs.com/advanced/db-interaction/
 *
 * @return array Optional success/failure message
 */
function onecontrol_deactivate()
{
    // Undo any database and schema modifications made by your module here
    try {
        Capsule::schema()
            ->dropIfExists('mod_onecontrol_onevm');
        Capsule::schema()
            ->dropIfExists('mod_onecontrol_oneuser');            
/*
        Capsule::schema()
            ->dropIfExists('mod_onecontrol_config'); 
*/
        Capsule::schema()
            ->dropIfExists('mod_onecontrol_onetemplate');             
        return [
            // Supported values here include: success, error or info
            'status' => 'success',
            'description' => 'Open Nebula Control module removed.',
        ];
    } catch (\Exception $e) {
        return [
            // Supported values here include: success, error or info
            "status" => "error",
            "description" => "Unable to drop mod_oneaddon tables: {$e->getMessage()}",
        ];
    }
}

/**
 * Upgrade.
 *
 * Called the first time the module is accessed following an update.
 * Use this function to perform any required database and schema modifications.
 *
 * This function is optional.
 *
 * @see https://laravel.com/docs/5.2/migrations
 *
 * @return void
 */
function onecontrol_upgrade($vars)
{
    $currentlyInstalledVersion = $vars['version'];

    /// Perform SQL schema changes required by the upgrade to version 1.1 of your module
    if ($currentlyInstalledVersion < 1.1) {
        $schema = Capsule::schema();
        // Alter the table and add a new text column called "demo2"
        $schema->table('mod_addonexample', function($table) {
            $table->text('demo2');
        });
    }

    /// Perform SQL schema changes required by the upgrade to version 1.2 of your module
    if ($currentlyInstalledVersion < 1.2) {
        $schema = Capsule::schema();
        // Alter the table and add a new text column called "demo3"
        $schema->table('mod_addonexample', function($table) {
            $table->text('demo3');
        });
    }
}

/**
 * Admin Area Output.
 *
 * Called when the addon module is accessed via the admin area.
 * Should return HTML output for display to the admin user.
 *
 * This function is optional.
 *
 * @see AddonModule\Admin\Controller::index()
 *
 * @return string
 */
function onecontrol_output($vars)
{
/*
    // Get common module parameters
    $modulelink = $vars['modulelink']; // eg. addonmodules.php?module=addonmodule
    $version = $vars['version']; // eg. 1.0
    $_lang = $vars['_lang']; // an array of the currently loaded language variables

    // Get module configuration parameters
    $one_ip_address = $vars['one_ip_address'];
    $one_tcp_port = $vars['one_tcp_port'];
    $one_user_password_strong = $vars['one_user_password_strong'];
    $one_user_password_length = $vars['one_user_password_length'];
*/
    // Dispatch and handle request here. What follows is a demonstration of one
    // possible way of handling this using a very basic dispatcher implementation.

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
    $vars_outside = isset($_REQUEST['vars']) ? $_REQUEST['vars'] : '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['uploadfile']))
    {
        $uploadfile = $_SERVER['DOCUMENT_ROOT'] . "/modules/addons/onecontrol/upload/vmset.txt";
         
        if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $uploadfile))
        {
          $vars += array("file_success" => "File is uploaded successfully");  
        }
        else {
          $vars += array("file_error" => "ERROR! File not uploaded!");
        }
    }    
    $vars = array_merge($vars, $vars_outside);
    

    $dispatcher = new AdminDispatcher();
    $response = $dispatcher->dispatch($action, $vars);
    echo $response;
}

/**
 * Admin Area Sidebar Output.
 *
 * Used to render output in the admin area sidebar.
 * This function is optional.
 *
 * @param array $vars
 *
 * @return string
 */
function onecontrol_sidebar($vars)
{
    // Get common module parameters
    $modulelink = $vars['modulelink'];
    $version = $vars['version'];
    $_lang = $vars['_lang'];

    return <<<EOF
<p>ONE addon</p>

EOF;

}

/**
 * Client Area Output.
 *
 * Called when the addon module is accessed via the client area.
 * Should return an array of output parameters.
 *
 * This function is optional.
 *
 * @see AddonModule\Client\Controller::index()
 *
 * @return array
 */

function onecontrol_clientarea($vars)
{
    // Get common module parameters
    $modulelink = $vars['modulelink']; // eg. index.php?m=addonmodule
    $version = $vars['version']; // eg. 1.0
    $_lang = $vars['_lang']; // an array of the currently loaded language variables

    // Get module configuration parameters
    $configTextField = $vars['Text Field Name'];
    $configPasswordField = $vars['Password Field Name'];
    $configCheckboxField = $vars['Checkbox Field Name'];
    $configDropdownField = $vars['Dropdown Field Name'];
    $configRadioField = $vars['Radio Field Name'];
    $configTextareaField = $vars['Textarea Field Name'];

    /**
     * Dispatch and handle request here. What follows is a demonstration of one
     * possible way of handling this using a very basic dispatcher implementation.
     */

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

    $dispatcher = new ClientDispatcher();
    return $dispatcher->dispatch($action, $vars);
}
