<?php

/** 
* class
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年5月24日下午2:04:19
* @source AccountsTable.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

namespace App\Model\Table;


class AccountsTable extends SobeyTable{
    
    public function initialize( array $config )
    {
        $this->hasOne('AccountInfos');
    }
     
}