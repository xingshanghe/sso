<?php
/** 
* 索贝云服务账户系统基类
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年5月19日下午9:49:37
* @source SobeyController.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

namespace App\Controller;


class SobeyController extends AppController
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
        
        $this->loadComponent('Auth',[
            'loginAction' => [
                'controller' => 'Accounts',
                'action' => 'login',
            ],
            'authenticate' => [
                'SobeyForm' => [
                    'userModel'=>'Accounts',
                    //'scope' =>  ['Accounts.status >'=>0],
                    'passwordHasher' => [
                        'className' => 'Sobey',
                    ],
                    'returnFields' => [
                        'id','loginname','email','mobile','password','salt'
                    ]
                ]
            ],
        ]);
        
    }
    
    
    
    
}