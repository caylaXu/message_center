<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/2/16
 * Time: 10:13
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Telephone
 * @property telephone_model telephone
 * @property Telephone_template_model teltpl
 */
class Telephone extends My_Controller
{

    public function index()
    {
        $this->render('backend/telephone', $this->data);
    }

    public function filter()
    {
        if (!$this->input->is_ajax_request() || !$this->input->get())
        {
            return;
        }

        $draw = intval($this->input->get('draw'));
        $start = intval($this->input->get('start'));
        $length = intval($this->input->get('length'));

        $this->load->model('dal/telephone_model', 'telephone');

        if (!$this->input->get('telephone'))
        {
            $where = array();
        }
        else
        {
            $where = array('Telephone' => $this->input->get('telephone'));
        }

        $fields = 'Id, Telephone, Owner';
        $result['draw'] = $draw;
        $result['data'] = $this->telephone->get($where, $fields, $start, $length);
        $result['recordsFiltered'] = $result['recordsTotal'] = $this->telephone->get_count($where);

        $this->ajax_return($result);
    }

    public function add()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post())
        {
            return;
        }

        $telephone = intval($this->input->post('telephone'));
        $owner = (string)$this->input->post('owner');
        $template = $this->input->post('template') ? $this->input->post('template') : array();

        $this->load->model('dal/Telephone_model', 'telephone');
        $this->load->model('dal/Telephone_template_model', 'teltpl');

        //判断是否存在
        $where = array('Telephone' => $telephone);
        $fields = 'Id';
        $result = $this->telephone->fetch($where, $fields);
        if ($result)
        {
            $return['status'] = 1;
            $return['message'] = '该手机号已存在';
            $this->ajax_return($return);
        }

        //插入新记录
        $data = array(
            'Telephone' => $telephone,
            'Owner' => $owner,
        );
        $result = $this->telephone->insert($data);
        if (!$result)
        {
            $return['status'] = 1;
            $return['message'] = '添加失败';
            $this->ajax_return($return);
        }

        //插入关联记录
        $data = array();
        $row = array();
        foreach ($template as $tplId)
        {
            $row['TelephoneId'] = $result;
            $row['TemplateId'] = intval($tplId);
            $data[] = $row;
        }
        if($data)
        {
            $this->teltpl->insert_batch($data);
        }

        Config::reload();

        $return['status'] = 0;
        $return['message'] = '添加成功';
        $this->ajax_return($return);
    }

    public function edit()
    {
        $id = intval($this->input->post('id'));
        $telephone = intval($this->input->post('telephone'));
        $owner = (string)$this->input->post('owner');
        $template = $this->input->post('template') ? $this->input->post('template') : array();

        $this->load->model('dal/Telephone_model', 'telephone');
        $this->load->model('dal/Telephone_template_model', 'teltpl');

        //修改
        $data = array(
            'Telephone' => $telephone,
            'Owner' => $owner,
        );
        $where = array('Id' => $id);
        $this->telephone->update($data, $where);

        //删除旧关联
        $where = array('TelephoneId' => $id);
        $result = $this->teltpl->delete($where);
        if (!$result)
        {
            $return['status'] = 1;
            $return['message'] = '修改失败';
            $this->ajax_return($return);
        }

        //插入新关联
        if ($template)
        {
            $data = array();
            $row = array();
            foreach ($template as $tplId)
            {
                $row['TelephoneId'] = $id;
                $row['TemplateId'] = intval($tplId);
                $data[] = $row;
            }
            $this->teltpl->insert_batch($data);
        }

        Config::reload();

        $return['status'] = 0;
        $return['message'] = '修改成功';
        $this->ajax_return($return);

    }

}