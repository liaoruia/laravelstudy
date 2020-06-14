<?php
/**
 * Created by PhpStorm.
 * User: liaorui
 * Date: 19-9-17
 * Time: 上午9:45
 */

namespace App\Logic\Utils;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log
{

    // 所有的LOG都要求在这里注册
    const LOG_ERROR = 'error';
    const LOG_INFO = 'info';

    private static $loggers = array();

    // 获取一个实例
    public static function log($name,$title,$content,$level=Logger::INFO,$file='lumen')
    {
        $log = new Logger($name);
        $log->pushHandler(new StreamHandler(storage_path
        ('logs/' . $name . '.' . $file . '.log'), 0));
        if ($level === Logger::INFO) {
            $log->info($title . '：' . $content);
        } elseif ($level === Logger::ERROR) {
            $log->error($title . '：' . $content);
        }
    }


    public static function logInfo($title, $content, $file = 'lumen')
    {
        self::log('admin', $title, $content, Logger::INFO, $file);
    }

    public  static function logError($title, $content, $file = 'lumen')
    {
        self::log('admin', $title, $content, Logger::ERROR, $file);
    }

}
