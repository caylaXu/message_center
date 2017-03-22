<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//$config['socket_type'] = 'tcp'; //`tcp` or `unix`
//$config['socket'] = '/var/run/redis.sock'; // in case of `unix` socket type
//$config['host'] = '127.0.0.1';
//$config['password'] = NULL;
//$config['port'] = 6379;
//$config['timeout'] = 0;
$config['use_redis_config']='redis_config';
$config['redis_config']=array('socket_type'=>'tcp','host'=>'127.0.0.1','password'=>'******','port'=>6379,'timeout'=>0);
$config['redis_cluster_config']=array(   
                'tcp://127.0.0.1:7000',  
                'tcp://127.0.0.1:7001',  
                'tcp://127.0.0.1:7002',  
                'tcp://127.0.0.1:7003',  
                'tcp://127.0.0.1:7004',  
                'tcp://127.0.0.1:7005');
