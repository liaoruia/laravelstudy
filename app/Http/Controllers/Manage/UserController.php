<?php
/**
 * Created by PhpStorm.
 * User: liaorui
 * Date: 20-6-12
 * Time: 下午3:21
 */

namespace App\Http\Controllers\Manage;


use App\Http\Controllers\Controller;
use App\Model\Ucenter\UserPersonalModel;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function test(Request $request){
        $a = UserPersonalModel::m()->where('uid',7)->first();
        var_dump($a);
        return [
            'data'=>['code'=>1]
        ];
    }
}