<?php


namespace App\Http\Controllers\Manage;


use App\Model\Ucenter\UserMobiletelephoneModel;
use Illuminate\Http\Request;
class test
{
    public function test(Request $request){
        $pagesize = 20;
        $page = $request->post('page',1);
        $offset = ($page-1) * $pagesize;
        $data = UserMobiletelephoneModel::m()->leftJoin('user_personal','user_mobiletelephone.uid','=','user_personal.uid')
            ->where('phonenumber','!=','')
            ->orderBy('user_mobiletelephone.insertDate','desc')
            ->offset($offset)
            ->limit($pagesize)
            ->get(['*'])
            ->toArray();
        $count = UserMobiletelephoneModel::m()->leftJoin('user_personal','user_mobiletelephone.uid','=','user_personal.uid')
            ->where('phonenumber','!=','')
            ->count();
        return [
            'code'=>200,
            'data'=>$data,
            'total'=>$count
        ];
    }
    public function aa(){
        $OPENAI_API_KEY='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';
        $url = 'https://api.openai.com/v1/embeddings';

        $data = array(
            'input' => 'Your text string goes here',
            'model' => 'text-embedding-ada-002'
        );

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $OPENAI_API_KEY // 请替换 $OPENAI_API_KEY 为您的 API 密钥
        );

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true
        );

        $curl = curl_init();
        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($response, true);

        print_r($result);
    }
}