<?php

/**
 * @filename MessageFactory.php
 * @encoding UTF-8
 * @author SunnySun <Sunchangzhi, 331942828@qq.com>
 * @link www.91lianche.com.cn
 * @copyright Copyright (C) 2015 SunnySun
 * @license http://www.gnu.org/licenses/
 * @datetime 2015-8-17  15:51:58
 * @version 1.0
 * @Description
 */
$dir = dirname(__FILE__);
require_once 'config.php';
require_once 'Basic_message.php';
require_once $dir.'/app_push/Jpush.php';
require_once $dir.'/mail_message/Php_mailer.php';
require_once $dir.'/short_message/Short_message_factory.php';
require_once $dir.'/../../../phplibs/phpmailer/phpmailer/PHPMailerAutoload.php';
//require_once $dir.'/../../../libraries/Scz_predis.php';
//include $dir.'/../../../vendor/autoload.php';
class Message_factory
{
    public $obj;
    public $type;
    public $mes;
    public function __construct($mes)
    {
        $this->type = $mes['Type'];
        $this->mes = $mes;
    }
    public function createMessage()
    {
        switch ($this->type)
        {
            case 'email':
                $this->obj = Php_mailer::getInstance(Config::SMTP_HOST, Config::SMTP_PORT, Config::SMTP_USER, Config::SMTP_PASS);
                break;
            case 'sms':
                //@todo 改为predis
                $redis = new Redis();
                $redis->pconnect(config::REDIS_HOST,config::REDIS_PORT);
                $redis->auth(config::REDIS_PASS);
                $operator = $redis->get('MessageChannel');
                if(empty($operator))
                {
                    $operator = config::MessageService;
                }
                $this->obj = Short_message_factory::create_short_message($operator);
                break;
            case 'app_push':
                $config = $this->get_config_app_push();
                if($config)
                {
                    $this->obj =  Jpush::getInstance($config['AppKey'],$config['MasterSecret']);
                }
                break;
        }
    }

    private function get_config_app_push()
    {
        if(!isset($this->mes['AppType']))
        {
            return false;
        }

        if(!in_array($this->mes['AppType'],Config::$AppArray))
        {
            return false;
        }

        return Config::$AppConfig[$this->mes['AppType']];
    }

    public function  send_message()
    {
        if($this->type == 'email')
        {
            //参数校验
            if(!isset($this->mes['ToAddress']) || !isset($this->mes['Subject']) || !isset($this->mes['Content']))
            {
                return false;
            }
            return $this->obj->send_message($this->mes['ToAddress'],$this->mes['Subject'],$this->mes['Content'],$this->mes);
        }

        if($this->type == 'sms')
        {
            if(!isset($this->mes['Mobile'])||!isset($this->mes['Content']))
            {
                return false;
            }
            return $this->obj->send_message($this->mes['Mobile'],$this->mes['Content'],$this->mes);
        }

        if($this->type == 'app_push')
        {
            if(!isset($this->mes['Title'])|| !isset($this->mes['Content']) || !isset($this->mes['Platform'])|| !isset($this->mes['Receiver']))
            {
                return false;
            }
            return $this->obj->send_message($this->mes['Receiver'],$this->mes['Title'],$this->mes['Content'],$this->mes);
        }

        //发送自定义消息
        if($this->type == 'app_push_msg')
        {
            if(!isset($this->mes['Message'])|| !isset($this->mes['ContentType']) || !isset($this->mes['Receiver']))
            {
                return false;
            }
            return $this->obj->app_push_msg($this->mes['Receiver'],$this->mes['Message'],$this->mes);
        }
    }
}
