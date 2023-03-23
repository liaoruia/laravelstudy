<?php
/**
 * Created by PhpStorm.
 * User: liaorui
 * Date: 20-6-12
 * Time: 下午3:21
 */

namespace App\Http\Controllers\Manage;


use App\Http\Controllers\Controller;
use App\Logic\Clinical\CaseLogic;
use App\Logic\Clinical\QALogic;
use App\Logic\Utils\Chat;
use App\Model\Clinical\CaseDiseaseSceneModel;
use App\Model\Clinical\CaseInquiryQuestionsModel;
use App\Model\Live\WoodModel;
use App\Model\Ucenter\UserMobiletelephoneModel;
use App\Model\Ucenter\UserPersonalModel;
use Illuminate\Http\Request;
use Phpml\Classification\Ensemble\RandomForest;
use Phpml\CrossValidation\RandomSplit;


class UserController extends Controller
{

    private  $questions = [
        '患者哪里不舒服？',
        '患者什么时候开始不舒服',
        '患者的症状持续多久了？',
        '患者有没有吃过什么药？',
        '既往有没有什么异常的病情？',
        '患者有没有工作？'
    ];
    public function caselist(Request $request){
        $seesionid = $request->post('sid',"");
        //随机20个案例
        $case_list = CaseLogic::instance()->case_list();
        if($seesionid == ""){
            $seesionid = md5(time().srand(1000));
        }
        return ['code'=>200,
            'sid'=>$seesionid,
            'data'=>$case_list
        ];
    }

    public function chatwen(Request $request){
        $caseId = $request->post('caseId',"");
        $seesionid = $request->post('sid',"");
        $content = $request->post('content','');
        if(!$seesionid){
            $seesionid = md5(time().rand(1000));
        }
        $pr = CaseLogic::instance()->systempr($caseId,true);
        $chat = new Chat();
        //定义系统场景
        $chat->Messages($pr,'system');//填充系统信息
        //定义连续问的时候需要从库中取问题
        $chat = QALogic::instance()->getqa($chat,$seesionid,$caseId);//填充历史问答
        //判断是否问过这个问题
        $result = QALogic::instance()->checkrepeat($seesionid,$caseId,$content);
        if($result){
            return [
                'code'=>200,
                'data'=>[
                    'content'=>'你问过这个问题了!换个问题吧'
                ]
            ];
        }
        $chat->Messages($content);//填充当前问题

        $chatMessages = $chat->liaotian();
        $chatMessages['choices'][0]['message']['content'];
        $insert = [
            'case_id'=>$caseId,
            'c_sid'=>$seesionid,
            'c_system'=>$pr,
            'c_name' =>"",
            'c_question'=>$content,
            'c_answer'=>$chatMessages['choices'][0]['message']['content'],
            'c_log_id'=>$chatMessages['id']
        ];
        //记录每次问答
        QALogic::instance()->userQA($insert);
        return [
            'code'=>200,
            'sid'=>$seesionid,
            'data'=>[
                'content'=>$chatMessages['choices'][0]['message']['content'],
            ]
        ];
    }


    public function newQA($case_id){

        //定义系统场景-- 案例数据取 病情介绍 既往史 门诊资料
        $pr = CaseLogic::instance()->case_info($case_id);
        //问诊数据 查体数据 取第一幕

        $chat = new Chat();
    }

    public function continueQA($sid,$case_id){

    }

    public function test(Request $request){
        $pagesize = 20;
        $page = $request->post('page',1);
        $offset = ($page-1) * $pagesize;

        $user_mobils = UserMobiletelephoneModel::m()
            ->where('phonenumber','!=','')
            ->orderBy('insertDate','desc')
            ->offset($offset)
            ->limit($pagesize)
            ->get(['*'])
            ->toArray();
        $count = UserMobiletelephoneModel::m()->count();
        $uids = array_column($user_mobils,'uid');
        $user_personals = UserPersonalModel::m()->whereIn('uid',$uids)->get(['*'])->keyBy('uid')
            ->toArray();
        foreach ($user_mobils as &$user_mobil){
            $user_personal = $user_personals[$user_mobil->uid];
            $user_mobil->uname = $user_personal->uname;
            $user_mobil->sex = $user_personal->sex;
            $user_mobil->address = $user_personal->address;
            $user_mobil->birthday = $user_personal->birthday;
            $user_mobil->work = $user_personal->work;
            $user_mobil->qq  = $user_personal->qq;
            $user_mobil->email = $user_personal->email;
        }
        return [
            'code'=>200,
            'data'=>$user_mobils,
            'total'=>$count
        ];
    }
    public function suichafen(){
        $data = new RandomSplit($dataset,0.3,42);
//        new RandomForest();
    }

