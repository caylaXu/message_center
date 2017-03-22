<?php

/**
 * @filename basic_message.php 
 * @encoding UTF-8 
 * @author SunnySun <Sunchangzhi, 331942828@qq.com> 
 * @link www.91lianche.com.cn 
 * @copyright Copyright (C) 2015 SunnySun 
 * @license http://www.gnu.org/licenses/
 * @datetime 2015-8-17  14:47:00
 * @version 1.0
 * @Description
  */


abstract class Basic_message
{
    //抽象方法不能包含函数体
    abstract public function send_message();//强烈要求子类必须实现该功能函数
}