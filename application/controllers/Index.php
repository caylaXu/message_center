<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2015/2/18
 * Time: 10:21
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Index
 */
class Index extends My_Controller
{
    public function logout()
    {
        $this->sso->log_out();
    }
}