<?php
/** 
* Des加密解密
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年5月21日下午3:02:42
* @source DesComponent.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;

class DesComponent extends Component
{
    
    protected $_defaultConfig = [
        'key'=>'',
        'iv'=>0,//8位
    ];
    
    protected $_key;
    protected $_iv;
    
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        //设置公钥文件路径
        $this->_defaultConfig['key'] = Configure::read('Security.Des.key');
        parent::__construct($registry, $config);
    }
    
    public function initialize(array $config)
    {
        parent::initialize($config);
        
        $this->_key = $this->_config['key'];
        
        if ($this->_config['iv'] == 0){
            $this->_iv = $this->_config['key'];
        }else{
            $this->_iv = $this->_config['iv'];//mcrypt_create_iv( mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_CBC), MCRYPT_DEV_RANDOM);
            //$this->_iv = mcrypt_create_iv( mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_CBC), MCRYPT_DEV_RANDOM);
        }
    }
    
    /**
     * 加密
     * @param string $data
     * @return string
     */
    public function encrypt($data){
        $size = mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_CBC);
        $data = $this->_pkcs5Pad($data, $size);
        $encode =  mcrypt_cbc(MCRYPT_DES, $this->_key, $data, MCRYPT_ENCRYPT, $this->_iv);
        //$encode = strtoupper( bin2hex( mcrypt_cbc(MCRYPT_DES, $this->_key, $data, MCRYPT_ENCRYPT, $this->_iv)));
        return trim(chop(base64_encode($encode)));
    }
    
    /**
     * 解密
     * @param string $data
     * @return Ambigous <boolean, string>
     */
    public function decrypt($data){
        $data = trim(chop(base64_decode($data)));
        $data = mcrypt_cbc(MCRYPT_DES, $this->_key, $data, MCRYPT_DECRYPT, $this->_iv);
        
        $data = $this->_pkcs5Unpad($data);
        return $data;
    }
    
    private function _pkcs5Pad($text,$block_size){
        $pad = $block_size - (strlen($text) % $block_size);
        return $text.str_repeat(chr($pad),$pad);
    }
    
    private function _pkcs5Unpad($text){
        
        $pad = ord($text{strlen($text) - 1});
        
        if ($pad > strlen($text)){
            return false;
        }
        
        if(strspn($text, chr($pad), strlen($text)-$pad) != $pad){
            return false;
        }
        return substr($text, 0,-1*$pad);
    }
    
}