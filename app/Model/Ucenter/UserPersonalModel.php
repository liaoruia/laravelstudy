<?php
/**
 * Created by PhpStorm.
 * User: liaorui
 * Date: 20-6-12
 * Time: 下午4:09
 */

namespace App\Model\Ucenter;


use App\Model\BaseModel;

class UserPersonalModel extends BaseModel
{
    protected $connection = 'ucenter';

    protected $table = 'user_personal';
}