<?php

/**
 * Created by PhpStorm.
 * User: MartinChen
 * Date: 2015/7/22
 * Time: 17:23
 * 接口源于上海创蓝
 */
class Shcl_msg extends Basic_message
{

        private $user;
        private $pwd;
        private $api_url;
         public static $_instance; 
        private function __construct($config)
        {
                $this->user = $config['SHCL_Msg_User'];
                $this->pwd = $config['SHCL_Msg_Pwd'];
                $this->api_url = $config['SHCL_Msg_Api_Url'];
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

        public function send_message($mobile='', $msg='',$params =array())
        {
                if (is_array($mobile))
                {
                        $mobile = implode(',', $mobile);
                }
                $data['account']    = $this->user;
                $data['pswd']       = $this->pwd;
                $data['mobile']     = $mobile;
                $data['msg']        = mb_convert_encoding(str_replace("【91恋车】", "",$msg),'UTF-8', 'auto');
                $data['needstatus'] = true;
                $post_data          = $this->format_data($data);
                $res                = $this->curl_post($this->api_url, $post_data);
                return $res;
        }

        private function curl_post($url, $post_data)
        {
                $ch     = curl_init();
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $result = curl_exec($ch);
                return $result;
        }

        private function format_data(array $post_data)
        {
                $data = '';
                foreach ($post_data as $k => $v)
                {
                        if (is_array($v))
                        {
                                echo var_export($v, true);
                        }
                        $data.= "$k=" . urlencode($v) . "&";
                }
                $data = substr($data, 0, -1);
                return $data;
        }

}

//$user = 'Jylc88888';
//$pwd = 'Jylc88888';
//$api_url = "http://222.73.117.158/msg/HttpBatchSendSM?";
//$send_msg = new sendmsg($user,$pwd,$api_url);
//
//$mobile[] = "18042411751";
//$mobile[] = "13058100501";
//$mobile[] = "18200712718";
//$mobile[] = "13823125047";
//$mobile[] = "13020272609";
//$mobile[] = "15279110584";
//$mobile[] = "18823209179";
//$mobile[] = "15919774440";
//$mobile[] = "13798990388";
//$mobile[] = "15507549969";
//
//$msg1 = "短信测试【91恋车】";
//$msg2 = "亲爱的用户，您的手机验证码是113456，此验证码5分钟内有效，请尽快完成验证。";
//$msg3 = "学员 赵丰，手机号13058100501，已支付学车费用的50%（4000元），91恋车将预留10%（800元）学车款，剩余40%（3200元）将在24小时内转至驾校账户（节假日顺延），谢谢！【91恋车】";
////$send_msg->send_msg($msg2,implode(',',$mobile));
//$send_msg->send_msg("13798990388",$msg3);
