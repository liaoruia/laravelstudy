<?php
/**
 * Created by PhpStorm.
 * User: liaorui
 * Date: 20-6-12
 * Time: 下午4:05
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public static function m(){
        $obj= new static();
        return $obj->newBaseQueryBuilder()->from($obj->table);
    }
}