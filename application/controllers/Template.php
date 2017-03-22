<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/2/16
 * Time: 18:13
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Template
 * @property template_model template
 * @property Telephone_template_model teltpl
 */
class Template extends My_Controller
{

    public function index()
    {
        $this->render('backend/template', $this->data);
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

        $this->load->model('dal/template_model', 'template');

        if (!$this->input->get('template'))
        {
            $where = array();
        }
        else
        {
            $where = array('Name' => $this->input->get('template'));
        }

        $fields = 'Id, Name, Message';
        $result['draw'] = $draw;
        $result['data'] = $this->template->get($where, $fields, $start, $length);
        $result['recordsFiltered'] = $result['recordsTotal'] = $this->template->get_count($where);

        $this->ajax_return($result);
    }

    public function fetch()
    {
        if (!$this->input->is_ajax_request())
        {
            return;
        }

        $telephone_id = intval($this->input->get('telephone_id'));

        $this->load->model('dal/Template_model', 'template');
        $where = array();
        $fields = 'Id, Name';
        $result = $this->template->fetch($where, $fields);

        //选中的模板
        if ($telephone_id)
        {
            $this->load->model('dal/Telephone_template_model', 'teltpl');

            $where = array('TelephoneId' => $telephone_id,);
            $fields = 'TemplateId';
            $records = $this->teltpl->fetch($where, $fields);
            $tpl_checked = array();
            foreach ($records as $record)
            {
                $tpl_checked[] = intval($record['TemplateId']);
            }
            foreach ($result as &$row)
            {
                if (in_array(intval($row['Id']), $tpl_checked))
                {
                    $row['checked'] = 'checked';
                }
                else
                {
                    $row['checked'] = '';
                }
            }
        }

        $this->ajax_return($result);
    }

    public function add()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post())
        {
            return;
        }

        $name = (string)$this->input->post('name');
        $message = (string)$this->input->post('message');

        $this->load->model('dal/Template_model', 'template');

        $where = array('name' => $name);
        $fields = 'Id';
        $result = $this->template->fetch($where, $fields);
        if ($result)
        {
            $return['status'] = 1;
            $return['message'] = '该名称已存在';
            $this->ajax_return($return);
        }

        $data = array(
            'Name' => $name,
            'Message' => $message,
        );
        $result = $this->template->insert($data);
        if ($result)
        {
            Config::reload();

            $return['status'] = 0;
            $return['message'] = '添加成功';
            $this->ajax_return($return);
        }
        else
        {
            $return['status'] = 1;
            $return['message'] = '添加失败，请稍后再试';
            $this->ajax_return($return);
        }
    }

    public function edit()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post())
        {
            return;
        }

        $id = intval($this->input->post('id'));
        $name = (string)$this->input->post('name');
        $message = (string)$this->input->post('message');

        $this->load->model('dal/Template_model', 'template');

        $where = array(
            'Id !=' => $id,
            'Name' => $name,
        );
        $fields = 'Id';
        $result = $this->template->fetch($where, $fields);
        if ($result)
        {
            $return['status'] = 1;
            $return['message'] = '该名称已存在';
            $this->ajax_return($return);
        }

        $data = array(
            'Name' => $name,
            'Message' => $message,
        );
        $where = array('Id' => $id);
        $result = $this->template->update($data, $where);
        if ($result)
        {
            Config::reload();

            $return['status'] = 0;
            $return['message'] = '修改成功';
            $this->ajax_return($return);
        }
        else
        {
            $return['status'] = 1;
            $return['message'] = '修改失败，请稍后再试';
            $this->ajax_return($return);
        }
    }

}