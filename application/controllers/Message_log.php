<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/5/17
 * Time: 11:05
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Message_log extends My_Controller
{
    public $send_type = array('sms'=>'短信','email'=>'邮件','app_push'=>'APP推送');//推送方式枚举

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->render('backend/message_log', $this->data);
    }

    public function filter()
    {
        if (!$this->input->is_ajax_request() || !$this->input->get())
        {
            return;
        }

        $draw = intval($this->input->get('draw'));
        $input['offset'] = intval($this->input->get('start'));
        $input['length'] = intval($this->input->get('length'));
        $input['tos'] = $this->input->get('Tos');
        $input['content'] = $this->input->get('Content');
        $input['start'] = $this->input->get('Start');
        $input['end'] = $this->input->get('End');
        $input['type'] = $this->input->get('Type');

        //检验输入
        $input['start'] = $input['start'] ?
            strtotime($input['start']) :
            strtotime('-7 day', time());
        $input['end'] = $input['end'] ?
            strtotime('+1 minutes', strtotime($input['end'])) - 1 :
            time();
        if (!$input['start'] || !$input['end'] || $input['start'] > $input['end'])
        {
            $result['draw'] = $draw;
            $result['recordsFiltered'] = $result['recordsTotal'] = 0;
            $result['data'] = array();
            $this->ajax_return($result);
        }

        $this->load->model('bll/Message_log_bll', 'message_log_bll');
        $data = $this->message_log_bll->filter($input);
        foreach ($data as &$row)
        {
            $row['ToUsers'] = json_decode($row['ToUsers'], TRUE);
            $row['ReceiptTime'] = date('Y-m-d H:i:s', $row['ReceiptTime']);
            if ($row['Type'] === 'email')
            {
                $row['Content'] = strip_tags($row['Content']);
                $row['Attr'] = strip_tags($row['Attr']);
            }
            $row['Type'] = $this->send_type[$row['Type']];
        }

        $result['draw'] = $draw;
        $result['data'] = $data;
        $result['recordsFiltered'] = $result['recordsTotal'] = $this->message_log_bll->get_cnt($input);

        $this->ajax_return($result);
    }
}