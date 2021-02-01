<?php

use WHMCS\Database\Capsule;


class SecGenerators {

    public function generate_token()
    {
        $arr = array(
                 'a','b','c','d','e','f',
                 '1','2','3','4','5','6',
                 '7','8','9','0');        

        $token = "";
        for($i = 0; $i < 16; $i++)
        {
          $index = rand(0, count($arr) - 1);
          $token .= $arr[$index];
        }
        
        return $token;
    }    


    public function generate_password()
    {
        try {
            $one_zmq_config[] = Capsule::table('tbladdonmodules')
                ->select('setting','value')
                ->Where('module', 'onecontrol')
                ->get();
            $one_zmq_config = $this->array_collapse($one_zmq_config,'setting','value');
            $one_user_password_length = $one_zmq_config['one_user_password_length'];
            $one_user_password_strong= $one_zmq_config['one_user_password_strong'];
        } catch (\Exception $e) {
            echo $e->getMessage();
            return $e->getMessage();
        }            
        
        $arr_strong = array(
                     'a','b','c','d','e','f',
                     'g','h','i','j','k','l',
                     'm','n','o','p','r','s',
                     't','u','v','x','y','z',
                     'A','B','C','D','E','F',
                     'G','H','I','J','K','L',
                     'M','N','O','P','R','S',
                     'T','U','V','X','Y','Z',
                     '1','2','3','4','5','6',
                     '7','8','9','0','.',',',
                     '(',')','[',']','!','?',
                     '&','^','%','@','*','$',
                     '<','>','/','|','+','-',
                     '{','}','`','~');
        $arr_middle = array(
                     'a','b','c','d','e','f',
                     'g','h','i','j','k','l',
                     'm','n','o','p','r','s',
                     't','u','v','x','y','z',
                     'A','B','C','D','E','F',
                     'G','H','I','J','K','L',
                     'M','N','O','P','R','S',
                     'T','U','V','X','Y','Z',
                     '1','2','3','4','5','6',
                     '7','8','9','0');
                     
        if ($one_user_password_strong) {
           $arr = $arr_strong;
        } else {
           $arr = $arr_middle;
        }

        $pass = "";
        for($i = 0; $i < $one_user_password_length; $i++)
        {
          $index = rand(0, count($arr) - 1);
          $pass .= $arr[$index];
        }
        
        return $pass;
    }

    private function array_collapse($arr, $x, $y) {
        $carr = array();
        if ($arr)
        {    
            foreach($arr as $key => $value)
            {
               foreach ($value as $key2 => $value2)
               {
                $carr[$value2->$x] = $value2->$y;
               }
            }
        }
        return $carr;
    }
    
}    