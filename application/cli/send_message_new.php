<?php

require_once 'queue.php';
require_once 'storage.php';
require_once '../third_party/message/config.php';
require_once '../third_party/message/Message_factory.php';

class send_message
{

    function __construct()
    {
        $this->init();
        $this->send_message();
    }

    /**
     * @author Peter
     * @date 2016-02-17
     */
    private function init()
    {
        $dsn = 'mysql:host=localhost;dbname=Message';
        $username = 'message';
        $password = '******';

        try
        {
            $db = new PDO($dsn, $username, $password);
        }
        catch (PDOException $e)
        {
            print 'Error: ' . $e->getMessage() . PHP_EOL;
            exit();
        }
        $db->exec('SET NAMES utf8');

        $sql = 'SELECT Id, Telephone, Owner FROM Telephone';
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $telephones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sql = 'SELECT Id, Name, Message FROM Template';
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sql = 'SELECT * FROM TelTpl';
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $teltpls = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //消息模板
        $messages = array();
        foreach ($templates as $template)
        {
            $messages[$template['Name']] = $template;
        }
        Config::$messages = $messages;

        //过滤电话
        $mobile_filter = array();
        foreach ($telephones as $telephone)
        {
            $template_ids = array();
            foreach ($teltpls as $teltpl)
            {
                if ($telephone['Id'] == $teltpl['TelephoneId'])
                {
                    $template_ids[] = $teltpl['TemplateId'];
                }
            }

            $mobile_filter[$telephone['Telephone']] = array(
                $telephone['Owner'],
                $template_ids,
            );
        }
        Config::$mobile_filter = $mobile_filter;
    }

    /**
     * 发短信
     */
    public function send_message()
    {
        set_time_limit(0);
        $queue = new queue();
        while (true)
        {
            $json_str = $queue->pop(config::MessageTypeMessage);

            if($json_str == 'reload')
            {
                $this->init();
                continue;
            }

            if ($json_str)
            {
//                print_r($json_str.PHP_EOL);
                $mes = json_decode($json_str, true);

                //1、构建消息模板
                $string = $this->get_message_new($mes);
                if($string === false)
                {
                    $this->error(array('Result'=>-1,'Msg'=>'消息内容不正确'),$mes);
                    continue;
                }
                $mes['Content'] = $string;

                //2、过滤手机号
                $result = $this->filter_mobile($mes);
                if($result === false)
                {
                    $this->error(array('Result'=>-1,'Msg'=>'接收人不正确'),$mes);
                    continue;
                }

                if(empty($result['Normal']))
                {
                    $this->error(array('Result'=>-1,'Msg'=>'全部被过滤'),$mes);
                    continue;
                }

                $mes['Mobile'] = $result['Normal'];
                $factory = new Message_factory($mes);
                $factory->createMessage();
                $res = $factory->send_message();
                $send_result = array();
                $send_result['Normal'] = $res;
                if(!empty($result['Filter']))
                {
                    $send_result['Desc'] = implode(',',$result['Filter'])."被过滤";
                }
                $this->error($send_result,$mes);
            }
            else
            {
//                $queue->set_php_life();
                sleep(1);
            }
        }
    }

    /**
     * 校正消息内容，支持消息模板
     * @param $mes
     * @author Jentely
     * @date 2016-02-15
     * @return bool
     */
    private function check_message(&$mes)
    {
        if (!$mes || !is_array($mes))
        {
            return FALSE;
        }

        //检查是否可以向该手机号发信息
        if (isset($mes['MessageId']) && isset($mes['Mobile']))
        {
            if (isset(Config::$mobile_filter[$mes['Mobile']]))
            {
                if(isset(Config::$messages[$mes['MessageId']]))
                {
                    $message_id = Config::$messages[$mes['MessageId']]['Id'];
                    if (!in_array($message_id, Config::$mobile_filter[$mes['Mobile']][1]))
                    {
                        return FALSE;
                    }
                }
            }
        }

        //根据模板ID和参数内容，构建出消息体
        if (isset($mes['MessageId']) && isset($mes['Content']) && is_array($mes['Content']))
        {
            $mes['Content'] = $this->get_message($mes['MessageId'], $mes['Content']);

            if (!$mes['Content'])
            {
                return FALSE;
            }

            if (Config::MessageService == 'MengWang')
            {
                $mes['Content'] .= "【91恋车】";
            }
        }

        return TRUE;
    }

