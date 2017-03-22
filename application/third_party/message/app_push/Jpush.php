<?php 
//极光推送的类
//文档见：http://docs.jpush.cn/display/dev/Push-API-v3

class Jpush extends Basic_message
{
    private $url = "https://api.jpush.cn/v3/push";  //推送的地址
    public static $_instance;
    private $app_key = 'd3d6132b3bbc15a6768c7350';  //待发送的应用程序(appKey)
    private $master_secret = 'd378dc9c5b6ea19b25162d56';// api主密码

    //若实例化的时候传入相应的值则按新的相应值进行
    private function __construct()
    {

    }
 
          //创建__clone方法防止对象被复制克隆
    public function __clone(){
        trigger_error('Clone is not allow!',E_USER_ERROR);
    }
       //单例方法,用于访问实例的公共的静态方法
    public static function getInstance($app_key=null, $master_secret=null)
    {
        if(!(self::$_instance instanceof self))
        {
            self::$_instance = new self();
        }
        self::$_instance->app_key = $app_key;
        self::$_instance->master_secret = $master_secret;
        return self::$_instance;
    }
    /*  $receiver 接收者的信息
        all 字符串 该产品下面的所有用户. 对app_key下的所有用户推送消息
        tag(20个)Array标签组(并集): tag=>array('昆明','北京','曲靖','上海');
        tag_and(20个)Array标签组(交集): tag_and=>array('广州','女');
        alias(1000)Array别名(并集): alias=>array('93d78b73611d886a74*****88497f501','606d05090896228f66ae10d1*****310');
        registration_id(1000)注册ID设备标识(并集): registration_id=>array('20effc071de0b45c1a**********2824746e1ff2001bd80308a467d800bed39e');
    */
    // $content 推送的内容。
    // $m_type 推送附加字段的类型(可不填) http,tips,chat....
    // $m_value 推送附加字段的类型对应的内容(可不填) 可能是url,可能是一段文字。
    // $m_time 保存离线时间的秒数默认为一天(可不传)单位为秒
//    public function send_message($receiver='all', $title='',$content='',$builder_id = 0 , $platform = array('android','ios'), $params=array(),$m_time='86400')
    public function send_message($receiver='all', $title='',$content='',$params=array())
    {
        $base64=base64_encode("$this->app_key:$this->master_secret");
        $header=array("Authorization:Basic $base64","Content-Type:application/json");
        $data = array();
        $data['platform'] = isset($params['Platform']) ? (array)$params['Platform']: array('android','ios');     //目标用户终端手机的平台类型android,ios,winphone
        $data['audience'] = is_array($receiver) ? array('registration_id'=>(array)$receiver) : $receiver; //目标用户
        $data['notification'] = array(
            //统一的模式--标准模式
            "alert"=>$content,
            //安卓自定义
            "android"=>array(
                "alert"=>$content,
                "title"=>$title,
                "builder_id"=>isset($params['BuilderId']) ? $params['BuilderId']: 0,
                "extras"=>isset($params['Params'])?$params['Params']:array()
            ),
            //ios的自定义
            "ios"=>array(
                "alert"=>$content,
                "sound" => isset($params['Sound']) ? $params['Sound']:"default",
                "badge"=>"1",
                "content-available"=>true,
                "extras"=>isset($params['Params'])?$params['Params']:array()
            ),
        );
 
//               //苹果自定义---为了弹出值方便调测
//        $data['message'] = array(
//            "msg_content"=>$content,
//            "title"  => $title,
//            "extras"=>$params
//        );
 
        //附加选项
        $data['options'] = array(
            "sendno"=>time(),
            "time_to_live"=>isset($params['Livetime'])?$params['Livetime']:'86400', //保存离线时间的秒数默认为一天
            "apns_production"=>isset($params['apns_production']) ? $params['apns_production']:1,//指定 APNS 通知发送环境：0开发环境，1生产环境。
        );
        $param = json_encode($data);
        $res = $this->push_curl($param, $header);
        $result = array();
        if($res) //激光推送请求返回信息->判断是否推送成功
        {
            //{"sendno":"1434335785","msg_id":"1957999942"}
        	$res_arr = json_decode($res, true);
        	if (isset($res_arr['error']))
        	{
                $result=array("result"=>$res_arr['error']['code'],$res_arr['error']['message'],"data"=>array());
        	}
        	else //处理成功的推送......
        	{
        		$data = array(
        			'sendno'  => $res_arr['sendno'],
        			'msg_id'  => $res_arr['msg_id'],
        			'errcode' => 0,
        			'message' => $content
        		);
        		//echo_json_result(0, '推送成功', $data);
                $result=array("result"=>0,"msg"=>'推送成功',"data"=>$data);
        	}
        }
        else
        {
        	//未得到返回值--返回失败
        	//echo_json_result(-1, "接口调用失败或无响应");
            $result=array("result"=>-1,"msg"=>"接口调用失败或无响应","data"=>array());
        }
        return $result;
    }

