<?php
/** 
* class
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年5月20日上午11:41:19
* @source SsoController.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Log\Log;

class SsoController extends Controller
{
    public $sessionKey = 'Auth.User';
    
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
        //ie下跨域
        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
    }
    
    /**
     * jsonp响应
     * 客户端到服务端
     */
    public function bye()
    {
        $queryArr = $this->request->query;
        
        $_serialize = array('sso');
        $sso  = array();
        
        $serviceTable = TableRegistry::get('Services');
        $services = $serviceTable->find()->select(array('url','sso'))->where(array('Services.status'=>1))->toArray();
        
        foreach ($services as $value){
            $service_hosts[] = parse_url($value['url'],PHP_URL_HOST);
            $sso[] =  $value['url'].$value['sso'].(strpos($value['sso'], '?')?'&':'?').'action=logout&callback=?';//TODO 部署时需注意
        }
        
        if (in_array(isset($queryArr['d'])?$queryArr['d']:'', $service_hosts)){
            $this->viewClass = 'Json';
            $this->set('_jsonp',true);
            
            $this->set(compact(array_values($_serialize)));
            $this->set('_serialize',$_serialize);
        }else{
            $this->autoRender = false;
        }
    }
    
    /**
     * jsonp响应
     * 客户端到服务端
     */
    public function hello(){
        $queryArr = $this->request->query;
        //$host = $this->request->host();
        $base = $this->request->base;
    
        $serviceTable = TableRegistry::get('Services');
        $services = $serviceTable->find()->select(array('url','sso'))->where(array('Services.status'=>1))->toArray();
        //$services[] = array('url'=>'http://'.$host);//TODO!
    
        $service_hosts = array();
        $sso = array();
        foreach ($services as $value){
            $service_hosts[] = parse_url($value['url'],PHP_URL_HOST);
            $sso[] =  $base.'/setCookie?t='.urlencode($value['url'].$value['sso']).'&callback=?';//TODO 部署时需注意
        }
        if (in_array(isset($queryArr['d'])?$queryArr['d']:'', $service_hosts)){
            $this->viewClass = 'Json';
            $this->set('_jsonp',true);
    
            $_serialize = array('info','sso');
            $info = '<a href="#">退出</a>';
            
            $this->Cookie->delete('lkey');
            $this->Cookie->delete('lvalue');
            $this->Cookie->write('lkey',$queryArr['lkey']);
            $this->Cookie->write('lvalue',$queryArr['lvalue']);
            $time = time();
            $alc = $this->Des->encrypt($time);
            //TODO 本应为访问列表，权限值。
            $alcKey = Configure::read('Security.alc.key');
            $this->Cookie->write($alcKey,$alc);
    
            $this->set(compact(array_values($_serialize)));
            $this->set('_serialize',$_serialize);
        }else{
            $this->autoRender = false;
        }
    
    }
    
    /**
     * jsonp响应
     * 客户端到服务端
     */
    public function setCookie(){
        $queryArr = $this->request->query;
        $url = urldecode($queryArr['t']);
    
        $url = $url.(strpos($url, '?')?'&':'?').'action=login';
        $account = array();
        $alcKey = Configure::read('Security.alc.key');
        
        //根据cookie中 lkey和lvalue去判断查询以及alc中时间确定登录
        if (time() - intval($this->Des->decrypt($this->Cookie->read($alcKey))) <= Configure::read('Security.alc.expires')){
            $lkey = $this->Cookie->read('lkey');
            $lvalue = $this->Cookie->read('lvalue');
            if ($lkey&&$lvalue){
                $where = array($lkey => urldecode($lvalue));
                $AccountTable = TableRegistry::get('Accounts');
                try {
                    $account = $AccountTable->find()->where($where)->first()->toArray();
                } catch (\Exception $e) {
                    //TODO
                }
                
            }
        }
    
        $securityCookieKey  = Configure::read('Security.Cookie.key');//pinId
        $c = array(
            time(),//混淆
            $securityCookieKey=>$this->Des->encrypt($account['id'].'@'.$this->Des->decrypt($this->Cookie->read($alcKey)).'@'.$queryArr['t']),
            'account'=>$account
        );
    
        $queryArr['c'] = $this->Des->encrypt(serialize($c));
        $queryStr = http_build_query($queryArr);
    
        $url .= '&'.$queryStr;
    
        header('Location:'.$url);exit;
    }
    
}
