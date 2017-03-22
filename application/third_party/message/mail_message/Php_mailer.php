<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/6/8
 * Time: 17:28
 * ToDo: 发送邮件
 */
class Php_mailer extends Basic_message
{

        private $amil;

        /**
         * 设置邮件的字符编码，这很重要，不然中文乱码
         * @var string
         */
        public $CharSet = 'UTF-8';

        /**
         * 您的企业邮局域名
         * @var string
         */
        public $Host;

        /**
         * 启用SMTP验证功能
         * @var bool
         */
        public $SMTPAuth = true;

        /**
         * 邮局用户名(请填写完整的email地址)
         * @var string
         */
        public $Username;

        /**
         * 邮局密码
         * @var string
         */
        public $Password;

        /**
         * 端口
         * @var int
         */
        public $Port;

        /**
         * 邮件发送者email地址
         * @var string
         */
        public $From;

        /**
         * 发送方名称
         * @var string
         */
        public $Fromname = "Motouch消息中心";
        public static $_instance;

        /**
         * @param $host
         * @param $port
         * @param $user
         * @param $pwd
         */
        private function __construct($host, $port, $user, $pwd)
        {
                $this->amil           = new PHPMailer();
                $this->amil->IsSMTP(); // 使用SMTP方式发送
                $this->amil->CharSet  = $this->CharSet;
                $this->amil->Host     = $host;
                $this->amil->SMTPAuth = $this->SMTPAuth;
                $this->amil->Username = $user;
                $this->amil->Password = $pwd;
                $this->amil->Port     = $port;
                $this->amil->From     = $user;
                $this->amil->FromName = $this->Fromname;
        }

        //创建__clone方法防止对象被复制克隆
        public function __clone()
        {
                trigger_error('Clone is not allow!', E_USER_ERROR);
        }

        //单例方法,用于访问实例的公共的静态方法
        public static function getInstance($host, $port, $user, $pwd)
        {
                if (!(self::$_instance instanceof self))
                {
                        self::$_instance = new self($host, $port, $user, $pwd);
                }
                return self::$_instance;
        }

        /**
         * @param $ToAddress
         * @param $Subject
         * @param $Body
         * @param string $AltBody
         * @param string $ToName
         * @return array
         */
//        public function send_message($ToAddress = '', $Subject = '', $Body = '', $AltBody = '', $ToName = 'Someone')
        public function send_message($ToAddress = '', $Subject = '', $Body = '',$params = array())
        {
                $address = $ToAddress; //收件地址
                $this->amil->clearAddresses();//发送新邮件之前置空历史发送地址
                if (is_array($ToAddress))
                {
                        foreach ($ToAddress as $k => $ToMail)
                        {
                                $this->amil->AddAddress("$ToMail", $k);
                        }
                }
                else
                {
                        $this->amil->AddAddress("$address", 'Someone'); //收件人地址，可以替换成任何想要接收邮件的email信箱,格式是AddAddress("收件人email","收件人姓名")
                }
                //$this->amil->AddAddress("$address", $ToName);
                //$this->amil->AddReplyTo("", "");
                //$this->amil->AddAttachment("/var/tmp/file.tar.gz"); // 添加附件
                //$this->amil->IsHTML(true); // set email format to HTML //是否使用HTML格式

                if(isset($params['CC']))
                {
                        if(is_array($params['CC']))
                        {
                                foreach ($params['CC'] as $k => $v)
                                {
                                        $this->amil->addCC($v);
                                }
                        }
                        else
                        {
                                $this->amil->addCC($params['CC']);
                        }
                }

//                if(isset($params['isHtml']))
//                {
                        $this->amil->isHTML(TRUE);
//                }

                $this->amil->Subject = $Subject; //邮件标题
                $this->amil->Body    = $Body; //邮件内容

                if(isset($params['AltBody']))
                {
                        $this->amil->AltBody = $params['AltBody']; //附加信息，可以省略
                }
                if (!$this->amil->Send())
                {
                        return array("result" => 1, "msg" => '邮件发送失败', "data" => array($this->amil->ErrorInfo));
                }
                return array("result" => 0, "msg" => '邮件发送成功', "data" => array());
        }

}
