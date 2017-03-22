<?php
require_once 'queue.php';
require_once 'storage.php';
require_once '../third_party/message/config.php';
require_once '../third_party/message/Message_factory.php';
class send_app_push
{
	function __construct()
	{
        //$this->test();
		$this->send_app_push();
	}

	/**
	 * @function 发送APP推送
	 * @param unknown $message
	 */
	public function send_app_push()
	{
		set_time_limit(0);
		$queue = new queue();
		while (TRUE)
		{
			$json_str = $queue->pop(config::MessageTypeAppPush);
			if ($json_str)
			{
				$mes = json_decode($json_str, true);
				$factory = new Message_factory($mes);
				$factory->createMessage();
				if(!empty($factory->obj))
				{
					   $res = $factory->send_message();
					   $this->error($res,$mes);
				}
				else
				{
						$this->error(array('result'=>-1,'msg'=>'客户端请求格式错误'),$mes);
				}
			}
			else
			{
//				$queue->set_php_life();
				sleep(1);
			}
		}
	}

	public function error($res,$mes)
	{
		$to = isset($mes['Receiver']) ? $mes['Receiver'] : '无';
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
			'Type' => config::MessageTypeAppPush,
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

$send_app_push = new send_app_push();

