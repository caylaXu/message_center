<?php
/**
 * 微网通联 短信通道
 * @User: Administrator
 * @Date: 2015/6/8
 * @Time: 16:20
 * @发送短信
 * 
 */
class wwtc_msg extends Basic_message
{
    /**
     * @var API_URL
     */
    public $target ; //短信接口

    /**
     * @var string
     */
    public $sname ;

    /**
     * @var string
     */
    public $spwd ;

    /**
     * 企业代码（扩展号，不确定请赋值空）
     * @var string
     */
    public $scorpid ;

    /**
     * 产品编号
     * @var string
     */
    public $sprdid ;
 public static $_instance; 

    private function __construct($config)
    {
        $this->target = $config['Msg_Target'];
        $this->sname = $config['Msg_Sname'];
        $this->spwd = $config['Msg_Pwd'];
        $this->scorpid = $config['Msg_Scorpid'];
        $this->sprdid = $config['Msg_Sprdid'];
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
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader .= "Host:" . $url_info['host'] . "\r\n";
        $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader .= "Content-Length:" . strlen($data) . "\r\n";
        $httpheader .= "Connection:close\r\n\r\n";
        //$httpheader .= "Connection:Keep-Alive\r\n\r\n";
        $httpheader .= $data;
//        $fd = fsockopen($url_info['host'], 80);
        $fd = fsockopen($url_info['host'],80,$errno,$errstr,30);
        if(!$fd)
        {
            return array('State'=>-1,'MsgState'=>'微网通联接口超时');
        }
        else
        {
            fwrite($fd, $httpheader);
            $gets = "";
            while(!feof($fd)) {
                $gets .= fread($fd, 128);
            }
            fclose($fd);
            if($gets != ''){
                $start = strpos($gets, '<?xml');
                if($start > 0) {
                    $gets = substr($gets, $start);
                }
            }
            return $gets;
        }
        return false;
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
            $tel = implode(',',$tel);
        }
        //替换成自己的测试账号,参数顺序和wenservice对应
        $post_data = "sname=".$this->sname."&spwd=".$this->spwd."&scorpid=".$this->scorpid."&sprdid=".$this->sprdid."&sdst=".$tel."&smsg=".rawurlencode($msg);
        $xml = $this->post($post_data, $this->target);
        if(is_array($xml))
        {
            return $xml;
        }
        else
        {
            $xml_obj = simplexml_load_string($xml);
            $res = json_decode(json_encode($xml_obj),true);
            //echo json_encode($res);
            return $res;
        }
        /**
         * {
        "State": "0",
        "MsgID": "1506151129164990601",
        "MsgState": "提交成功",
        "Reserve": "0"
        }
         */
        //return $res;
    }
}