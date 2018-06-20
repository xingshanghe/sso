<?php
/** 
* Rsa 加密解密
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年5月20日下午11:06:07
* @source RsaComponent.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 
namespace App\Controller\Component;

use Cake\Controller\Component; 
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Filesystem\File;

class RsaComponent extends Component
{
    /**
     * 支持public(公钥文件路径)和private(密钥文件路径)
     */
    protected $_defaultConfig = [
        'public'=>'',   //公钥
        'private'=>''   //密钥
    ];
    protected $_base64 = true;
    
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        //设置公钥文件路径
        $this->_defaultConfig['public'] = Configure::read('App.keys.public');
        //设置密钥文件路径
        $this->_defaultConfig['private'] = Configure::read('App.keys.private');
        parent::__construct($registry, $config);
    }
    
    public function initialize(array $config)
    {
        parent::initialize($config);
        //配置公钥
        $file = new File($this->_config['public']);
        Configure::write('Security.Rsa.public',openssl_pkey_get_public($file->read()));
        $file->close();
        //配置密钥
        $file = new File($this->_config['private']);
        Configure::write('Security.Rsa.private',openssl_pkey_get_private($file->read()));
        $file->close();
    }
        
    
    /**
     * 加密
     * @param string $data
     * @param string $type
     */
    public function encrypt($data,$type="private"){
        $encrypt = false;
        if (!in_array($type, array('private','public'))){
            $type = 'private';
        }
        if ($type == 'private'){
            openssl_private_encrypt($data,$encrypt,Configure::read('Security.Rsa.private'));
        }else{
            openssl_public_encrypt($data,$encrypt,Configure::read('Security.Rsa.public'));
        }
        if ($this->_base64){
            $encrypt = $encrypt?base64_encode($encrypt):false;
        }
        return $encrypt;
    }
    
    /**
     * 解密
     * @param string $data
     * @param string $type
     */
    public function decrypt($data,$type="public"){
        $decrypt= false;
        if (!in_array($type, array('private','public'))){
            $type = 'private';
        }
        if ($this->_base64){
            $data = base64_decode($data);
        }
        if ($type == 'public'){
            openssl_public_decrypt($data,$decrypt,Configure::read('Security.Rsa.public'));
        }else{
            openssl_private_decrypt($data,$decrypt,Configure::read('Security.Rsa.private'));
        }
        return $decrypt;
    }
    
}