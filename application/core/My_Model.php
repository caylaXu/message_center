<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/2/16
 * Time: 14:15
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class My_Model extends CI_Model
{
    protected $tbl_name = '';

    public function __construct($tbl_name = '')
    {
        parent::__construct();
        $this->load->database();
        $this->tbl_name = $tbl_name;
    }

    /**
     * 查找
     * @param array $where
     * @param string $field
     * @return array
     */
    public function fetch($where = array(), $field = '*')
    {
        if (is_string($field))
        {
            $this->db->select($field);
        }
        if ($where)
        {
            $this->db->where($where);
        }
        $query = $this->db->get($this->tbl_name);

        return $query ? $query->result_array() : array();
    }

    /**
     * 分页查找
     * @param array $where
     * @param string $field
     * @param int $start
     * @param int $length
     * @return array
     */
    public function get($where = array(), $field = '*', $start = 0, $length = 50)
    {
        if(is_numeric($where))
        {
            $where = array('Id' => $where);
        }
        $this->limit($length, $start);
        $this->order_by('Id');
        $result = $this->fetch($where, $field);

        return $result;
    }

    /**
     * 获取符合条件的记录数
     * @param $where
     */
    public function get_count($where = array())
    {
        $this->where($where);

        return $this->count();
    }

    /**
     * 根据Id集查找备注映射
     * @param array $ids
     * @return array
     */
    public function get_by_ids($ids = array())
    {
        $this->db->where_in('Id', $ids);
        $query = $this->db->get($this->tbl_name);
        $result = $query->result_array();
        $result = $this->reindex($result);

        return $result;
    }

    public function insert($data)
    {
        $query = $this->db->insert($this->tbl_name, $data);
        return $query ? $this->db->insert_id() : FALSE;
    }

    public function insert_batch($data)
    {
        $query = $this->db->insert_batch($this->tbl_name, $data);
        return $query;
    }

    public function delete($where)
    {
        return $this->db->delete($this->tbl_name, $where);
    }

    public function update($data, $where)
    {
        $this->db->update($this->tbl_name, $data, $where);

        return $this->db->affected_rows();
    }

    public function limit($rows, $offset = 0)
    {
        $this->db->limit($rows, $offset);
        return $this;
    }

    public function order_by($field, $order = 'ASC')
    {
        $this->db->order_by($field, $order);
        return $this;
    }

    public function where($key, $value = NULL, $escape = NULL)
    {
        $this->db->where($key, $value, $escape);
    }

    public function where_in($field, $range = array())
    {
        $this->db->where_in($field, $range);
        return $this;
    }

    public function like($field, $match = '', $side = 'both', $escape = NULL)
    {
        $this->db->like($field, $match, $side, $escape);
        return $this;
    }

    public function count()
    {
        $count = $this->db->count_all_results($this->tbl_name, FALSE);
        return $count;
    }

    /**
     * 按Id重新索引
     * @param $rows
     * @return array
     */
    public function reindex($rows)
    {
        $result = array();
        foreach($rows as $row)
        {
            if(!isset($row['Id']))
            {
                return $rows;
            }
            $result[$row['Id']] = $row;
        }

        return $result;
    }

}