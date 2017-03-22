<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/5/17
 * Time: 14:47
 */

class Rlt_time_table_model extends My_Model
{
    public function __construct()
    {
        parent::__construct('RltTimeTable');
    }

    public function get_rlt_tables($start, $end)
    {
        $sql = "SELECT TableName FROM RltTimeTable WHERE " .
            "(MinTime <= {$start} AND MaxTime >= {$start}) OR " .
            "(MinTime <= {$end} AND MaxTime >= {$end}) OR " .
            "(MinTime >= {$start} AND MaxTime <= {$end}) OR " .
            "(MinTime <= {$start} AND MaxTime >= {$end})";
        $query = $this->db->query($sql);
        $tables = $query->result_array();

        return array_column($tables, 'TableName');
    }
}