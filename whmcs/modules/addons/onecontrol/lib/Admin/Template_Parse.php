<?php

namespace WHMCS\Module\Addon\OneControl\Admin;

class Template_Parse
  {
    var $vars = array(); 
    var $template; 

    public function get_tpl($tpl_name)
      {
       if(empty($tpl_name) || !file_exists($tpl_name))
        {
          return false;
        }
       else
        {
          $this->template = file_get_contents($tpl_name);
        }
      }
      
    public function set_tpl($key,$var)
      {
        $this->vars[$key] = $var;
      }
      
    public function tpl_parse()
      {
         foreach($this->vars as $find => $replace)
             {
               $this->template = str_replace($find, $replace, $this->template);
             }
      }
  }