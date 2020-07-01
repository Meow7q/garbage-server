<?php
/**
 * Created by PhpStorm.
 * UserController: meow7
 * Date: 2018/3/26
 * Time: 14:29
 */

namespace App\Service;


use Illuminate\Log\Writer;

class Logger
{
    private static $loggers = [];

    /**
     * 获取一个Logger，默认绑定一个日志文件
     * @param string $name
     * @param int $day
     * @return Writer
     */
    public static function getLogger($name, $day = 30)
    {
        if(empty(self::$loggers[$name])){
            self::$loggers[$name] = new Writer(new \Monolog\Logger($name));
            self::$loggers[$name]->useDailyFiles(storage_path().'/logs/'. $name .'.log', $day);
        }

        return self::$loggers[$name];
    }
}