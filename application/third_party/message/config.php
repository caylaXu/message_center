<?php
class Config
{
    const DEBUG = true;

    // MySQL
    const MYSQL_HOST    = 'localhost';
    const MYSQL_PORT    = 3306;
    const MYSQL_USER    = 'root';
    const MYSQL_PASS    = 'motouch@104';
    const MYSQL_DBNAME  = 'GDogLog';
    const MYSQL_CHARSET = 'utf8';

    // Redis
    const REDIS_HOST = '127.0.0.1';
    const REDIS_PORT = 6379;
    const REDIS_PASS = 'motouch@2015';

    // Queue
    const QUEUE_IP   = '0.0.0.0';
    const QUEUE_PORT = 8080;

    // Smtp Mail
    const SMTP_HOST = 'smtp.exmail.qq.com';
    const SMTP_PORT = 25;
    const SMTP_USER = 'messagecenter@motouch.cn';
    const SMTP_PASS = 'Dianxia@2016';
    // Pid
    const WORKER_PID = '/var/run/motouch_log_worker.pid';
    const QUEUE_PID  = '/var/run/motouch_log_queue.pid';

    //  message one 微网通联
    const Msg_Target = "http://cf.lmobile.cn/submitdata/Service.asmx/g_Submit";
    const Msg_Sname = 'DLlinlij';
    const Msg_Pwd = 't0BbDfJ3';
    const Test_Msg_Sname = 'dl-wangyah';
    const Test_Msg_Pwd = 'wangyh12';
    const Msg_Scorpid = '';
    const Msg_Sprdid = '1012888'; //只支持发送单个短信

    //message two 上海创蓝
    const SHCL_Msg_Api_Url = "http://222.73.117.158/msg/HttpBatchSendSM?";
    const SHCL_Msg_User = 'Jylc88888';
    const SHCL_Msg_Pwd = 'Jylc88888';

    
    //三种推送类型
    const MessageTypeMessage="sms";//短信
    const MessageTypeAppPush ="app_push";//APP推送
    const MessageTypeEmail="email";//发邮件
    
    const TcpWorkerNum = 1;	//tcp worker(进程)个数
    
    // Queue
    const TCP_IP   = '0.0.0.0';
    const TCP_PORT = 8187;
    const DAEMONIZE = true;

    //短信通道配置
    const MessageService='MengWang';

    static $MessageServiceConfig = array(
        'WeiWangTongLian' => array(
            'Msg_Target' => "http://cf.lmobile.cn/submitdata/Service.asmx/g_Submit",
            'Msg_Sname' => 'DLlinlij',
            'Msg_Pwd' => 't0BbDfJ3',
            'Test_Msg_Sname' => 'dl-wangyah',
            'Test_Msg_Pwd' => 'wangyh12',
            'Msg_Scorpid' => '',
            'Msg_Sprdid' => '1012888'//只支持发送单个短信
        ),

        'ShangHaiChuangLan' => array(
            'SHCL_Msg_Api_Url' => "http://222.73.117.158/msg/HttpBatchSendSM?",
            'SHCL_Msg_User' => 'Jylc88888',
            'SHCL_Msg_Pwd' => 'Jylc88888',
        ),


        'MengWang' => array(
            'MW_pageurl' => "http://61.145.229.29:9006/MWGate/wmgw.asmx/MongateSendSubmit",
            'MW_userId' => "J10073",
            'MW_password' => "965321",
            'MW_MsgId' => 0,
            'pszSubPort' => "*"
        )

    );

    static $AppConfig = array(
        'Coach' => array(
            'AppKey' => "4bb4f7c3dcda7cb6572d10a0",
            'MasterSecret'=>"3a7e370322e35220e418af12"
        ),

        'School' => array(
            'AppKey' => "f4bf34c57ed4589d821d3a31",
            'MasterSecret'=>"abdfd7351912856ba2efbf4e"
        ),

        'Student' => array(
            'AppKey' => "05a9843dc481052540ea2612",
            'MasterSecret'=>"120d7dcfb80cf67d2eeb51bd"
        ),
    );


    static  $mw_status_code = array(
        "-1" => "参数为空。信息、电话号码等有空指针，登陆失败",
        "-2" => "电话号码个数超过100",
        "-10" => "申请缓存空间失败",
        "-11" => "电话号码中有非数字字符",
        "-12" => "有异常电话号码",
        "-13" => "电话号码个数与实际个数不相等",
        "-14" => "实际号码个数超过100",
        "-101" => "发送消息等待超时",
        "-102" => "发送或接收消息失败",
        "-103" => "接收消息超时",
        "-200" => "其他错误",
        "-999" => "web服务器内部错误",
        "-10001" => "用户登陆不成功",
        "-10002" => "提交格式不正确",
        "-10003" => "用户余额不足",
        "-10004" => "手机号码不正确",
        "-10005" => "计费用户帐号错误",
        "-10006" => "计费用户密码错",
        "-10007" => "账号已经被停用",
        "-10008" => "账号类型不支持该功能",
        "-10009" => "其它错误",
        "-10010" => "企业代码不正确",
        "-10011" => "信息内容超长",
        "-10012" => "不能发送联通号码",
        "-10013" => "操作员权限不够",
        "-10014" => "费率代码不正确",
        "-10015" => "服务器繁忙",
        "-10016" => "企业权限不够",
        "-10017" => "此时间段不允许发送",
        "-10018" => "经销商用户名或密码错",
        "-10019" => "手机列表或规则错误",
        "-10021" => "没有开停户权限",
        "-10022" => "没有转换用户类型的权限",
        "-10023" => "没有修改用户所属经销商的权限",
        "-10024" => "经销商用户名或密码错",
        "-10025" => "操作员登陆名或密码错误",
        "-10026" => "操作员所充值的用户不存在",
        "-10027" => "操作员没有充值商务版的权限",
        "-10028" => "该用户没有转正不能充值",
        "-10029" => "此用户没有权限从此通道发送信息",
        "-10030" => "不能发送移动号码",
        "-10031" => "手机号码(段)非法",
        "-10032" => "用户使用的费率代码错误",
        "-10033" => "非法关键词"
        );

