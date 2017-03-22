<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/5/17
 * Time: 11:27
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Message_log_bll extends CI_Model
{
    private $fields = 'Id, Type, ToUsers, Content, Attr, ReceiptTime, Response';

    public function __construct()
    {
        parent::__construct();
    }

    public function filter($input = array())
    {
        $this->load->model('dal/Rlt_time_table_model' ,'rlt_time_table');
        $rlt_tables = $this->rlt_time_table->get_rlt_tables($input['start'], $input['end']);
        if (!$rlt_tables)
        {
            return array();
        }

        $this->load->model('dal/Message_log_model' ,'message_log');
        $condition = $this->_build_condition($input);
        $union = $this->_build_union($rlt_tables, $condition, $input['offset'], $input['length']);
        $sql = "SELECT {$this->fields} " .
            "FROM {$union} " .
            "WHERE {$condition['where']} " .
            "ORDER BY ReceiptTime DESC LIMIT {$input['offset']}, {$input['length']}";
        $query = $this->db->query($sql, $condition['bind']);
        $result = $query->result_array();

        return $result;
    }

    /**
     * 获取符合条件的总记录数
     * @param array $input
     * @return mixed
     */
    public function get_cnt($input = array())
    {
        $rlt_tables = $this->rlt_time_table->get_rlt_tables($input['start'], $input['end']);
        $condition = $this->_build_condition($input);

        if (!$rlt_tables)
        {
            return array();
        }
        $count = 0;
        foreach ($rlt_tables as $table)
        {
            $sql = "SELECT COUNT(*) as count " .
                "FROM {$table} " .
                "WHERE {$condition['where']} " .
                "ORDER BY ReceiptTime DESC";
            $query = $this->db->query($sql, $condition['bind']);
            $count += intval($query->row_array()['count']);
        }

        return $count;
    }

    /**
     * 构建查询条件
     * @param array $input
     * @return array
     */
    private function _build_condition($input = array())
    {
        static $condition = array();

        if ($condition)
        {
            return $condition;
        }

        $condition['where'] = ' ';
        $condition['bind'] = array();

        $start = $input['start'];
        $end = $input['end'];
        $tos = isset($input['tos']) ? $input['tos'] : FALSE;
        $content = isset($input['content']) ? $input['content'] : FALSE;
        $type = isset($input['type']) ? $input['type'] : FALSE;
        if ($start)
        {
            $condition['where'] .= 'ReceiptTime >= ? AND ';
            $condition['bind'][] = $start;
        }
        if ($end)
        {
            $condition['where'] .= 'ReceiptTime <= ? AND ';
            $condition['bind'][] = $end;
        }
        if ($tos)
        {
            $condition['where'] .= 'ToUsers LIKE ? AND ';
            $condition['bind'][] = '%' . $tos . '%';
        }
        if ($content)
        {
            $condition['where'] .= 'Content LIKE ? AND ';
            $condition['bind'][] =  '%' . $content . '%';
        }
        if ($type)
        {
            $condition['where'] .= 'Type = ? AND ';
            $condition['bind'][] = $type;
        }

        $condition['where'] .= '1=1';

        return $condition;
    }

    /**
     * 构建union子句
     * @param $tables
     * @param $condition
     * @param $offset
     * @param $length
     * @return string
     */
    private function _build_union($tables, &$condition, $offset = 0, $length = 0)
    {
        if (!is_array($tables) || !$tables)
        {
            return FALSE;
        }

        $union = "( ";
        $bind = $condition['bind'];
        foreach ($tables as $table)
        {
            $union .= "( SELECT {$this->fields} FROM {$table} WHERE {$condition['where']} " .
                "ORDER BY ReceiptTime DESC LIMIT " . ($offset+$length) . ") UNION ALL ";
            $condition['bind'] = array_merge($condition['bind'], $bind);
        }
        $union = substr($union, 0, -10);
        $union .= ") t";

        return $union;
    }

}