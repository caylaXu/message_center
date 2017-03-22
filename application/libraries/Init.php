<?php
/**
 * Created by PhpStorm.
 * User: Jentely
 * Date: 2015/11/2
 * Time: 15:43
 */
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'production');
}

include('Power_manager.php');

//场景定义，'场景标识' => array('name'=>'场景别名', 'parent'=>'父场景标识')
$scenes = array(
    'message_admin' => array('name'=>'消息管理', 'parent'=>''),
);

//权限定义，'权限标识' => array('name'=>'权限别名', 'scene'=>'所属场景标识')
$rights = array(
    'telephone_index'   => array('name'=>'电话管理', 'scene'=>'message_admin'),
    'template_index'    => array('name'=>'模板管理', 'scene'=>'message_admin'),
);

$params = array(
    'id' => 1, //暂时用不到，请保持默认值
    'account' => 'peter', //系统管理子账号，请向权限管理系统负责人索取
    'system_id' => 8, //系统编号，在权限管理系统里创建系统后生成
);

$pm = new Power_manager($params);

$result1 = $pm->init_scenes(array('scenes' => json_encode($scenes)));
var_dump($result1);
echo "\n";

$result2 = $pm->init_rights(array('rights' => json_encode($rights)));
var_dump($result2);
echo "\n";