<?php

namespace WHMCS\Module\Addon\OneControl\Admin;

/**
 * Sample Admin Area Controller
 */
class Controller {

    /**
     * Index action.
     *
     * @param array $vars Module configuration parameters
     *
     * @return string
     */
    public function index($vars)
    {
        // Get common module parameters
        $modulelink = $vars['modulelink']; // eg. addonmodules.php?module=addonmodule
        $version = $vars['version']; // eg. 1.0
        $LANG = $vars['_lang']; // an array of the currently loaded language variables

        // Get module configuration parameters
        $one_ip_address = $vars['one_ip_address'];
        $one_tcp_port = $vars['one_tcp_port'];
        $one_user_password_strong = $vars['one_user_password_strong'];
        $one_user_password_length = $vars['one_user_password_length'];

        return <<<EOF
<!--
<p>The currently installed version is: <strong>{$version}</strong></p>

<p>Values of the configuration field are as follows:</p>

<blockquote>
    ONE IP address: {$one_ip_address}<br>
    ONE TCP port: {$one_tcp_port}<br>
    ONE user password strong: {$one_user_password_strong}<br>
    ONE user password length: {$one_user_password_length}<br>
</blockquote>

<p>
    <a href="{$modulelink}&action=show" class="btn btn-success">
        <i class="fa fa-check"></i>
        Show VM's options config
    </a>
        <a href="{$modulelink}&action=vmconfig" class="btn btn-success">
        <i class="fa fa-check"></i>
        VM's options config
    </a>
    
    <a href="{$modulelink}&action=invalid" class="btn btn-default">
        <i class="fa fa-times"></i>
        Visit invalid action link
    </a>
    
</p>
-->

<form method="post" action="addonmodules.php?module=onecontrol">
<table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
    <tbody><tr>
        <td width="20%" class="fieldlabel">Transaction ID</td>
        <td class="fieldarea">
            <input type="text" name="transid" class="form-control input-225" value="">
        </td>
    </tr>
    <tr>
        <td width="20%" class="fieldlabel">Date Range</td>
        <td class="fieldarea">
            <div class="form-group date-picker-prepend-icon">
                <label for="inputRange" class="field-icon">
                    <i class="fal fa-calendar-alt"></i>
                </label>
                <input id="inputRange" type="text" name="range" value="07/14/2020 - 08/12/2020" class="form-control date-picker-search">
            </div>
        </td>
    </tr>
    <tr>
        <td width="20%" class="fieldlabel">Email</td>
        <td class="fieldarea">
            <input type="text" name="email" class="form-control input-225" value="">
        </td>
    </tr>
    <tr>
        <td width="20%" class="fieldlabel">Receipt ID</td>
        <td class="fieldarea">
            <input type="text" name="receiptid" class="form-control input-225" value="">
        </td>
    </tr>
</tbody></table>
<div class="btn-container">
    <input type="submit" value="Search" class="btn btn-primary">
</div>
</form>
EOF;
    }

    /**
     * Show action.
     *
     * @param array $vars Module configuration parameters
     *
     * @return string
     */
    public function vmconfig($vars)
    {
        // Get common module parameters
        $modulelink = $vars['modulelink']; // eg. addonmodules.php?module=addonmodule
        $version = $vars['version']; // eg. 1.0
        $LANG = $vars['_lang']; // an array of the currently loaded language variables

        // Get module configuration parameters
        $one_ip_address = $vars['one_ip_address'];
        $one_tcp_port = $vars['one_tcp_port'];
        $one_user_password_strong = $vars['one_user_password_strong'];
        $one_user_password_length = $vars['one_user_password_length'];
        
        return <<<EOF

<h2>VM's config</h2>

<p>This is the <em>show</em> action output of the sample addon module.</p>

<p>The currently installed version is: <strong>{$version}</strong></p>

<p>
    <a href="{$modulelink}" class="btn btn-info">
        <i class="fa fa-arrow-left"></i>
        Back to home
    </a>
</p>

EOF;
    }

    /**
     * Show action.
     *
     * @param array $vars Module configuration parameters
     *
     * @return string
     */
    public function show($vars)
    {
        // Get common module parameters
        $modulelink = $vars['modulelink']; // eg. addonmodules.php?module=addonmodule
        $version = $vars['version']; // eg. 1.0
        $LANG = $vars['_lang']; // an array of the currently loaded language variables

        // Get module configuration parameters
        $one_ip_address = $vars['one_ip_address'];
        $one_tcp_port = $vars['one_tcp_port'];
        $one_user_password_strong = $vars['one_user_password_strong'];
        $one_user_password_length = $vars['one_user_password_length'];
        
        return <<<EOF

<h2>Show</h2>

<p>This is the <em>show</em> action output of the sample addon module.</p>

<p>The currently installed version is: <strong>{$version}</strong></p>

<p>
    <a href="{$modulelink}" class="btn btn-info">
        <i class="fa fa-arrow-left"></i>
        Back to home
    </a>
</p>

EOF;

    }
 
}
