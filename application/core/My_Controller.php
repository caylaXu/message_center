<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/2/16
 * Time: 10:42
 */

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/third_party/message/config.php';

/**
 * Class My_Controller
 */
class My_Controller extends CI_Controller
{
    protected $layout = 'backend/layout/main';

    protected $param = array();
    protected $data = array();

    public function __construct()
    {
        parent::__construct();

        //过滤输入
        array_walk_recursive($_POST, function (&$value)
        {
            $value = trim($value);
        });
        array_walk_recursive($_GET, function (&$value)
        {
            $value = trim($value);
        });

        //检查登录态
        $this->load->library("sso", array('system_id' => $this->get_system_id()));
        if (!$this->sso->check_login())
        {
            if (!$this->input->is_ajax_request())
            {
                $this->sso->request_login();
            }
            else if($this->input->get('draw'))
            {
                $data['draw'] = intval($this->input->get('draw'));
                $data['data'] = array();
                $data['recordsTotal'] = $data['recordsFiltered'] = 0;
                $data['error'] = '请先登录';
                $this->ajax_return($data);
            }
            else
            {
                $this->ajax_return(-100, '请先登录');
            }
        }
        $this->data['account'] = $this->sso->account();

        //检查权限
        $controller = $this->router->class;
        $action = $this->router->method;
        $power = $controller . '_' . $action;
        $power_list = array(
            'telephone_index',
            'template_index',
        );
        if (in_array($power, $power_list))
        {
            $params = array(
                'id' => 1,
                'account' => $this->sso->account(),
                'system_id' => $this->get_system_id(),
            );
            $this->load->library('power_manager', $params);
            $this->power_manager->query_by_scene('message_admin');
            $ret = $this->power_manager->check_rights($power);
            if (!$ret)
            {
                if (!$this->input->is_ajax_request())
                {
                    $this->show_msg('你没有该权限');
                }
                else if($this->input->get('draw'))
                {
                    $data['draw'] = intval($this->input->get('draw'));
                    $data['data'] = array();
                    $data['recordsTotal'] = $data['recordsFiltered'] = 0;
                    $data['error'] = '你没有该权限';
                    $this->ajax_return($data);
                }
                else
                {
                    $this->ajax_return(100, '你没有该权限');
                }
            }
        }

    }

    private function get_system_id()
    {
        $system_id = 0;

        switch (ENVIRONMENT)
        {
            case 'development':
                $system_id = 8;
                break;
            case 'testing':
                $system_id = 7;
                break;
            case 'tproduction':
                $system_id = 7;
                break;
            case 'production':
                $system_id = 8;
                break;
            default:
                break;
        }

        return $system_id;
    }

    /**
     * Ajax方式返回数据到客户端
     * @param $data
     */
    protected function ajax_return($data)
    {
        header('Content-Type:application/json; charset=utf-8');
        die(json_encode($data));
    }

    /**
     * 显示消息
     * @param $msg
     * @param string $view
     */
    protected function show_msg($msg, $view = 'backend/message')
    {
        $this->data['msg'] = $msg;
        $this->render($view, $this->data);
        exit;
    }

    /**
     * 渲染视图
     * @param string $file
     * @param array $viewData
     * @param array $layoutData
     */
    protected function render($file = NULL, $viewData = array(), $layoutData = array())
    {
        if ($file)
        {
            $data['content'] = $this->load->view($file, $viewData, TRUE);
            $data['layout'] = $layoutData;
            die($this->load->view($this->layout, $data, TRUE));
        }
        else
        {
            die($this->load->view($this->layout, $viewData, TRUE));
        }
    }

}