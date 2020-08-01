<?php

$password = generate_password(6,0);
echo "password: ".$password."\n";
#
$vm_name = "v1p249.clouds365.host";
$user_id = 30;
$template_id = 6;
$ip_address = "192.168.55.249";
$dns_ip_address = "8.8.8.8";
$gw_ip_address = "192.168.55.1";
$network_id = 0;
$network_address = "192.168.55.0";

/*
$arr = array(
    "cmd" => "vm_terminate",
    "vm_id" => 246,
    "user_id" => 27    
);
*/
/*
$arr = array(
    "cmd" => "template_instantiate",
    "user_id" => $user_id,
    "vm_name" => $vm_name,
    "template_id" => $template_id,
    "ip_address" => $ip_address,
    "dns_ip_address" => $dns_ip_address,
    "gw_ip_address" => $gw_ip_address,
    "network_id" => $network_id,
    "network_address" => $network_address,
);
*/
/*
$arr = array(
    "cmd" => "template_instantiate_user",
    "user_id" => 7,
    "user_name" => "test_user2",
    "user_password" => "8OgsSE",
    "vm_name" => $vm_name,
    "template_id" => 6,
    "ip_address" => "192.168.55.253",
    "dns_ip_address" => "8.8.8.8",
    "gw_ip_address" => "192.168.55.1",
    "network_id" => 0,
    "network_address" => "192.168.55.0",
);
*/
/*
    VM action:
    poweroff-hard
    poweroff
    reboot-hard
    reboot
    resume
    
$arr = array(
    "cmd" => "vm_action",
    "action" => "resume",
    "vm_id" => 248,
    "user_id" => 30,    
);
*/
/*
$user_group_id_array = array(100);
$arr = array(
    "cmd" => "user_allocate",
    "user_name" => "test_user1",
    "user_password" => $password,
//    "user_group_id_array" => $user_group_id_array
);
*/
/*
$arr = array(
    "cmd" => "get_user_info",
    "user_id" => 30,    
);
*/
/*
$arr = array(
    "cmd" => "user_delete",
    "user_id" => 27,    
);
*/
/*
$arr = array(
    "cmd" => "get_vm_state",
    "user_id" => 30,
    "vm_id" => 248,
);
*/
/**/
$arr = array(
    "cmd" => "get_all_vm_state",
    "user_id" => 30,
);

$cipher="AES-256-CBC";
$key_file = '../key_aes';
$key = base64_decode(file_get_contents($key_file));

define("REQUEST_TIMEOUT", 20000); //  msecs, (> 1000!)
define("REQUEST_RETRIES", 3); //  Before we abandon

$plaintext =  json_encode($arr);

/*
* Encryption message function 
*/
function encrypt($plaintext, $key, $cipher){
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    $ciphertext = base64_encode( $iv.$ciphertext_raw );
    return $ciphertext;
}

/*
* Decryption message function 
*/
function decrypt($ciphertext, $key, $cipher){
    $c = base64_decode($ciphertext);
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = substr($c, 0, $ivlen);
    $ciphertext_raw = substr($c, $ivlen);
    $plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    return $plaintext;
}

/*
* Password generator
*/
function generate_password($number,$strong)
{
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
    if ($strong) {
       $arr = $arr_strong;
    } else {
       $arr = $arr_middle;
    }

    $pass = "";
    for($i = 0; $i < $number; $i++)
    {
      $index = rand(0, count($arr) - 1);
      $pass .= $arr[$index];
    }
    return $pass;
}

/*
* Helper function that returns a new configured socket
*/
function client_socket(ZMQContext $context)
{
    echo "I: connecting to server…", PHP_EOL;
    $client = new ZMQSocket($context,ZMQ::SOCKET_REQ);
    $client->connect("tcp://10.3.3.3:5555");

    //  Configure socket to not wait at close time
    $client->setSockOpt(ZMQ::SOCKOPT_LINGER, 0);

    return $client;
}

$context = new ZMQContext();
$client = client_socket($context);

$retries_left = REQUEST_RETRIES;
$read = $write = array();



while ($retries_left) {
    //  We send a request, then we work to get a reply
    $client->send(encrypt($plaintext, $key, $cipher));

    $expect_reply = true;
    while ($expect_reply) {
        //  Poll socket for a reply, with timeout
        $poll = new ZMQPoll();
        $poll->add($client, ZMQ::POLL_IN);
        $events = $poll->poll($read, $write, REQUEST_TIMEOUT);

        //  If we got a reply, process it
        if ($events) {
            //  We got a reply from the server
            $reply = decrypt($client->recv(), $key, $cipher);
            if (json_decode($reply)) {
                printf ("I: server replied OK (%s)%s", $reply, PHP_EOL);
                var_dump(json_decode($reply));
                $retries_left = 0;
                $expect_reply = false;
            } else {
                printf ("E: malformed reply from server: %s%s", $reply, PHP_EOL);
            }
        } elseif (--$retries_left == 0) {
            echo "E: server seems to be offline, abandoning", PHP_EOL;
            break;
        } else {
            echo "W: no response from server, retrying…", PHP_EOL;
            //  Old socket will be confused; close it and open a new one
            $client = client_socket($context);
            //  Send request again, on new socket
            $client->send(encrypt($plaintext, $key, $cipher));
        }
    }
}
