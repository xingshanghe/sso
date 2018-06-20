<?php
/** 
* class
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年5月24日上午12:31:30
* @source ServicesController.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

namespace App\Controller;

class ServicesController extends SobeyController
{
    public function index(){
        $where = array('Services.status >'=>0);
        $services = $this->Services->find('all')->where(array('Services.status >'=>0));
        $this->set(compact('services'));
    }
}