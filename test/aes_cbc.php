<?php

$cipher="AES-256-CBC";

$key_file = 'key_file';
$key = base64_decode(file_get_contents($key_file));

$arr = array(
    "lang" => "php",
    "vm_name" => "debian-vm2",
    "vm_state" => "run",
);

$plaintext =  json_encode($arr);

$ciphertext_file = 'ciphertext_file';
$ciphertext = file_get_contents($ciphertext_file);

var_dump(json_decode(decrypt($ciphertext, $key, $cipher)));
file_put_contents($ciphertext_file, encrypt($plaintext, $key, $cipher));

function encrypt($plaintext, $key, $cipher){
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    $ciphertext = base64_encode( $iv.$ciphertext_raw );
    return $ciphertext;
}

function decrypt($ciphertext, $key, $cipher){
    $c = base64_decode($ciphertext);
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = substr($c, 0, $ivlen);
    $ciphertext_raw = substr($c, $ivlen);
    $plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    return $plaintext;
}
?>