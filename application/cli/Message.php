<?php

/**
 * Class Message 消息服务器
 * @author CaylaXu <caylaxu@motouch.cn>
 */
error_reporting(E_ALL & ~E_NOTICE);
require_once '../third_party/message/config.php';
require_once '../third_party/message/Message_factory.php';
class Message{
    public function __construct()
    {
        $this->listen();
    }

    /**
     * 监听端口
     */
    public function listen()
    {
        $serv = new swoole_server(config::TCP_IP, config::TCP_PORT);
        $serv->set(array('worker_num' => 8, /*工作进程数量*/
            //'daemonize'  => config::DAEMONIZE,
            'task_worker_num' => 8));
        $serv->on('receive', [$this, 'handle']);
        $serv->on('Task', [$this, 'task']);
        $serv->on('Finish', [$this, 'finish']);
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
        $data = json_decode($data, true);
        if (!in_array($data['Type'], config::$MessageTypeArray)) {
            $return_msg = array("result"=>-1,"msg"=>'发送类型暂时只支持sms(信息),email(邮件),app_push(app消息推送)');
            $serv->send($fd, json_encode($return_msg));
            $serv->close($fd);

        } else {
            $serv->task(json_encode($data));
            $return_msg =  array("result"=>0,"msg"=>'成功');
            $serv->send($fd, json_encode($return_msg));
            $serv->close($fd);
        }
    }

    public function task($serv, $task_id, $from_id, $data)
    {
        $mes = json_decode($data, true);
        $factory = new Message_factory($mes);
        $factory->createMessage();
        if(!empty($factory->obj))
        {
            $res = $factory->send_message();
            $this->error($res,$data);
        }
        else
        {
            $this->error(array('result'=>-1,'msg'=>'客户端请求格式错误','data'=>$data));
        }
        $serv->finish(date('Y-m-d H:i:s'));
    }

    public function finish($serv, $task_id, $data)
    {
        print_r("finish:".$data.PHP_EOL);
    }

    public function error($res)
    {
        if(isset($res['result']) && $res['result'] == 0)
        {
            return;
        }
        else if(isset($res['State']) && $res['State'] == 0)
        {
            return;
        }
        else if($res == 1)
        {
            return;
        }
        $file_name = date("Y-m-d").'txt';
        $path = '../logs/'.$file_name;
        //失败数据写入文件
        $handle = fopen($path, "a+");
        if(is_array($res))
        {
            $res['Time'] =  date("Y-m-d H:i:s");
            $error = json_encode($res);
        }
        else
        {
            $error = $res;
            $error .=date("Y-m-d H:i:s");
        }
        fwrite($handle,$error.PHP_EOL);
        fclose($handle);
    }
}
$Message = new Message();