    /**
     * @function 自定义消息推送
     * @author CaylaXu
     * @param string $receiver 消息接收者
     * @param string $message 消息内容
     * @param array $params 消息类型,可选参数，消息标题
     * @return array
     */
    public function app_push_msg($receiver='all', $msg_content='',$params=array())
    {
        $base64=base64_encode("$this->app_key:$this->master_secret");
        $header=array("Authorization:Basic $base64","Content-Type:application/json");
        $data = array();
        $data['platform'] = isset($params['Platform']) ? (array)$params['Platform']: array('android','ios');     //目标用户终端手机的平台类型android,ios,winphone
        $data['audience'] = is_array($receiver) ? array('registration_id'=>(array)$receiver) : $receiver; //目标用户

        $data['message'] = array(
            "msg_content"=>$msg_content,
            "content_type"=>isset($params['ContentType']) ? $params['ContentType'] : '',
            "title"  => isset($params['Title']) ? $params['Title'] : '',
            "extras"=>isset($params['Params'])?$params['Params']:array()
        );

        //附加选项
        $data['options'] = array(
            "sendno"=>time(),
            "time_to_live"=>isset($params['Livetime'])?$params['Livetime']:'86400', //保存离线时间的秒数默认为一天
            "apns_production"=>isset($params['apns_production']) ? $params['apns_production']:1,//指定 APNS 通知发送环境：0开发环境，1生产环境。
        );
        $param = json_encode($data);
        $res = $this->push_curl($param, $header);
        $result = array();
        if($res) //激光推送请求返回信息->判断是否推送成功
        {
            //{"sendno":"1434335785","msg_id":"1957999942"}
            $res_arr = json_decode($res, true);
            if (isset($res_arr['error']))
            {
                $result=array("result"=>$res_arr['error']['code'],$res_arr['error']['message'],"data"=>array());
            }
            else //处理成功的推送......
            {
                $data = array(
                    'sendno'  => $res_arr['sendno'],
                    'msg_id'  => $res_arr['msg_id'],
                    'errcode' => 0,
                    'message' => $msg_content
                );
                //echo_json_result(0, '推送成功', $data);
                $result=array("result"=>0,"msg"=>'推送成功',"data"=>$data);
            }
        }
        else
        {
            //未得到返回值--返回失败
            //echo_json_result(-1, "接口调用失败或无响应");
            $result=array("result"=>-1,"msg"=>"接口调用失败或无响应","data"=>array());
        }
        return $result;
    }

    //推送的Curl方法
    public function push_curl($param="", $header=""){
        if (empty($param)) { return false; }
        $postUrl = "https://api.jpush.cn/v3/push";
        $curlPost = $param;
        $ch = curl_init();                                      //初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);                 //抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);                    //设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);            //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);                      //post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);           // 增加 HTTP Header（头）里的字段 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);        // 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch);                                 //运行curl
        curl_close($ch);
        return $data;
    }
}