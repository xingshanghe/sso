<?php
/** 
* 密码
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年5月21日下午6:45:01
* @source SobeyPasswordHasher.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

namespace App\Auth;

use Cake\Auth\AbstractPasswordHasher;

class SobeyPasswordHasher extends AbstractPasswordHasher
{
    protected $_defaultConfig = [
        'salt'=>''
    ];
    
    public function __construct(array $config = []){
        parent::__construct($config);
    }
    
    public function hash($password){
        //return $password;//测试用明文密码
        //加入对salt支持
        return md5(md5($password).$this->_config['salt']);
    }
    
    public function check($password, $hashedPassword){
       //return $password === $hashedPassword;//测试用明文密码
        //对salt支持
        return md5(md5($password).$this->_config['salt']) ===  $hashedPassword;
    }
    
    public function checkMd5($md5pwd, $hashedPassword){
        //return $password === $hashedPassword;//测试用明文密码
        //对salt支持
        return md5($md5pwd.$this->_config['salt']) ===  $hashedPassword;
    }
    
    public function setSalt($salt = null){
        if(!is_null($salt))
        $this->_config['salt'] = $salt;
    }
    
    
}