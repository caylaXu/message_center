<?php

/**
 * @filename short_message_factory.php 
 * @encoding UTF-8 
 * @author SunnySun <Sunchangzhi, 331942828@qq.com> 
 * @link www.91lianche.com.cn 
 * @copyright Copyright (C) 2015 SunnySun 
 * @license http://www.gnu.org/licenses/
 * @datetime 2015-8-17  15:57:57
 * @version 1.0
 * @Description
  */
/**
 * 工程类，主要用来创建对象
 * 功能：根据输入类型，工厂就能实例化出合适的对象
 */
require_once 'Shcl_msg.php';
require_once 'Wwtc_msg.php';
require_once 'Mw_msg.php';

class Short_message_factory 
{
    public static function create_short_message($operator)
    {
        switch ($operator)
        {
            case 'WeiWangTongLian':
                return Wwtc_msg::getInstance(Config::$MessageServiceConfig[$operator]);
                break;

            case 'ShangHaiChuangLan':
                return Shcl_msg::getInstance(Config::$MessageServiceConfig[$operator]);
                break;

            case 'MengWang':
                return Mw_msg::getInstance(Config::$MessageServiceConfig[$operator]);
                break;

            default:
                return Wwtc_msg::getInstance(Config::$MessageServiceConfig[$operator]);
        }
    }
}