<?php
/** 
* class
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年5月25日下午1:37:27
* @source Account.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Auth\SobeyPasswordHasher;

class Account extends Entity
{
    
    protected function _setPassword($password)
    {
        return $password;//测试用明文密码
        //return (new SobeyPasswordHasher(array('salt'=>$this->salt)))->hash($password);
    }
    
}
