<?php

/**
 * Class Mw_msg 梦网 短信通道
 * @author CaylaXu <caylaxu@motouch.cn>
 */
$dir = dirname(__FILE__);
require_once $dir.'/../config.php';
class Mw_msg extends Basic_message
{
        /**
         * @var API_URL
         */
        public $target ; //短信接口

        /**
         * @var string
         */
        public $user_id;

        /**
         * @var string
         */
        public $password;

        /**
         * 扩展号，不确定请赋*
         * @var string
         */
        public $pszSubPort;

        /**
         * 产品编号
         * @var string
         */
        public $msg_id ;
        public static $_instance;

        private function __construct($config)
        {
                $this->target = $config['MW_pageurl'];
                $this->user_id = $config['MW_userId'];
                $this->password = $config['MW_password'];
                $this->pszSubPort = $config['pszSubPort'];
                $this->msg_id = $config['MW_MsgId'];
        }
        //创建__clone方法防止对象被复制克隆
        public function __clone()
        {
                trigger_error('Clone is not allow!', E_USER_ERROR);
        }

        //单例方法,用于访问实例的公共的静态方法
        public static function getInstance($config)
        {
                if (!(self::$_instance instanceof self))
                {
                        self::$_instance = new self($config);
                }
                return self::$_instance;
        }

        /**
         * 发送http请求
         * @param $data
         * @param $target
         * @return string
         */
        public function post($data, $target) {
                $url_info = parse_url($target);
                $httpheader = "POST " . $url_info['path'] . " HTTP/1.1\r\n";
                $httpheader .= "Host:" . $url_info['host'] . "\r\n";
                $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
                $httpheader .= "Content-Length:" . strlen($data) . "\r\n";
                $httpheader .= "\r\n";
                $httpheader .= trim($data);

                if(!isset($url_info["port"]))
                {
                        $url_info["port"]=80;
                }

//                $fd = fsockopen($url_info['host'], $url_info['port']);
                $fd = fsockopen($url_info['host'],$url_info['port'],$errno,$errstr,30);
                if(!$fd)
                {
                        return array('State'=>-1,'MsgState'=>'梦网接口超时');
                }
                else
                {
                        fwrite($fd, $httpheader);
                        $gets = "";
                        while(!feof($fd))
                        {
                                $gets .= fread($fd, 128);
                        }
                        fclose($fd);
                        if($gets != '')
                        {
                                $start = strpos($gets, '<?xml');
                                if($start > 0)
                                {
                                        $gets = substr($gets, $start);
                                }
                        }
                        return $gets;
                }
        }

        /**
         * 组装发送数据
         * @param $tel
         * @param $msg
         * @return mixed
         */
        public function send_message($tel='',$msg='',$params = array())
        {
                if(is_array($tel))
                {
                        $iMobiCount = count($tel);
                        $tel = implode(',',$tel);
                }
                else
                {
                        $iMobiCount = count(explode(',',$tel));
                }
                //替换成自己的测试账号,参数顺序和wenservice对应
                $post_data = "userId=".$this->user_id."&password=".$this->password."&pszMobis=".$tel."&pszMsg=".rawurlencode(str_replace("【91恋车】", "",$msg))."&iMobiCount=".$iMobiCount."&pszSubPort=".$this->pszSubPort;
                $xml = $this->post($post_data, $this->target);
                $xml_obj = simplexml_load_string($xml);
                $res = json_decode(json_encode($xml_obj),true);
                $err = $this->GetCodeMsg($res);
                if($err == '')
                {
                        return array('State'=>0,"MsgState"=>"提交成功");
                }
                return array('State'=>-1,"MsgState"=>$err);
                /**
                 * {
                "State": "0",
                "MsgID": "1506151129164990601",
                "MsgState": "提交成功",
                "Reserve": "0"
                }
                 */
        }


        function GetCodeMsg($code)
        {
                $err = '';
                if (is_array($code))
                {
                        $err = $code[0];
                }
                else
                {
                        $err = $code;
                }

                if (isset(Config::$mw_status_code[$err]))
                {
                        return Config::$mw_status_code[$err];
                }
                return '';
        }
}
