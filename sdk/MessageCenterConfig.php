<?php
/**
 * Created by PhpStorm.
 * User: MartinChen
 * Date: 2015/7/6
 * Time: 16:28
 */
class MessageCenterConfig
{
    static $CurrentHost='official';

    static $message_center_host = array(
        'beta' => array(
            'HOST'=>"112.74.14.46",
            'PORT'=>"8187",
        ),
        'gdog' => array(
            'HOST'=>"123.56.102.104",
            'PORT'=>"8187",
        ),
        'official' => array(
            'HOST'=>"120.24.66.7",
            'PORT'=>"8189",
        ),
    );

    //beta新消息中心
    const HOST = "112.74.14.46";
    const PORT = "8187";
}