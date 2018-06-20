<?php
/** 
* 认证组件，修改为cookie认证
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年5月22日下午8:39:23
* @source AuthComponent.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 
namespace App\Controller\Component;

use Cake\Controller\Component\AuthComponent as Base;
use Cake\Core\Configure;
use Cake\Utility\Hash;

class AuthComponent extends Base{
    
    /**
     * 加载Rsa组件
     * @var unknown
     */
    public $components = ['RequestHandler', 'Flash','Cookie','Rsa','Des'];
    
    /**
     * (non-PHPdoc)
     * @see \Cake\Controller\Component\AuthComponent::initialize()
     */
    public function initialize( array $config)
    {
        parent::initialize($config);
        $this->Cookie->config([
            'encryption'=>false,
        ]);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Cake\Controller\Component\AuthComponent::setUser()
     */
    public function setUser(array $user)
    {
        $alcKey = Configure::read('Security.alc.key');
        
        //写入cookie值 账号id@alc  的rsa加密(alc暂时起混淆作用)
        $this->Cookie->delete(Configure::read('Security.Cookie.key'));
        $this->Cookie->write(Configure::read('Security.Cookie.key'),$this->Des->encrypt($user['id'].'@'.$this->Des->decrypt($this->Cookie->read($alcKey)).'@'.$this->request->domain()));
        
        $this->Cookie->delete($alcKey);
        //$this->Cookie->write($alcKey,$this->Des->encrypt(time()));
        
        parent::setUser($user);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Cake\Controller\Component\AuthComponent::user()
     */
    public function user($key = null)
    {
        if (!empty($this->_user)) {
            $user = $this->_user;
        } elseif ($this->sessionKey && $this->session->check($this->sessionKey)) {
            $user = $this->session->read($this->sessionKey);
            
            //增加对cookie的校验
            if (!$this->checkCookie($user)){
                return null;
            }
            
        } else {
            return null;
        }
        if ($key === null) {
            return $user;
        }
        return Hash::get($user, $key);
    }
    
    /**
     * 验证cookie
     * @param array $user
     * @return boolean
     */
    private function checkCookie(array $user)
    {
        $securityCookieKey  = Configure::read('Security.Cookie.key');
        
        $securityCookie = $this->Cookie->check($securityCookieKey)?$this->Cookie->read($securityCookieKey):false;
        $securityCookieArr = explode('@', $this->Des->decrypt($securityCookie));
        
        
        return $securityCookie && isset($user['id']) && ($securityCookieArr[0] == $user['id']);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Cake\Controller\Component\AuthComponent::logout()
     */
    public function logout(){
        //增加对cookie的删除
        $this->Cookie->delete(Configure::read('Security.Cookie.key'));
        $this->Cookie->delete(Configure::read('Security.alc.key'));
        return parent::logout();
    }
}