    public function aaa(){

    }
    public function dot($a, $b) {
        $sum = 0;
        for ($i=0; $i < count($a); $i++) {
            $sum += $a[$i] * $b[$i];
        }
        return $sum;
    }
    public function buquan(){
        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = "Authorization: Bearer ".env("CHAT_KEY","");


        $data = array(
            "model" => "text-davinci-003",
            "max_tokens"=>2048,
            "prompt" => '俄罗斯桦木的鉴别'
        );
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

        curl_close($curl);
    }
    public function chat(){
        $chat = new Chat();
        $p = ['德国榉木'];
        $obj = WoodModel::m();
        foreach ($p as $t){
            $a=[
                "w_distinguish" => "的鉴别",
                "w_popularize" => "的推广文案",
                "w_introduce" => "的介绍"
            ];
            foreach ($a as $k=>$b){
                $response = $chat->Message($t.$b)->liaotian();
                $up = [
                    $k=>$response['choices'][0]['message']['content'],
                ];
                $result  = $obj->where(['w_name'=>$t])->update($up);
            }
            $result  = $obj->where(['w_name'=>$t])->update(['w_state'=>1]);
        }
    }
    public function liaotian(){
        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = "Authorization: Bearer ".env("CHAT_KEY","");
        $data = array(
            "model" => "gpt-3.5-turbo",
            "max_tokens"=>2048,
            "messages" => array(
                array(
                    "role" => "user",
                    "content" => "俄罗斯桦木的鉴别!"
                )
            )
        );
        $options = array(
            CURLOPT_URL => "https://api.openai.com/v1/chat/completions",
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_PROXY => "192.168.100.93:21882",
        );

        $curl = curl_init();
        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $response = json_decode($response, true);
        if (curl_errno($curl)) {
            echo "Error: " . curl_error($curl);
        } else {
            var_dump($response);
        }

        curl_close($curl);
    }
    public function aa(){
        $url = 'https://api.openai.com/v1/embeddings';
//        $url = 'https://api.openai.com/v1/completions';

        $data = array(
            //在全面贯彻党的二十大精神开局之年召开的全国两会上，代表委员热议民生关切，展望美好图景，凝聚起团结奋斗
            //两会上，代表委员以团结奋斗的精神，热议民生关切，为全面贯彻党的二十大精神开局之年，展望美好图景，共同谋求发展，实现共同繁荣。
            'input' => ['在全面贯彻党的二十大精神开局之年召开的全国两会上，代表委员热议民生关切，展望美好图景，凝聚起团结奋斗',
                '在全面贯彻党的二十大精神开局之年召开的全国两会上,代表委员热议民生关切,展望美好图景,畅谈未来打算,在迈向第二个百年奋斗目'],
            'model' => 'text-embedding-ada-002'
        );
//        $prompt="中国的首都是哪里？";

//        $answer = '武汉';
        $data2 = array(
            'model' => "text-davinci-002", # 选择语言模型
            "prompt"=>"介绍一下俄罗斯桦木", # 构造输入
            "max_tokens"=>2048, # 输出文本的最大长度
            "n" =>1, # 返回结果的数量
            "stop"=>null, # 结束模型输出的标记
            "temperature"=>0.0 # 控制模型生成文本的随机程度
        );

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . env("CHAT_KEY","") // 请替换 $OPENAI_API_KEY 为您的 API 密钥
        );

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
        curl_close($curl);

        $response = json_decode($response, true);
        var_dump($response);die;
//        var_dump($response['data']);
        var_dump($data);
//        var_dump($response);die;
        $embedding_a = $response['data'][0]['embedding'];
        $embedding_b = $response['data'][1]['embedding'];

        // 测试代码
//        $vec1 = [1, 2, 3];
//        $vec2 = [4, 5, 6];
//        $cosineSimilarity = $this->cosineSimilarity($embedding_a, $embedding_b);
//        echo "余弦相似度为: ".$cosineSimilarity; // 输出余弦相似度为: 0.97463184619708
        $distance = $this->vector_distance($embedding_a, $embedding_b);
        var_dump($distance);
        $aa= $this->dot($embedding_a,$embedding_b);
        var_dump($aa);
//        print_r($result);
    }

//    public function cosineSimilarity(array $vec1, array $vec2)
//    {
//        $dotProduct = 0.0;
//        $magnitude1 = 0.0;
//        $magnitude2 = 0.0;
//        foreach ($vec1 as $key => $value) {
//            // 计算点积
//            $dotProduct += $value * $vec2[$key];
//            // 计算向量1的长度
//            $magnitude1 += pow($value, 2);
//            // 计算向量2的长度
//            $magnitude2 += pow($vec2[$key], 2);
//        }
//        // 计算向量1的长度
//        $magnitude1 = sqrt($magnitude1);
//        // 计算向量2的长度
//        $magnitude2 = sqrt($magnitude2);
//        // 计算余弦相似度
//        $cosineSimilarity = $dotProduct / ($magnitude1 * $magnitude2);
//        return $cosineSimilarity;
//    }
    public function cosine_similarity($u, $v) {
        $dot_product = 0;
        $norm_u = 0;
        $norm_v = 0;

        for ($i = 0; $i < count($u); $i++) {
            $dot_product += $u[$i] * $v[$i];
            $norm_u += pow($u[$i], 2);
            $norm_v += pow($v[$i], 2);
        }

        $similarity = $dot_product / (sqrt($norm_u) * sqrt($norm_v));
        return $similarity;
    }

    public function vector_distance($u, $v) {
        $similarity = $this->cosine_similarity($u, $v);
        $distance = 1 - $similarity;
        return $distance;
    }

}