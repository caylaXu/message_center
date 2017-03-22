<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Change_config extends My_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('bll/redis_bll');
    }

    public function change()
    {
        if(isset($_POST['ChannelNames']))
        {
            if(empty($_POST['ChannelNames']))
            {
                echo json_encode(array('resulr' => -1, 'msg' => '参数错误'));
                exit;
            }
            $path = APPPATH.'third_party'.DS.'message'.DS.'config.php';
            $origin_str = file_get_contents($path);
            $msg = preg_replace("/(MessageService=').*(';)/", "$1".$_POST['ChannelNames']."$2", $origin_str);
            if(file_put_contents($path, $msg))
            {
                $this->redis_bll->my_set('MessageChannel',$_POST['ChannelNames']);
                echo json_encode(array('result' => 0, 'msg' => '修改成功'));
                exit;
            }
            echo json_encode(array('result' => -1, 'msg' => '修改失败'));
            exit;
        }
//        $this->load->view('backend/change_config.html');
        $this->render('backend/change_config', $this->data);
    }

    public function get()
    {
        $path = APPPATH.'third_party'.DS.'message'.DS.'config.php';
        $origin_str = file_get_contents($path);
        preg_match_all("/MessageService='(.*)';/i",$origin_str,$matches);
        if(isset($matches[1][0]))
        {
            echo json_encode(array('resulr' => 0, 'msg' => '获取成功','data'=>$matches[1][0]));
            exit;
        }
        echo json_encode(array('resulr' => -1, 'msg' => '获取失败','data'=>''));
    }
}
