<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/2/16
 * Time: 15:28
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Template_model extends My_Model
{
    public function __construct()
    {
        parent::__construct('Template');
    }
}