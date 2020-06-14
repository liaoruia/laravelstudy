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
}