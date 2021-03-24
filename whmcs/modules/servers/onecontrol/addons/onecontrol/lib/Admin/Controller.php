<?php

namespace WHMCS\Module\Addon\OneControl\Admin;

use WHMCS\Database\Capsule;

/**
 * Sample Admin Area Controller
 */
class Controller {

  protected function check_add_data_os($vars)
  {
      $return_message = '';

       $vars['os'] ? : $return_message = $return_message . '"VM NAME" is empty.<br>';
       $vars['pid'] ? : $return_message = $return_message . '"VM NAME" is empty.<br>';
       $vars['on_tid'] ? : $return_message = $return_message . '"VM NAME" is empty.<br>';
      // inet_pton($vars['os']) ? : $return_message = $return_message . '"Operating System" is empty or is not IP address.<br>';
      // inet_pton($vars['pid']) ? : $return_message = $return_message . '"Product ID" is empty or is not IP address.<br>';
      // inet_pton($vars['on_tid']) ? : $return_message = $return_message . '"ON Template" is empty or is not IP address.<br>';

      // if ($vars['one_network_id'] == "")
      // {
      //     $return_message = $return_message . '"ONE NETWORK ID" is empty.<br>';
      // }

      $os = $vars['os'];
      $pid = $vars['pid'];
      $on_tid = $vars['on_tid'];

      try {
          $vm[] = Capsule::table('on_templates')
              ->select('os','pid','on_tid')
              ->Where('os','=', $os)
              ->Where('pid','=', $pid)
              ->Where('on_tid','=', $on_tid)
              ->get();
          if ($vm)
          {
              foreach($vm as $key => $value)
              {
                 foreach ($value as $key2 => $value2)
                 {
                  $return_message = $return_message . 'OS with such data already exists: ' . $value2->OS ." => ". $value2->pid ." => ". $value2->on_tid ."<br>";
                 }
              }
          }
      } catch (\Exception $e) {
          $return_message = $e->getMessage();
      }

  return $return_message;

  }

  protected function check_update_data($vars)
  {
      $return_message = '';

      $vars['vm_name'] ? : $return_message = $return_message . '"VM NAME" is empty.<br>';
      inet_pton($vars['vm_ip_address']) ? : $return_message = $return_message . '"VM IP ADDRESS" is empty or is not IP address.<br>';
      inet_pton($vars['vm_network_mask']) ? : $return_message = $return_message . '"VM NETWORK MASK" is empty or is not IP address.<br>';
      inet_pton($vars['vm_network_address']) ? : $return_message = $return_message . '"VM NETWORK ADDRESS" is empty or is not IP address.<br>';
      inet_pton($vars['vm_gw_ip_address']) ? : $return_message = $return_message . '"VM GATEWAY ADDRESS" is empty or is not IP address.<br>';
      inet_pton($vars['vm_dns_ip_address']) ? : $return_message = $return_message . '"VM DNS ADDRESS" is empty or is not IP address.<br>';
      if ($vars['one_network_id'] == "")
      {
          $return_message = $return_message . '"ONE NETWORK ID" is empty.<br>';
      }

      $vm_name = $vars['vm_name'];
      $vm_ip_address = $vars['vm_ip_address'];


  return $return_message;

  }

    protected function check_add_data($vars)
    {
        $return_message = '';

        $vars['vm_name'] ? : $return_message = $return_message . '"VM NAME" is empty.<br>';
        inet_pton($vars['vm_ip_address']) ? : $return_message = $return_message . '"VM IP ADDRESS" is empty or is not IP address.<br>';
        inet_pton($vars['vm_network_mask']) ? : $return_message = $return_message . '"VM NETWORK MASK" is empty or is not IP address.<br>';
        inet_pton($vars['vm_network_address']) ? : $return_message = $return_message . '"VM NETWORK ADDRESS" is empty or is not IP address.<br>';
        inet_pton($vars['vm_gw_ip_address']) ? : $return_message = $return_message . '"VM GATEWAY ADDRESS" is empty or is not IP address.<br>';
        inet_pton($vars['vm_dns_ip_address']) ? : $return_message = $return_message . '"VM DNS ADDRESS" is empty or is not IP address.<br>';
        if ($vars['one_network_id'] == "")
        {
            $return_message = $return_message . '"ONE NETWORK ID" is empty.<br>';
        }

        $vm_name = $vars['vm_name'];
        $vm_ip_address = $vars['vm_ip_address'];

        try {
            $vm[] = Capsule::table('mod_onecontrol_onevm')
                ->select('vm_name','vm_ip_address')
                ->Where('vm_name','=', $vm_name)
                ->orWhere('vm_ip_address','=', $vm_ip_address)
                ->get();
            if ($vm)
            {
                foreach($vm as $key => $value)
                {
                   foreach ($value as $key2 => $value2)
                   {
                    $return_message = $return_message . 'VM with such data already exists: ' . $value2->vm_name ." => ". $value2->vm_ip_address ."<br>";
                   }
                }
            }
        } catch (\Exception $e) {
            $return_message = $e->getMessage();
        }

    return $return_message;

    }

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

