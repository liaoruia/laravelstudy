<?php
/**
 * Created by PhpStorm.
 * User: liaorui
 * Date: 2023/3/15
 * Time: 12:26
 */

namespace App\Logic\Utils;


use App\Model\Live\ChatLogsModel;

class Chat
{

    private $_OPENAI_API_KEY;

    private $_url;
    private $_data=[];
    /**
     * Chat constructor.
     */
    public function __construct(){
        $this->_OPENAI_API_KEY = env('CHAT_KEY','');
        $this->_url = env('CHAT_URL','');
        $this->_data = [
            "model" => "gpt-3.5-turbo",
            "temperature"=>0,
            "presence_penalty"=>0,
            "frequency_penalty"=>0,
            "top_p"=>1,
            "max_tokens"=>2048,
//            "stream"=>true
        ];
    }

    public function Message($prompt){
        $this->_data['messages'] = array(
            array(
                "role" => "user",
                "content" => $prompt
            )
        );
        return $this;
    }

    public function Messages($prompt,$role='user'){
        if(isset($this->_data['messages'])){
            array_push($this->_data['messages'],array(
                "role" => $role,
                "content" => $prompt
            ));
        } else {
            $this->_data['messages'] = array(
                array(
                    "role" => $role,
                    "content" => $prompt
                )
            );
        }
        return $this;
    }
    public function liaotian(){
        return $this->curl($this->_url,$this->_data);
    }

    private  function curl($url,$data){
        $stime=microtime(true);
        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = "Authorization: Bearer ".$this->_OPENAI_API_KEY;
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_PROXY => "192.168.100.93:21882",
        );

        $curl = curl_init();
        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $etime=microtime(true);//获取程序执行结束的时间
        $total=$etime-$stime;   //计算差值
        if (curl_errno($curl)) {
            echo "Error: " . curl_error($curl);
        } else {
            if(isset($response['error'])){
                echo "Error: " . $response['message'];
            }
            $response = json_decode($response,true);
        }
        $id = $this->write($data,$response,$total);
        $response['id'] = $id;
        return $response;
    }

    public function write($reuqest,$response,$total){

        $insert = [
            'request'=>json_encode($reuqest),
            'request_len'=>strlen(json_encode($reuqest)),
            'response'=>json_encode($response),
            'response_len'=>strlen(json_encode($response)),
            'execution_time'=>$total,
        ];

        if($response){
            $insert['prompt_tokens']=$response['usage']['prompt_tokens'];
            $insert['completion_tokens']=$response['usage']['completion_tokens'];
        }
        return ChatLogsModel::m()->insertGetId($insert);
    }
}