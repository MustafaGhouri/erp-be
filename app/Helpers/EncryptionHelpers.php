<?php

namespace App\Helpers;

class EncryptionHelpers
{
    public static function encrypt($plainText, $key = '88D24FD13EC5AFA5899CFD7AC5E19A70')
    {
        $method = "AES-256-GCM";
        $initVector = openssl_random_pseudo_bytes(16);
        $openMode = openssl_encrypt($plainText, $method, $key, OPENSSL_RAW_DATA, $initVector, $tag);
        return bin2hex($initVector) . bin2hex($openMode . $tag);
    }

    public static function decrypt($encryptedText, $key = '88D24FD13EC5AFA5899CFD7AC5E19A70')
    {
        $method = 'AES-256-GCM';
        $encryptedText = hex2bin($encryptedText);
        $iv_len = $tag_length = 16;
        $iv = substr($encryptedText, 0, $iv_len);
        $tag = substr($encryptedText, -$tag_length, $iv_len);
        $ciphertext = substr($encryptedText, $iv_len, -$tag_length);
        return openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv, $tag);
    }
}