        $html = '';
        $data_vm = Capsule::table('mod_onecontrol_onevm')
        ->select('*')
        ->get();

        foreach ($data_vm as $data)
        {
         $html .= '<tr>
                     <td>'. $data->vm_name .'</td>
                     <td>'. $data->vm_ip_address .'</td>
                     <td>'. $data->vm_network_mask .'</td>
                     <td>'. $data->vm_network_address .'</td>
                     <td>'. $data->vm_gw_ip_address .'</td>
                     <td>'. $data->vm_dns_ip_address .'</td>
                     <td>'. $data->one_network_id .'</td>
                     <td>'. $data->vm_active .'</td>
                   </tr>';
         //do something with myid and unique column
        }

        $html2 = '';
        $data_os = Capsule::table('on_templates')
        ->select('*')
        ->get();

        foreach ($data_os as $data)
        {
         $html2 .= '<tr>
                     <td>'. $data->id .'</td>
                     <td>'. $data->os .'</td>
                     <td>'. $data->pid .'</td>
                     <td>'. $data->on_tid .'</td>
                   </tr>';
         //do something with myid and unique column
        }


        $parse = new Template_Parse;
        $parse->get_tpl("/var/www/html/www.clouds365.host/whmcs/modules/addons/onecontrol/templates/admin.tpl");
        $parse->set_tpl('{VMID}', $vmid);
        $parse->set_tpl('{MODULELINK}', $modulelink);
        $parse->set_tpl('{VERSION}', $version);
        $parse->set_tpl('{SHOWTABDEF}', 'tabShow');
        $parse->set_tpl('{CONFIGTABDEF}', '');
        $parse->set_tpl('{CLEARTABDEF}', '');
        $parse->set_tpl('{OSTABDEF}', '');
        $parse->set_tpl('{DELTABDEF}', '');
        $parse->set_tpl('{VM_NAME}', '');
        $parse->set_tpl('{VM_IP_ADDRESS}', '');
        $parse->set_tpl('{VM_NETWORK_MASK}', '');
        $parse->set_tpl('{VM_NETWORK_ADDRESS}', '');
        $parse->set_tpl('{VM_GW_IP_ADDRESS}', '');
        $parse->set_tpl('{VM_DNS_IP_ADDRESS}', '');
        $parse->set_tpl('{ONE_NETWORK_ID}', '');
        $parse->set_tpl('{FEEDBACK_FILE_MESSAGE}', '');
        $parse->set_tpl('{FEEDBACK_FILE_MESSAGE_CLASS}', '');
        $parse->set_tpl('{FEEDBACK_MESSAGE}', '');
        $parse->set_tpl('{FEEDBACK_MESSAGE_CLASS}', '');
        $parse->set_tpl('{VM}', $html);
        $parse->set_tpl('{OS}', $html2);
        $from_file ? $parse->set_tpl('{checked}', 'checked') : '';
        $parse->tpl_parse();

