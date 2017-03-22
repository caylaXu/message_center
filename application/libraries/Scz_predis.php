<?php
$dir = dirname(__FILE__);
require_once $dir.'/../phplibs/predis-1.0/autoload.php';

class Scz_predis
{

        public static $predis;
        protected static $default_redis_config = array(
            'host'     => '127.0.0.1',
            'port'     => 6379,
        );

        function __construct()
        {
                 $this->inti_redis();
        }
        /**
         * @todo Description初始化redis的配置文件，并启动redis
         * @Tips :此方法依赖于ci
         */
        function inti_redis()
        {
                $CI =& get_instance();
                $CI->config->load('redis', TRUE, TRUE);
                $redis_config =$CI->config->item('redis');
                
                if (isset($redis_config['use_redis_config']))
                {
                        if ($redis_config['use_redis_config'] == 'redis_config')
                        {
                                $server=array_merge(self::$default_redis_config,$redis_config['redis_config']);
                               self::$predis = new Predis\Client($server);
                        }
                        else if($redis_config['use_redis_config'] == 'redis_cluster_config')
                        {
                                $server=$redis_config['redis_cluster_config'];
                                $options = array('cluster' => 'redis');
                                self::$predis = new Predis\Client($server,$options);
                        }
                }
        }

}
