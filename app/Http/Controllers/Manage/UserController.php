<?php
/**
 * Created by PhpStorm.
 * User: liaorui
 * Date: 20-6-12
 * Time: 下午3:21
 */

namespace App\Http\Controllers\Manage;


use App\Http\Controllers\Controller;
use App\Model\Ucenter\UserMobiletelephoneModel;
use App\Model\Ucenter\UserPersonalModel;
use Illuminate\Http\Request;

class UserController extends Controller
{

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
}