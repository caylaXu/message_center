<?php

/**
 * Class Message 消息服务器
 * @author CaylaXu <caylaxu@motouch.cn>
 */
error_reporting(E_ALL & ~E_NOTICE);
require_once '../third_party/message/config.php';
require_once '../third_party/message/Message_factory.php';
require_once 'queue.php';

class Message
{
    public function __construct()
    {
        $this->listen();
    }

    /**
     * @param string $message
     * @return array
     * @internal param bool $debug
     */
    public function push_list($message)
    {
        $queue = new queue();
        $mes = json_decode($message, true);

        if (!$mes || !isset($mes['Type']) || !in_array($mes['Type'], config::$MessageTypeArray))
        {
            return array("result" => -1, "msg" => '发送类型暂时只支持sms(信息),email(邮件),app_push(app消息推送)');
        }
        else
        {
            $type = $mes['Type'];
            //记录下接收消息的时间时间
            $mes['ReceiptTime'] = time();

            $res = $queue->push($type, json_encode($mes));
            if ($res)
            {
                return array("result" => 0, "msg" => '成功');
            }
            else
            {
                return array("result" => -1, "msg" => '失败');
            }
        }
    }

    /**
     * 监听端口
     */
    public function listen()
    {
        $serv = new swoole_server(config::TCP_IP, config::TCP_PORT);
        $serv->set(array(
            'worker_num' => 8, //工作进程数量
            //'daemonize'  => config::DAEMONIZE,
            //'task_worker_num' => 4
        ));
        $serv->on('receive', [$this, 'handle']);
        $serv->start();
    }

    /**
     * @function 处理tcp接收到的数据
     * @author CaylaXu
     * @param $serv
     * @param $fd
     * @param $from_id
     * @param $data
     */
    public function handle($serv, $fd, $from_id, $data)
    {
        $serv->send($fd, json_encode($this->push_list($data)));
        $serv->close($fd);
    }

    /**
     * @function 接收记录
     * @User: CaylaXu
     * @param $res
     */
//    public function send_log($res)
//    {
//        $file_name = date("Y-m-d") . 'send_log.txt';
//        $path = '../logs/' . $file_name;
//        //失败数据写入文件
//        $handle = fopen($path, "a+");
//        if (is_array($res))
//        {
//            $log = json_encode($res);
//            $log = date("Y-m-d H:i:s").$log;
//        }
//        else
//        {
//            $log = date("Y-m-d H:i:s").$res;
//        }
//        fwrite($handle, $log . PHP_EOL);
//        fclose($handle);
//    }
}

$Message = new Message();