    /**
     * 获取消息内容
     * @param $id int 消息模板ID
     * @param $data array 消息参数
     * @return string
     * @author Jentely
     */
    private function get_message($id, $data)
    {
        $messages = Config::$messages;

        if (!is_string($id) || !isset($messages[$id]))
        {
            return '';
        }

        $search = $replace = array();

        foreach ($data as $k => $v)
        {
            $search[] = '{#' . $k . '}';
            $replace[] = $v;
        }

        $string = str_replace($search, $replace, $messages[$id]['Message']);

        return $string;
    }

    /**
     * @function 过滤手机号
     * @User: CaylaXu
     * @param $mes
     * @return bool 参数错误返回false 否则返回正常数据与被过滤数组
     */
    private function filter_mobile($mes)
    {
        if(empty($mes['Mobile']) || (!is_array($mes['Mobile']) && !is_array($mes['Mobile'])))
        {
            return false;
        }

        $mobiles = array();
        if (is_string($mes['Mobile']))
        {
            $mobiles = explode(',',$mes['Mobile']);
        }
        else
        {
            $mobiles = $mes['Mobile'];
        }
        $normal_arr = array();
        $filter_arr = array();
        foreach ($mobiles as $mobile)
        {
            $filter = false;
            $mes['Mobile'] = $mobile;
            if (isset($mes['MessageId']) && isset($mes['Mobile']))
            {
                if (isset(Config::$mobile_filter[$mes['Mobile']]))
                {
                    if(isset(Config::$messages[$mes['MessageId']]))
                    {
                        $message_id = Config::$messages[$mes['MessageId']]['Id'];
                        if (!in_array($message_id, Config::$mobile_filter[$mes['Mobile']][1]))
                        {
                            $filter = true;
                        }
                    }
                }
            }

            if($filter === true)//放入过滤数组
            {
                $filter_arr[] = $mobile;
            }
            else //放入正常发送组
            {
                $normal_arr[] = $mobile;
            }
        }

        $result['Normal'] = $normal_arr;
        $result['Filter'] = $filter_arr;
        return $result;
    }

    /**
     * @function 构建消息内容
     * @User: CaylaXu
     * @param $mes
     * @return bool|mixed|string 返回构建好的内容否则false
     */
    private function get_message_new($mes)
    {
        if (!$mes || !is_array($mes) || !isset($mes['Content']))
        {
            return FALSE;
        }

        $string = is_string($mes['Content']) ? $mes['Content'] : '';

        //根据模板ID和参数内容，构建出消息体
        if (isset($mes['MessageId']) && is_array($mes['Content']))
        {
            $id = $mes['MessageId'];
            $data = $mes['Content'];
            $messages = Config::$messages;
            if (!is_string($id) || !isset($messages[$id]))
            {
                return false;
            }
            $search = $replace = array();
            foreach ($data as $k => $v)
            {
                $search[] = '{#' . $k . '}';
                $replace[] = $v;
            }
            $string = str_replace($search, $replace, $messages[$id]['Message']);
        }

        if (Config::MessageService == 'MengWang')
        {
            $string .= "【91恋车】";
        }

        return $string;
    }

    public function error($res,$mes)
    {
        $to = isset($mes['Mobile']) ? $mes['Mobile'] : '无';
        $to_users = array();
        if(is_string($to))
        {
            $to_users = implode(',',$to_users);
        }
        else
        {
            $to_users =  $to;
        }
        $data = array(
            'Type' => config::MessageTypeMessage,
            'ToUsers' =>json_encode($to_users,JSON_UNESCAPED_UNICODE),
            'Content' => isset($mes['Content']) ? $mes['Content'] : '',
            'Attr' => json_encode($mes, JSON_UNESCAPED_UNICODE),
            'ReceiptTime' => $mes['ReceiptTime'],
            'SendTime' => time(),
            'Response' => json_encode((array) $res, JSON_UNESCAPED_UNICODE),
        );
        $storage = new Storage();
        $storage ->store($data);
    }
}

$send_message = new send_message();