        return $parse->template;
    }

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
        $vmid = $vars['vmid'];
        /*
        $tpl =  file_get_contents(/home2/lappaeu/aleksale/clouds365/whmcs/modules/addons/onecontrol/templates/admin.tpl");
        $tpl = str_replace("{vmid}", $vmid, $tpl);
        return $tpl;
        */

        $parse = new Template_Parse;
        $parse->get_tpl("/var/www/html/www.clouds365.host/whmcs/modules/addons/onecontrol/templates/admin.tpl");
        $parse->set_tpl('{VMID}', $vmid);
        $parse->set_tpl('{MODULELINK}', $modulelink);
        $parse->set_tpl('{VERSION}', $version);
        $parse->set_tpl('{SHOWTABDEF}', 'tabShow');
        $parse->set_tpl('{CONFIGTABDEF}', '');
        $parse->set_tpl('{CLEARTABDEF}', '');
        $parse->set_tpl('{DELTABDEF}', '');
        $parse->set_tpl('{VM_NAME}', '');
        $parse->set_tpl('{VM_IP_ADDRESS}', '');
        $parse->set_tpl('{VM_NETWORK_MASK}', '');
        $parse->set_tpl('{VM_NETWORK_ADDRESS}', '');
        $parse->set_tpl('{VM_GW_IP_ADDRESS}', '');
        $parse->set_tpl('{VM_DNS_IP_ADDRESS}', '');
        $parse->set_tpl('{ONE_NETWORK_ID}', '');
        $parse->set_tpl('{FEEDBACK_FILE_MESSAGE}', '');
        $parse->set_tpl('{FEEDBACK_FILE_MESSAGE_CLASS}', '');
        $parse->set_tpl('{FEEDBACK_MESSAGE}', '');
        $parse->set_tpl('{FEEDBACK_MESSAGE_CLASS}', '');
        $parse->tpl_parse();

        return $parse->template;

    }


    public function del($vars){
      $vars['vm_name'] = $vars['vm_name'];
      $vars['vm_ip_address'] = $vars['vm_ip_address'];
      $vars['osid'] = $vars['vm_network_mask'];
      $vars['osid'] = $vars['vm_network_address'];
      $vars['osid'] = $vars['vm_gw_ip_address'];
      $vars['osid'] = $vars['vm_dns_ip_address'];
      $vars['osid'] = $vars['one_network_id'];

      $pdo = Capsule::connection()->getPdo();
      $pdo->beginTransaction();

      try {
          $statement = $pdo->prepare(
              'delete from mod_onecontrol_onevm
              where
                vm_name = :vm_name and
                vm_ip_address = :vm_ip_address and
                vm_network_mask = :vm_network_mask and
                vm_network_address = :vm_network_address and
                vm_gw_ip_address = :vm_gw_ip_address and
                vm_dns_ip_address = :vm_dns_ip_address and
                one_network_id = :one_network_id
              '
          );

          $statement->execute(
              [
                  ':vm_name' => $vars['vm_name'],
                  ':vm_ip_address' => $vars['vm_ip_address'],
                  ':vm_network_mask' => $vars['vm_network_mask'],
                  ':vm_network_address' => $vars['vm_network_address'],
                  ':vm_gw_ip_address' => $vars['vm_gw_ip_address'],
                  ':vm_dns_ip_address' => $vars['vm_dns_ip_address'],
                  ':one_network_id' => $vars['one_network_id'],
              ]
          );

          $pdo->commit();
      } catch (\Exception $e) {
          $feedback_message = $e->getMessage();
          $feedback_message_class = "alert alert-danger";
          $pdo->rollBack();
      }

      $html = '';
      $data_vm = Capsule::table('mod_onecontrol_onevm')
      ->select('*')
      ->get();

      foreach ($data_vm as $data)
      {
       $html .= '<tr>
                   <td>'. $data->vm_name .'</td>
                   <td>'. $data->vm_ip_address .'</td>
                   <td>'. $data->vm_network_mask .'</td>
                   <td>'. $data->vm_network_address .'</td>
                   <td>'. $data->vm_gw_ip_address .'</td>
                   <td>'. $data->vm_dns_ip_address .'</td>
                   <td>'. $data->one_network_id .'</td>
                   <td>'. $data->vm_active .'</td>
                 </tr>';
       //do something with myid and unique column
      }

      $html2 = '';
      $data_os = Capsule::table('on_templates')
      ->select('*')
      ->get();

      foreach ($data_os as $data)
      {
       $html2 .= '<tr>
                   <td>'. $data->id .'</td>
                   <td>'. $data->os .'</td>
                   <td>'. $data->pid .'</td>
                   <td>'. $data->on_tid .'</td>
                 </tr>';
       //do something with myid and unique column
      }


      $parse = new Template_Parse;
      $parse->get_tpl("/var/www/html/www.clouds365.host/whmcs/modules/addons/onecontrol/templates/admin.tpl");
      $parse->set_tpl('{VMID}', $vmid);
      $parse->set_tpl('{MODULELINK}', $modulelink);
      $parse->set_tpl('{VERSION}', $version);
      $parse->set_tpl('{SHOWTABDEF}', 'tabShow');
      $parse->set_tpl('{CONFIGTABDEF}', '');
      $parse->set_tpl('{VM_NAME}', $vm_name);
      $parse->set_tpl('{VM_IP_ADDRESS}', $vm_ip_address);
      $parse->set_tpl('{VM_NETWORK_MASK}', $vm_network_mask);
      $parse->set_tpl('{VM_NETWORK_ADDRESS}', $vm_network_address);
      $parse->set_tpl('{VM_GW_IP_ADDRESS}', $vm_gw_ip_address);
      $parse->set_tpl('{VM_DNS_IP_ADDRESS}', $vm_dns_ip_address);
      $parse->set_tpl('{ONE_NETWORK_ID}', $one_network_id);
      $parse->set_tpl('{FEEDBACK_FILE_MESSAGE}', $feedback_file_message);
      $parse->set_tpl('{FEEDBACK_FILE_MESSAGE_CLASS}', $feedback_file_message_class);
      $parse->set_tpl('{FEEDBACK_MESSAGE}', $feedback_message);
      $parse->set_tpl('{FEEDBACK_MESSAGE_CLASS}', $feedback_message_class);
      $parse->set_tpl('{VM}', $html);
      $parse->set_tpl('{OS}', $html2);
      //$from_file ? $parse->set_tpl('{checked}', 'checked') : '';
      $parse->tpl_parse();

      return $parse->template;

    }

    public function update($vars){

      $feedback_message = $this->check_update_data($vars);

      $vm_name = $vars['vm_name'];
      $vm_ip_address = $vars['vm_ip_address'];
      $vm_network_mask = $vars['vm_network_mask'];
      $vm_network_address = $vars['vm_network_address'];
      $vm_gw_ip_address = $vars['vm_gw_ip_address'];
      $vm_dns_ip_address = $vars['vm_dns_ip_address'];
      $one_network_id = $vars['one_network_id'];
      $vm_active = $vars['vm_active'];

      $pdo = Capsule::connection()->getPdo();
      $pdo->beginTransaction();


      $html = '';
      $data_vmv = Capsule::table('mod_onecontrol_onevm')
      ->select('*')
      ->get();

      foreach ($data_vmv as $data)
      {
       $html .= '<tr>
                   <td>'. $data->vm_name .'</td>
                   <td>'. $data->vm_ip_address .'</td>
                   <td>'. $data->vm_network_mask .'</td>
                   <td>'. $data->vm_network_address .'</td>
                   <td>'. $data->vm_gw_ip_address .'</td>
                   <td>'. $data->vm_dns_ip_address .'</td>
                   <td>'. $data->one_network_id .'</td>
                   <td>'. $data->vm_active .'</td>
                 </tr>';
       //do something with myid and unique column
      }

      $data_vm = Capsule::table('mod_onecontrol_onevm')
        ->Where('vm_name','=', $vm_name)
        ->update([

          'vm_name' => $vm_name,
          'vm_ip_address' => $vm_ip_address,
          'vm_network_mask' => $vm_network_mask,
          'vm_network_address' => $vm_network_address,
          'vm_gw_ip_address' => $vm_gw_ip_address,
          'vm_dns_ip_address' => $vm_dns_ip_address,
          'one_network_id' => $one_network_id,
          'vm_active' => $vm_active,
      ]);

      $html2 = '';
      $data_os = Capsule::table('on_templates')
      ->select('*')
      ->get();

      foreach ($data_os as $data)
      {
       $html2 .= '<tr>
                   <td>'. $data->id .'</td>
                   <td>'. $data->os .'</td>
                   <td>'. $data->pid .'</td>
                   <td>'. $data->on_tid .'</td>
                 </tr>';
       //do something with myid and unique column
      }


      $parse = new Template_Parse;
      $parse->get_tpl("/var/www/html/www.clouds365.host/whmcs/modules/addons/onecontrol/templates/admin.tpl");
      $parse->set_tpl('{VMID}', $vmid);
      $parse->set_tpl('{MODULELINK}', $modulelink);
      $parse->set_tpl('{VERSION}', $version);
      $parse->set_tpl('{EDITTABDEF}', 'tabShow');
      $parse->set_tpl('{CONFIGTABDEF}', '');
      $parse->set_tpl('{VM_NAME}', $vm_name);
      $parse->set_tpl('{VM_IP_ADDRESS}', $vm_ip_address);
      $parse->set_tpl('{VM_NETWORK_MASK}', $vm_network_mask);
      $parse->set_tpl('{VM_NETWORK_ADDRESS}', $vm_network_address);
      $parse->set_tpl('{VM_GW_IP_ADDRESS}', $vm_gw_ip_address);
      $parse->set_tpl('{VM_DNS_IP_ADDRESS}', $vm_dns_ip_address);
      $parse->set_tpl('{ONE_NETWORK_ID}', $one_network_id);
      $parse->set_tpl('{FEEDBACK_FILE_MESSAGE}', $feedback_file_message);
      $parse->set_tpl('{FEEDBACK_FILE_MESSAGE_CLASS}', $feedback_file_message_class);
      $parse->set_tpl('{FEEDBACK_MESSAGE}', $feedback_message);
      $parse->set_tpl('{FEEDBACK_MESSAGE_CLASS}', $feedback_message_class);
      $parse->set_tpl('{VM}', $html);
      $parse->set_tpl('{OS}', $html2);
      //$from_file ? $parse->set_tpl('{checked}', 'checked') : '';
      $parse->tpl_parse();
      return $parse->template;

    }



    public function edit($vars){


      $vm_name = $vars['vm_name'];

      $pdo = Capsule::connection()->getPdo();
      $pdo->beginTransaction();


      $html = '';
      $data_vm = Capsule::table('mod_onecontrol_onevm')
      ->select('*')
        ->Where('vm_name','=', $vm_name)
      ->get();

      foreach ($data_vm as $data)
      {
        $vm_ip_address = $data->vm_ip_address;
        $vm_network_mask = $data->vm_network_mask;
        $vm_network_address = $data->vm_network_address;
        $vm_gw_ip_address = $data->vm_gw_ip_address;
        $vm_dns_ip_address = $data->vm_dns_ip_address;
        $one_network_id = $data->one_network_id;
        $vm_active = $data->vm_active;

       //do something with myid and unique column
      }

      // $html2 = '';
      // $data_os = Capsule::table('on_templates')
      // ->select('*')
      // ->get();
      //
      // foreach ($data_os as $data)
      // {
      //  $html2 .= '<tr>
      //              <td>'. $data->id .'</td>
      //              <td>'. $data->os .'</td>
      //              <td>'. $data->pid .'</td>
      //              <td>'. $data->on_tid .'</td>
      //            </tr>';
      //  //do something with myid and unique column
      // }


      $parse = new Template_Parse;
      $parse->get_tpl("/var/www/html/www.clouds365.host/whmcs/modules/addons/onecontrol/templates/admin.tpl");
      $parse->set_tpl('{VMID}', $vmid);
      $parse->set_tpl('{MODULELINK}', $modulelink);
      $parse->set_tpl('{VERSION}', $version);
      $parse->set_tpl('{EDITTABDEF}', 'tabShow');
      $parse->set_tpl('{CONFIGTABDEF}', '');
      $parse->set_tpl('{VM_NAME}', $vm_name);
      $parse->set_tpl('{VM_IP_ADDRESS}', $vm_ip_address);
      $parse->set_tpl('{VM_NETWORK_MASK}', $vm_network_mask);
      $parse->set_tpl('{VM_NETWORK_ADDRESS}', $vm_network_address);
      $parse->set_tpl('{VM_GW_IP_ADDRESS}', $vm_gw_ip_address);
      $parse->set_tpl('{VM_DNS_IP_ADDRESS}', $vm_dns_ip_address);
      $parse->set_tpl('{ONE_NETWORK_ID}', $one_network_id);
      $parse->set_tpl('{FEEDBACK_FILE_MESSAGE}', $feedback_file_message);
      $parse->set_tpl('{FEEDBACK_FILE_MESSAGE_CLASS}', $feedback_file_message_class);
      $parse->set_tpl('{FEEDBACK_MESSAGE}', $feedback_message);
      $parse->set_tpl('{FEEDBACK_MESSAGE_CLASS}', $feedback_message_class);
      $parse->set_tpl('{VM}', $html);
      $parse->set_tpl('{OS}', $html2);
      //$from_file ? $parse->set_tpl('{checked}', 'checked') : '';
      $parse->tpl_parse();

      return $parse->template;

    }

    public function delos($vars){
      $vars['osid'] = strtolower(preg_replace('/\s*\t*/','',$vars['os_id']));
      $pdo = Capsule::connection()->getPdo();
      $pdo->beginTransaction();

      try {
          $statement = $pdo->prepare(
              'delete from on_templates
              where id = :osid'
          );

          $statement->execute(
              [
                  ':osid' => $vars['osid'],
              ]
          );

          $pdo->commit();
      } catch (\Exception $e) {
          $feedback_message = $e->getMessage();
          $feedback_message_class = "alert alert-danger";
          $pdo->rollBack();
      }

      $html = '';
      $data_vm = Capsule::table('mod_onecontrol_onevm')
      ->select('*')
      ->get();

      foreach ($data_vm as $data)
      {
       $html .= '<tr>
                   <td>'. $data->vm_name .'</td>
                   <td>'. $data->vm_ip_address .'</td>
                   <td>'. $data->vm_network_mask .'</td>
                   <td>'. $data->vm_network_address .'</td>
                   <td>'. $data->vm_gw_ip_address .'</td>
                   <td>'. $data->vm_dns_ip_address .'</td>
                   <td>'. $data->one_network_id .'</td>
                   <td>'. $data->vm_active .'</td>
                 </tr>';
       //do something with myid and unique column
      }

      $html2 = '';
      $data_os = Capsule::table('on_templates')
      ->select('*')
      ->get();

      foreach ($data_os as $data)
      {
       $html2 .= '<tr>
                   <td>'. $data->id .'</td>
                   <td>'. $data->os .'</td>
                   <td>'. $data->pid .'</td>
                   <td>'. $data->on_tid .'</td>
                 </tr>';
       //do something with myid and unique column
      }

      $parse = new Template_Parse;
      $parse->get_tpl("/var/www/html/www.clouds365.host/whmcs/modules/addons/onecontrol/templates/admin.tpl");
      $parse->set_tpl('{SHOWTABDEF}', '');
      $parse->set_tpl('{OSTABDEF}', 'tabShow');
      $parse->set_tpl('{CLEARTABDEF}', '');
      $parse->set_tpl('{DELTABDEF}', '');
      $parse->set_tpl('{VM_NAME}', $vm_name);
      $parse->set_tpl('{VM_IP_ADDRESS}', $vm_ip_address);
      $parse->set_tpl('{VM_NETWORK_MASK}', $vm_network_mask);
      $parse->set_tpl('{VM_NETWORK_ADDRESS}', $vm_network_address);
      $parse->set_tpl('{VM_GW_IP_ADDRESS}', $vm_gw_ip_address);
      $parse->set_tpl('{VM_DNS_IP_ADDRESS}', $vm_dns_ip_address);
      $parse->set_tpl('{ONE_NETWORK_ID}', $one_network_id);
      $parse->set_tpl('{FEEDBACK_FILE_MESSAGE}', $feedback_file_message);
      $parse->set_tpl('{FEEDBACK_FILE_MESSAGE_CLASS}', $feedback_file_message_class);
      $parse->set_tpl('{FEEDBACK_MESSAGE}', $feedback_message);
      $parse->set_tpl('{FEEDBACK_MESSAGE_CLASS}', $feedback_message_class);
      $parse->set_tpl('{VM}', $html);
      $parse->set_tpl('{OS}', $html2);
      //$from_file ? $parse->set_tpl('{checked}', 'checked') : '';
      $parse->tpl_parse();

      return $parse->template;

    }

    public function addos($vars)
    {
      $vars['os'] = $vars['os_name'];
      $vars['pid'] = preg_replace('/\s*\t*/','',$vars['os_id']);
      $vars['on_tid'] = preg_replace('/\s*\t*/','',$vars['os_template']);



      $feedback_message = $this->check_add_data_os($vars);
      $pdo = Capsule::connection()->getPdo();
      $pdo->beginTransaction();

      if (!$feedback_message)
      {
          try {
              $statement = $pdo->prepare(
                  'insert into on_templates (os, pid, on_tid)
                  values (:os, :pid, :on_tid)'
              );

              $statement->execute(
                  [
                      ':os' => $vars['os'],
                      ':pid' => $vars['pid'],
                      ':on_tid' => $vars['on_tid'],
                  ]
              );

              $pdo->commit();
          } catch (\Exception $e) {
              $feedback_message = $e->getMessage();
              $feedback_message_class = "alert alert-danger";
              $pdo->rollBack();
          }
      } else {
          $feedback_message_class = "alert alert-danger";
      }


      $html = '';
      $data_vm = Capsule::table('mod_onecontrol_onevm')
      ->select('*')
      ->get();

      foreach ($data_vm as $data)
      {
       $html .= '<tr>
                   <td>'. $data->vm_name .'</td>
                   <td>'. $data->vm_ip_address .'</td>
                   <td>'. $data->vm_network_mask .'</td>
                   <td>'. $data->vm_network_address .'</td>
                   <td>'. $data->vm_gw_ip_address .'</td>
                   <td>'. $data->vm_dns_ip_address .'</td>
                   <td>'. $data->one_network_id .'</td>
                   <td>'. $data->vm_active .'</td>
                 </tr>';
       //do something with myid and unique column
      }

      $html2 = '';
      $data_os = Capsule::table('on_templates')
      ->select('*')
      ->get();

      foreach ($data_os as $data)
      {
       $html2 .= '<tr>
                   <td>'. $data->id .'</td>
                   <td>'. $data->os .'</td>
                   <td>'. $data->pid .'</td>
                   <td>'. $data->on_tid .'</td>
                 </tr>';
       //do something with myid and unique column
      }

      $parse = new Template_Parse;
      $parse->get_tpl("/var/www/html/www.clouds365.host/whmcs/modules/addons/onecontrol/templates/admin.tpl");
      $parse->set_tpl('{SHOWTABDEF}', '');
      $parse->set_tpl('{ADDOSTABDEF}', 'tabShow');
      $parse->set_tpl('{CLEARTABDEF}', '');
      $parse->set_tpl('{DELTABDEF}', '');
      $parse->set_tpl('{VM_NAME}', $vm_name);
      $parse->set_tpl('{VM_IP_ADDRESS}', $vm_ip_address);
      $parse->set_tpl('{VM_NETWORK_MASK}', $vm_network_mask);
      $parse->set_tpl('{VM_NETWORK_ADDRESS}', $vm_network_address);
      $parse->set_tpl('{VM_GW_IP_ADDRESS}', $vm_gw_ip_address);
      $parse->set_tpl('{VM_DNS_IP_ADDRESS}', $vm_dns_ip_address);
      $parse->set_tpl('{ONE_NETWORK_ID}', $one_network_id);
      $parse->set_tpl('{FEEDBACK_FILE_MESSAGE}', $feedback_file_message);
      $parse->set_tpl('{FEEDBACK_FILE_MESSAGE_CLASS}', $feedback_file_message_class);
      $parse->set_tpl('{FEEDBACK_MESSAGE}', $feedback_message);
      $parse->set_tpl('{FEEDBACK_MESSAGE_CLASS}', $feedback_message_class);
      $parse->set_tpl('{VM}', $html);
      $parse->set_tpl('{OS}', $html2);
      //$from_file ? $parse->set_tpl('{checked}', 'checked') : '';
      $parse->tpl_parse();

      return $parse->template;
    }

    public function add($vars)
    {

        $vars['vm_name'] = strtolower(preg_replace('/\s*\t*/','',$vars['vm_name']));
        $vars['vm_ip_address'] = preg_replace('/\s*\t*/','',$vars['vm_ip_address']);
        $vars['vm_network_mask'] = preg_replace('/\s*\t*/','',$vars['vm_network_mask']);
        $vars['vm_network_address'] = preg_replace('/\s*\t*/','',$vars['vm_network_address']);
        $vars['vm_gw_ip_address'] = preg_replace('/\s*\t*/','',$vars['vm_gw_ip_address']);
        $vars['vm_dns_ip_address'] = preg_replace('/\s*\t*/','',$vars['vm_dns_ip_address']);
        $vars['one_network_id'] = preg_replace('/\s*\t*/','',$vars['one_network_id']);
        $from_file = $vars['from_file'];

        if (!$from_file) {

            $feedback_message = $this->check_add_data($vars);
            $pdo = Capsule::connection()->getPdo();
            $pdo->beginTransaction();

            if (!$feedback_message)
            {
                try {
                    $statement = $pdo->prepare(
                        'insert into mod_onecontrol_onevm (vm_name, vm_ip_address, vm_network_mask, vm_network_address, vm_gw_ip_address, vm_dns_ip_address, one_network_id)
                        values (:vm_name, :vm_ip_address, :vm_network_mask, :vm_network_address, :vm_gw_ip_address, :vm_dns_ip_address, :one_network_id)'
                    );

                    $statement->execute(
                        [
                            ':vm_name' => $vars['vm_name'],
                            ':vm_ip_address' => $vars['vm_ip_address'],
                            ':vm_network_mask' => $vars['vm_network_mask'],
                            ':vm_network_address' => $vars['vm_network_address'],
                            ':vm_gw_ip_address' => $vars['vm_gw_ip_address'],
                            ':vm_dns_ip_address' => $vars['vm_dns_ip_address'],
                            ':one_network_id' => $vars['one_network_id'],
                        ]
                    );

                    $pdo->commit();
                } catch (\Exception $e) {
                    $feedback_message = $e->getMessage();
                    $feedback_message_class = "alert alert-danger";
                    $pdo->rollBack();
                }
            } else {
                $feedback_message_class = "alert alert-danger";
            }
        } else {
            if (!$vars['file_error'])
            {
                $pdo = Capsule::connection()->getPdo();

                $feedback_file_message = $vars['file_success'];
                $feedback_file_message_class = "alert alert-success";

                $handle = @fopen("/var/www/html/www.clouds365.host/whmcs/modules/addons/onecontrol/upload/vmset.txt", "r");
                if ($handle) {
                    $str=1;
                    while (($buffer = fgets($handle)) !== false) {
                        $vars_db = array();
                        $vars_from_file = explode( ',', $buffer );

                        $vars_db += array("vm_name" => strtolower(preg_replace('/\s*\t*/','',$vars_from_file[0])));
                        $vars_db += array("vm_ip_address" => strtolower(preg_replace('/\s*\t*/','',$vars_from_file[1])));
                        $vars_db += array("vm_network_mask" => strtolower(preg_replace('/\s*\t*/','',$vars_from_file[2])));
                        $vars_db += array("vm_network_address" => strtolower(preg_replace('/\s*\t*/','',$vars_from_file[3])));
                        $vars_db += array("vm_gw_ip_address" => strtolower(preg_replace('/\s*\t*/','',$vars_from_file[4])));
                        $vars_db += array("vm_dns_ip_address" => strtolower(preg_replace('/\s*\t*/','',$vars_from_file[5])));
                        $vars_db += array("one_network_id" => strtolower(preg_replace('/\s*\t*/','',$vars_from_file[6])));
                /**/
                        $feedback_message_check = $this->check_add_data($vars_db);
                        if ($feedback_message_check)
                        {
                            $feedback_message_class = "alert alert-danger";
                            $feedback_message = $feedback_message . "(string ". $str . ") " . $feedback_message_check;
                            $str += 1;
                            continue;
                        }

                        $pdo->beginTransaction();
                        try {
                            $statement = $pdo->prepare(
                                'insert into mod_onecontrol_onevm (vm_name, vm_ip_address, vm_network_mask, vm_network_address, vm_gw_ip_address, vm_dns_ip_address, one_network_id)
                                values (:vm_name, :vm_ip_address, :vm_network_mask, :vm_network_address, :vm_gw_ip_address, :vm_dns_ip_address, :one_network_id)'
                            );

                            $statement->execute(
                                [
                                    ':vm_name' => $vars_db['vm_name'],
                                    ':vm_ip_address' => $vars_db['vm_ip_address'],
                                    ':vm_network_mask' => $vars_db['vm_network_mask'],
                                    ':vm_network_address' => $vars_db['vm_network_address'],
                                    ':vm_gw_ip_address' => $vars_db['vm_gw_ip_address'],
                                    ':vm_dns_ip_address' => $vars_db['vm_dns_ip_address'],
                                    ':one_network_id' => $vars_db['one_network_id'],
                                ]
                            );

                            $pdo->commit();
                        } catch (\Exception $e) {
                            $feedback_message = $e->getMessage();
                            $feedback_message_class = "alert alert-danger";
                            $pdo->rollBack();
                        }

                        $str += 1;
                    }
                    if (!feof($handle)) {
                        $feedback_message = $feedback_message . "Error: fgets() failed unexpectedly";
                        $feedback_message_class = "alert alert-danger";
                    }
                    fclose($handle);
                }
            } else {
                $feedback_file_message = $vars['file_error'];
                $feedback_file_message_class = "alert alert-danger";
            }
        }

        $parse = new Template_Parse;
        $parse->get_tpl("/var/www/html/www.clouds365.host/whmcs/modules/addons/onecontrol/templates/admin.tpl");
        $parse->set_tpl('{SHOWTABDEF}', '');
        $parse->set_tpl('{ADDTABDEF}', 'tabShow');
        $parse->set_tpl('{CLEARTABDEF}', '');
        $parse->set_tpl('{DELTABDEF}', '');
        $parse->set_tpl('{VM_NAME}', $vm_name);
        $parse->set_tpl('{VM_IP_ADDRESS}', $vm_ip_address);
        $parse->set_tpl('{VM_NETWORK_MASK}', $vm_network_mask);
        $parse->set_tpl('{VM_NETWORK_ADDRESS}', $vm_network_address);
        $parse->set_tpl('{VM_GW_IP_ADDRESS}', $vm_gw_ip_address);
        $parse->set_tpl('{VM_DNS_IP_ADDRESS}', $vm_dns_ip_address);
        $parse->set_tpl('{ONE_NETWORK_ID}', $one_network_id);
        $parse->set_tpl('{FEEDBACK_FILE_MESSAGE}', $feedback_file_message);
        $parse->set_tpl('{FEEDBACK_FILE_MESSAGE_CLASS}', $feedback_file_message_class);
        $parse->set_tpl('{FEEDBACK_MESSAGE}', $feedback_message);
        $parse->set_tpl('{FEEDBACK_MESSAGE_CLASS}', $feedback_message_class);
        //$from_file ? $parse->set_tpl('{checked}', 'checked') : '';
        $parse->tpl_parse();

        return $parse->template;
    }

}