    static $AppArray = array('Coach','School', 'Student');
    static $MessageTypeArray = array(self::MessageTypeMessage,self::MessageTypeAppPush,self::MessageTypeEmail);

    static $messages = array(
        /*0 => '{#0}',
        1 => '您的学车订单大约将在1小时之内响应，请耐心等待。感谢您对91恋车的信任，91恋车的合作驾校都是经过严格筛选，并在行业拥有良好口碑的驾校。您的学车订单大约将在1小时之内响应，请耐心等待。感谢您对91恋车的信任，91恋车的合作驾校都是经过严格筛选，并在行业拥有良好口碑的驾校。',
        2 => '今天{#0}，教练 {#1} 抢单成功。您可以主动咨询教练，也可以等待教练联系您，有任何疑问请咨询400-112-5180。',
        3 => '恭喜您抢单成功！快和学员联系吧，根据学员自身情况确定学车计划，再邀请学员前往门店参观可以大大提高学员的支付率哦！',
        4 => '您的学车订单已超时，暂时没有教练抢单，别生气，教练可能都在忙，再发一次试试。',
        5 => '您的学车订单已结束，有{#0}位教练抢单成功，请前往个人中心查看。',
        6 => '学员 {#0}，手机号{#1}，已支付1元试学费用，请及时安排试学等相关事宜。有任何疑问请拨打400-112-5180。',
        7 => '学员 {#0}，手机号{#1}，已支付保障金{#2}元(学费扣除{#3}元红包后的{#4})，剩余学费请与学员按合同自行结算。有任何疑问请拨打400-112-5180。',
        8 => '学员 {#0}，手机号{#1}，已支付保障金{#2}元(学费的{#3})，剩余学费请与学员按合同自行结算。有任何疑问请拨打400-112-5180。',
        9 => '学员 {#0}，手机号{#1}，已确认拿证，平台将按期支付{#2}的保障金，请留意账户信息，有任何疑问请拨打400-112-5180。',
        10 => '学员 {#0}，手机号{#1}，已支付1元试学费用，请查看学员详情，并安排后续相关事宜，谢谢！',
        11 => '学员 {#0}，手机号{#1}，已支付保障金{#2}元，请查看学员详情，并安排后续学车计划，谢谢！',
        12 => '学员 {#0}，手机号{#1}，已支付第三笔学费{#2}元，请查看学员详情，并安排后续学车计划，谢谢！',
        13 => '91恋车验证码（{#0}）',
        14 => '学员 {#0}，手机号{#1}，已支付1元试学费用，请前往订单中心查看。',
        15 => '学员 {#0}，手机号{#1}，已支付保障金{#2}元，请前往订单中心查看。',
        16 => '学员 {#0}，手机号{#1}，已支付第三笔学费{#2}元，请前往订单中心查看。',
        17 => '学员 {#0}，手机号{#1}，已确认拿证，请前往订单中心查看，并向驾校转账{#2}保障金。',
        18 => '亲爱的 {#0}，您有一个{#1}元的学车红包将于{#2}天后到期，赶紧前往91恋车-个人中心-我的钱包查看并使用吧。',
        19 => '亲爱的 {#0}，您有一个{#1}元的学车红包将于今天到期，请前往91恋车-个人中心-我的钱包查看并使用。',
        20 => '您在 {#0} 报名的“{#1}”价格已变动，价格调整为{#2}元，请前往91恋车查看。',
        21 => '您有一条待抢订单未处理，请速往91恋车APP抢单！此订单将于2小时后失效。',
        22 => '有一条订单超过5分钟无驾校抢单，请速往后台处理！',
        23 => '学员 {#0}，手机号{#1}，已线下支付{#2}元，请及时确认款项并联系学员！',
        24 => '亲爱的 {#0}，您已成功报名了一元试学，学车总监将全程为您服务，感谢您的支持！',
        25 => '亲爱的 {#0}，您已支付{#1}的保障金，请及时与驾校结算剩余款项，正式开始学车之旅。91恋车将全程监管教练，为您保驾护航。',*/
    );

    static $mobile_filter = array(
        //'18682007796' => array('赵丰', array()),
    );

    static function reload()
    {
        //重新载入配置
        $redis = new Redis();
        $redis->pconnect(config::REDIS_HOST,config::REDIS_PORT);
        $redis->auth(config::REDIS_PASS);
        $redis->rpush(config::MessageTypeMessage, 'reload');
    }

}