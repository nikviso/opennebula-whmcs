<?php

use WHMCS\Database\Capsule;


class OneConnector {

        private function client_socket(ZMQContext $context, $dsn)
        {
            $client = new ZMQSocket($context,ZMQ::SOCKET_REQ);
            $client->connect($dsn);

            //  Configure socket to not wait at close time
            $client->setSockOpt(ZMQ::SOCKOPT_LINGER, 0);

            return $client;
        }

        public function connector($arr)
        {
            try {
                $one_zmq_config[] = Capsule::table('tbladdonmodules')
                    ->select('setting','value')
                    ->Where('module', 'onecontrol')
                    ->get();
                $one_zmq_config = $this->array_collapse($one_zmq_config,'setting','value');
                $request_timeout = $one_zmq_config['request_timeout'];
                $request_retries = $one_zmq_config['request_retries'];
                $cipher = $one_zmq_config['cipher'];
                $key = base64_decode(file_get_contents($one_zmq_config['key_file']));
                $dsn = "tcp://" . $one_zmq_config['one_ip_address'] . ":" . $one_zmq_config['one_tcp_port'];  
            } catch (\Exception $e) {
                logModuleCall(
                    'onecontrol',
                    __METHOD__,
                    $e->getMessage(),
                    $e->getTraceAsString()
                );
                //return $e->getMessage();
                return json_encode(array("error" => "error connection to DB."));
            }
            
            $plaintext =  json_encode($arr);

            $context = new ZMQContext();
            $client = $this->client_socket($context, $dsn);

            $retries_left = $request_retries;
            $read = $write = array();            

            while ($retries_left) {
                //  We send a request, then we work to get a reply
                $client->send($this->encrypt($plaintext, $key, $cipher));

                $expect_reply = true;
                while ($expect_reply) {
                    //  Poll socket for a reply, with timeout
                    $poll = new ZMQPoll();
                    $poll->add($client, ZMQ::POLL_IN);
                    $events = $poll->poll($read, $write, $request_timeout);

                    //  If we got a reply, process it
                    if ($events) {
                        //  We got a reply from the server
                        $reply = json_decode($this->decrypt($client->recv(), $key, $cipher));
                        if ($reply) {
                            //error_log ("I: server replied OK." . $reply . PHP_EOL);
                            logModuleCall(
                                'onecontrol',
                                __METHOD__,
                                "I: to ONE server request:" .$plaintext,
                                "I: ONE server replied:" . json_encode($reply)
                            );
                            $retries_left = 0;
                            $expect_reply = false;
                        } else {
                            logModuleCall(
                                'onecontrol',
                                __METHOD__,
                                "I: to ONE server request:" .$plaintext,
                                "E: malformed reply from ONE server: " . json_encode($reply)
                            );
                            return json_encode(array("error" => "malformed reply from ONE server."));
                        }
                    } elseif (--$retries_left == 0) {
                        logModuleCall(
                            'onecontrol',
                            __METHOD__,
                            "I: to ONE server request:" .$plaintext,
                            "E: ONE server seems to be offline, abandoning."
                        );                        
                        return json_encode(array("error" => "ONE server seems to be offline, abandoning."));
                    } else {
                        logModuleCall(
                            'onecontrol',
                            __METHOD__,
                            "I: to ONE server request:" .$plaintext,
                            "W: no response from ONE server, retrying…"
                        ); 
                        //  Old socket will be confused; close it and open a new one
                        $client = $this->client_socket($context, $dsn);
                        //  Send request again, on new socket
                        $client->send($this->encrypt($plaintext, $key, $cipher));
                    }
                }
            }
            
            return $reply;
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

        /*
        * Encryption message function 
        */
        private function encrypt($plaintext, $key, $cipher)
        {
            $ivlen = openssl_cipher_iv_length($cipher);
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
            $ciphertext = base64_encode( $iv.$ciphertext_raw );
            return $ciphertext;
        }

        /*
        * Decryption message function 
        */
        private function decrypt($ciphertext, $key, $cipher)
        {
            $c = base64_decode($ciphertext);
            $ivlen = openssl_cipher_iv_length($cipher);
            $iv = substr($c, 0, $ivlen);
            $ciphertext_raw = substr($c, $ivlen);
            $plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
            return $plaintext;
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