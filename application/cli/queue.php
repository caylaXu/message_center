<?php
require_once '../third_party/message/config.php';
//require_once '../application/helpers/common_helper.php';
class queue
{
	function __construct()
	{

	}
	/**
	 * @function 入队
	 * @param unknown $message
	 */
	public function push($queue_name,$message)
	{
		$redis = new Redis();
		$redis->pconnect(config::REDIS_HOST,config::REDIS_PORT);
		$redis->auth(config::REDIS_PASS);
//		$redis->close();
		return $redis->lPush($queue_name, $message);
	}
	
	/**
	 * @function 出队
	 * @param unknown $message
	 */
	public function pop($queue_name)
	{
		$redis = new Redis();
		$redis->pconnect(config::REDIS_HOST,config::REDIS_PORT);
		$redis->auth(config::REDIS_PASS);
		$message = $redis->rPop($queue_name);
//		$redis->close();
		return $message;
	}

	public function set_php_life()
	{
		$redis = new Redis();
		$redis->pconnect(config::REDIS_HOST,config::REDIS_PORT);
		$redis->auth(config::REDIS_PASS);
		$message = $redis->set('php_life',date('Y-m-d H:i:s'));
		return $message;
	}
}
