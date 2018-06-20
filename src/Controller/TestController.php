<?php

/** 
* class
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年5月28日下午4:23:41
* @source Test.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 
namespace App\Controller;

use Cake\Controller\Controller;
//uimtdkvi
class TestController extends Controller
{
    
    public function initialize(){
        parent::initialize();
        //Cookie
        $this->loadComponent('Cookie',[
            'encryption' => false
        ]);
        //Rsa加密解密算法
        $this->loadComponent('Rsa');
        //Des加密解密算法
        $this->loadComponent('Des');
    
    }
    
    
    function index(){
        $this->autoRender = false;
        $p = json_encode(array('string'=>'FJIBQAKSgCjVw8EMtgyPPIKCQFWXCZJgEVzZaICsVUChZSMMrM\/swQ=='));
        debug($p);
        
        debug($this->Des->decrypt('FJIBQAKSgCjVw8EMtgyPPIKCQFWXCZJgEVzZaICsVUChZSMMrM\/swQ=='));
        //$this->Des->decrypt(json_encode(ar));
        
    }
}