<?php
/**
 * Created by PhpStorm.
 * User: liaorui
 * Date: 2023/3/15
 * Time: 12:26
 */

namespace App\Logic\Utils;


class Chat
{

    private $_OPENAI_API_KEY='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';
    private $_url="https://api.openai.com/v1/completions";
    /**
     * Chat constructor.
     */
    public function __construct(){

    }

    public function liaotian(){

    }

    private  function curl($data){
        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = "Authorization: Bearer ".$this->_OPENAI_API_KEY;
        $options = array(
            CURLOPT_URL => "https://api.openai.com/v1/completions",
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_PROXY => "192.168.100.93:21882",
        );

        $curl = curl_init();
        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo "Error: " . curl_error($curl);
        } else {
            var_dump($response);
        }
    }
}