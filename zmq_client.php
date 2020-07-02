<?php

$password = generate_password(16,0);
echo "password: ".$password."\n";

/*
$arr = array(
    "cmd" => "user_allocate",
    "user_name" => "test_user1",
    "user_password" => $password,
);
*/
$arr = array(
    "cmd" => "get_vm_state",
    "user_id" => 4,
    "vm_id" => 26,
);


$cipher="AES-256-CBC";
$key_file = 'security/key';
$key = base64_decode(file_get_contents($key_file));

define("REQUEST_TIMEOUT", 2500); //  msecs, (> 1000!)
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
    $client->connect("tcp://79.135.149.37:5555");

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