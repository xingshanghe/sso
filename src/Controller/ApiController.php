<?php
/** 
* 索贝云服务账户系统 对外接口类，提供json和jsonp接口调用
* 
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年5月20日下午1:55:28
* @source ApisController.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

namespace App\Controller;


use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use App\Auth\SobeyPasswordHasher;
use Cake\Log\Log;
use Cake\Network\Http\Client;
use Cake\Utility\Text;

class ApiController extends Controller
{
    public $sessionKey = 'Auth.User';
    private $_data = null;
    private $_error = null;
    private $_serialize = array('code','msg','data');
    private $_code = 0;
    private $_msg = "";
    private $_infoFields = array('id','loginname','sex','username','email','mobile','password','md5pwd','salt');
    
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
        $this->loadComponent('RequestHandler');
    
        //获取参数
        $this->_data = $this->_getData();
        //获取vboss接口地址
        $this->_api_vboss = Configure::read('Api.vboss');
        
        /*
        if (!$this->_checkToken($this->request->query('_token'))){
            exit(json_encode(['code'=>'00005','msg'=>$this->_getMsg('00005')]));
        }
        */
    }
    
    //校验token是否过期
    protected function _checkToken($token){
        $_token_plaintext = $this->Des->decrypt($token);
        $_token_arr = explode('@', $_token_plaintext);
        
        return isset($_token_arr[1])&&(time() < $_token_arr[1]);
    }
    
    
    public function getToken()
    {
        $this->viewClass = 'Json';
        $code = $this->_code;
        if ($this->_data && is_array($this->_data)){
        
            if (isset($this->_data['app'])&&isset($this->_data['key'])){
                
                $apps = ['college','sobeyyun','yiqibian'];
                $_check_result = false;
                //TODO，根据key校验apps
                $_check_result = true;
                if ($_check_result){
                    define('TOKEN_OVERDUE', 120);//过期时间120秒
                    
                    $_current_time = time();
                    $_expired_time = $_current_time+TOKEN_OVERDUE;
                    //明文
                    $_token_plaintext = $_expired_time;
                    //加入uuid混淆
                    $data['token'] = $this->Des->encrypt(Text::uuid().'@'.$_token_plaintext);
                }
        
            }else{
                $code = '10002';
                $msg = $this->_getMsg($code);
            }
        
        }else{
            //post 数据空
            $this->_error[] = Configure::read('MSG.00004');
            $code = '00004';
            $msg = $this->_getMsg($code);
        }
        
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }
    
    public function exits(){
        $this->viewClass = 'Json';
        $code = $this->_code;
        
        $host = $this->request->host();
        $base = $this->request->base;
        
        $serviceTable = TableRegistry::get('Services');
        $services = $serviceTable->find()->select(array('url','sso'))->where(array('Services.status'=>1))->toArray();
        
        $data = array();
        foreach ($services as $value){
            $data['sso'][] =  $value['url'].$value['sso'].(strpos($value['sso'], '?')?'&':'?').'action=logout&callback=?';//TODO 部署时需注意
        }
        
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }
    
    //检查重复
    public function check() {
        $this->viewClass = 'Json';
        $code = $this->_code;
        if ($this->_data && is_array($this->_data)){
            $type = isset($this->_data['type'])?
            in_array($this->_data['type'], array('loginname','email','mobile'))?$this->_data['type']:'loginname':'loginname';
            
            
            if (isset($this->_data[$type])){
                $accountTable = TableRegistry::get('Accounts');
                
                try {
                    $account_info = $accountTable->find()->select(array('id'))->where(array('Accounts.'.$type=>$this->_data[$type]))->toArray();
                    $data = $account_info?false:true;
                } catch (\Exception $e) {
                    $code = '00098';
                    $msg = $this->_getMsg($code,$type);
                }
                
            }else{
                $code = '10002';
                $msg = $this->_getMsg($code,$type);
            }
            
        }else{
            //post 数据空
            $this->_error[] = Configure::read('MSG.00004');
            $code = '00004';
            $msg = $this->_getMsg($code);
        }
            
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }
    
    
    //获取用户信息
    public function get() {
        $this->viewClass = 'Json';
        $code = $this->_code;
        if ($this->_data && is_array($this->_data)){
            $type = isset($this->_data['type'])?
            in_array($this->_data['type'], array('loginname','email','mobile'))?$this->_data['type']:'loginname':'loginname';
    
            if (isset($this->_data['fields'])){
                $fields = explode(',', $this->_data['fields']);
            }else{
                $fields = array();
            }
    
            if (isset($this->_data[$type])){
                $accountTable = TableRegistry::get('Accounts');
                try {
                    $account_info = $accountTable->find()->select($fields)->where(array('Accounts.'.$type=>$this->_data[$type]))->toArray();
                    
                    if ($account_info){
                        $data['info'] = $account_info;
                    }else{//查无此用户
                        $code = '10003';
                        $msg = $this->_getMsg($code,$type);
                    }
                    
                } catch (\Exception $e) {
                    $code = '00098';
                    $msg = $this->_getMsg($code,$type);
                }
    
            }else{
                $code = '10002';
                $msg = $this->_getMsg($code,$type);
            }
    
        }else{
            //post 数据空
            $this->_error[] = Configure::read('MSG.00004');
            $code = '00004';
            $msg = $this->_getMsg($code);
        }
    
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }
    
    /**
     * 根据pin检查业务系统是否登录
     * 
     * 返回 
     *  info:登录账号信息
     *  source:系统登录来源
     *  time:登录时间
     */
    public function isLogin() {
        $this->viewClass = 'Json';
        $code = $this->_code;
        
        if ($this->_data && is_array($this->_data)){
            if (isset($this->_data['pin'])){
                //解密后的cookie值
                $c = unserialize($this->Des->decrypt($this->_data['pin']));
                $cookieKey = Configure::read('Security.Cookie.key');
                
                //pin错误。
                $code = '10005';
                $msg = $this->_getMsg($code);
                
                if (isset($c[$cookieKey])&&isset($c['account'])&&isset($c[0])){
                    $pinId = $this->Des->decrypt($c[$cookieKey]);
                    list($id,$time,$host) = explode('@', $pinId);
                    
                    //对pin值以及有效期的校验
                    if ($id == $c['account']['id']&&abs(time()-$time)<=Configure::read('Security.alc.expires')){
                        $code = 0;
                        $msg = $this->_getMsg($code);
                        $data['info'] = $c['account'];
                        $data['source'] = $host;
                        $data['time'] = $time;
                    }
                }
                
            }else{
                $code = '10002';
                $msg = $this->_getMsg($code,'pin');
            }
            
        }else{
            //post 数据空
            $this->_error[] = Configure::read('MSG.00004');
            $code = '00004';
            $msg = $this->_getMsg($code);
        }
        
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }
    
    /**
     * 主要提供给业务系统表单登录
     * 返回账号信息，包含密码。以及cookie值，包含login,pinId,pin
     * 
     * 日志todo
     */
    public function login(){
        $this->viewClass = 'Json';
        $code = $this->_code;
        
        if ($this->_data && is_array($this->_data)){
            //处理可选值
            $type = isset($this->_data['type'])?
            in_array($this->_data['type'], array('loginname','email','mobile'))?$this->_data['type']:'loginname':'loginname';
            if ($this->_checkLoginParams($this->_data)){
                if (isset($this->_data[$type])){
                    //$loginname 或 $email或 mobile 以及password ,type参数齐全
                    $accountTable = TableRegistry::get('Accounts');
                    
                    $account_info = $accountTable->find()->select($this->_infoFields)->where(array('Accounts.'.$type=>$this->_data[$type]))->first();
                    
                    if ($account_info){
                        $time = time();
                        $securityCookieKey = Configure::read('Security.Cookie.key');
                        
                        //密码校验
                        $_password_result = false;
                        
                        $md5pwd = null;
                        
                        if (isset($this->_data['md5pwd'])){
                            if ((new SobeyPasswordHasher(array('salt'=>$account_info['salt'])))->checkMd5($this->_data['md5pwd'],$account_info['password'])){
                                $_password_result = true;
                                $md5pwd = $this->_data['md5pwd'];
                            }
                        }elseif (isset($this->_data['password'])){
                            if ((new SobeyPasswordHasher(array('salt'=>$account_info['salt'])))->check($this->_data['password'],$account_info['password'])){
                                $_password_result = true;
                                $md5pwd = md5($this->_data['password']);
                            }
                        }
                        //补全md5pwd
                        if (empty($account_info['md5pwd'])||$md5pwd!=$account_info['md5pwd']){
                            $account_info['md5pwd'] = $md5pwd;
                            $accountTable->save($account_info);
                        }
                        
                        //密码校验
                        if ((new SobeyPasswordHasher(array('salt'=>$account_info['salt'])))->check($this->_data['password'],$account_info['password'])){
                            $data['info'] = $account_info;
                            $data['cookie'] = array(
                                //为其他子业务系统写入cookie
                                'logining'=>1,
                                $securityCookieKey => $this->Des->encrypt($account_info['id'].'@'.$time.'@'.$this->request->domain())
                            );
                            //为自身登录cookie
                            $data['cookie']['pin'] = $this->Des->encrypt(json_encode(array(
                                $time,
                                $securityCookieKey=>$data['cookie'][$securityCookieKey],
                                'account'=>$data['info']
                            )));
                            
                            //成功后为写入登录日志
                            try {
                                //写入登陆日志
                                $_http = new Client();
                                $_token = '';
                                $_http->post($this->_api_vboss.'/loginlog/writeLogin?_token='.$_token,[
                                        'loginname'     =>  $account_info['loginname'],
                                        'product_code'  =>  isset($this->_data['source'])?$this->_data['source']:'api',
                                        'loginip'       =>  isset($this->_data['loginip'])?$this->_data['loginip']:'0.0.0.0'
                                    ]);
                            } catch (\Exception $e) {
                                //throw $e;
                                //TODO 
                            }
                            
                        }else{
                            //密码错误
                            $code = '10004';
                            $msg = $this->_getMsg($code);
                        }
                    }else{
                        $code = '10003';
                        $msg = $this->_getMsg($code);
                    }
                    
                }else{
                    $code = '10002';
                    $msg = $this->_getMsg($code,$type);
                }
            }else{
                $code = '10002';
                $msg = $this->_getMsg($code);
            }
            
        }else{
            //post 数据空
            $this->_error[] = Configure::read('MSG.00004');
            $code = '00004';
            $msg = $this->_getMsg($code);
        }
        
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }
    
    private function _checkLoginParams($params){
        //password
        return isset($params['password'])||isset($params['md5pwd']);
    } 
    
    /**
     * 生成随机字符串
     * @param number $len
     * @return string
     */
    private function _random_str($len= 6){
        $str = "";
        for($i = 0; $i< $len; $i++){
            $str .= chr(mt_rand(64, 122));
        }
        return $str;
    }

    
    /**
     * 注册
     */
    public function regist(){
        $this->viewClass = 'Json';
        $code = $this->_code;
        
        if ($this->_data && is_array($this->_data)){
            
            if($this->_checkRegistParams($this->_data)){
                
                //$loginname 或 $email或 mobile 以及password ,type参数齐全
                $accountTable = TableRegistry::get('Accounts');
                $account = $accountTable->newEntity();
                
                $account->loginname = $this->_data['loginname'];
                $account->username = $this->_data['loginname'];
                $account->email = $this->_data['email'];
                $account->regip = isset($this->_data['regip'])?$this->_data['regip']:'0.0.0.0';
                $account->mobile = $this->_data['mobile'];
                $account->created = $account->modified = date('Y-m-d H:i:s');
                $account->salt = isset($this->_data['salt'])?$this->_data['salt']:$this->_random_str();
                $account->password = (new SobeyPasswordHasher(array('salt'=>$account->salt)))->hash($this->_data['password']);
                $account->md5pwd = md5($this->_data['password']);
                $account->source_system = isset($this->_data['source'])?$this->_data['source']:'other';
                $account->status = 1;
                //校验重复
                $account_exists = $accountTable->find()->where(['OR'=>
                    [
                        'loginname'=>$this->_data['loginname'],
                        'email'=>  $this->_data['email'],
                        'mobile'=>$this->_data['mobile']
                    ]
                ])->toArray();
                if ($account_exists){
                    $code = '10006';
                    $msg = $this->_getMsg($code);
                    
                    foreach (['10007'=>'loginname','10008'=>'email','10009'=>'mobile'] as $key => $value){
                        if (isset($account_exists[0][$value])&&($account_exists[0][$value] == $this->_data[$value] )){
                            $code = $key;
                            $msg = $this->_getMsg($code);
                            break;
                        }
                    }
                    
                }else{
                    try {
                        $info = $accountTable->save($account);
                        if ($info){
                            $data['info'] = $info;
                        }else{
                            $code = '00003';
                            $msg = $this->_getMsg($code);
                        }
                    } catch (\Exception $e) {//
                        $code = '00003';
                        $msg = $this->_getMsg($code);
                    }
                    
                }
                
            }else{
                $code = '10002';
                $msg = $this->_getMsg($code);
            }
            
        }else{
            //post 数据空
            $this->_error[] = Configure::read('MSG.00004');
            $code = '00004';
            $msg = $this->_getMsg($code);
        }
        
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }
    
    
    private function _checkRegistParams($params){
        return isset($params['loginname'])&&$params['email']&&$params['mobile']&&$params['password'];
    }
    
    
    /**
     * 密码生成
     */
    public function password(){
        $this->viewClass = 'Json';
        $code = $this->_code;
        
        if ($this->_data){
            
            $salt = '';
            if (isset($this->_data['salt'])){
                $salt = $this->_data['salt'];
                unset($this->_data['salt']);
            }
            
            if( is_array($this->_data) ){
                foreach ($this->_data as $key => $value){
                    $data[$key] = (new SobeyPasswordHasher(array('salt'=>$salt)))->hash($value);
                }
            
            }else{
                $data = (new SobeyPasswordHasher(array('salt'=>$salt)))->hash($this->_data);
            }
            
        }else{
            //post 数据空
            $this->_error[] = Configure::read('MSG.00004');
            $code = '00004';
            $msg = $this->_getMsg($code);
        }
        
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
        
    }
    
    
    /**
     * 编辑
     */
    public function edit(){
        $this->viewClass = 'Json';
        $code = $this->_code;
        
        if ($this->_data && is_array($this->_data)){
            if($this->_checkEditParams($this->_data)){
                $id = $this->_data['id'];
                //$loginname 或 $email或 mobile 以及password ,type参数齐全
                $accountTable = TableRegistry::get('Accounts');
                try {
                    $account = $accountTable->get($id);
                    if($account){
                        if (isset($this->_data['loginname'])){
                            $account->loginname = $this->_data['loginname'];
                        }
                        if (isset($this->_data['email'])){
                            $account->email = $this->_data['email'];
                        }
                        if (isset($this->_data['mobile'])){
                            $account->mobile = $this->_data['mobile'];
                        }
                        if (isset($this->_data['salt'])){
                            $account->salt = isset($this->_data['salt'])?$this->_data['salt']:'';
                        }
                        if (isset($this->_data['password'])){
                            $account->md5pwd = md5($this->_data['password']);
                            $account->password = (new SobeyPasswordHasher(array('salt'=>$account->salt)))->hash($this->_data['password']);
                        }
                        if (isset($this->_data['sex'])){
                            $account->sex = $this->_data['sex'];
                        }
                        if (isset($this->_data['username'])){
                            $account->username = $this->_data['username'];
                        }
                    
                        if ($accountTable->save($account)){
                            $data['info'] = $account;
                        }else{
                            $code = '00003';
                            $msg = $this->_getMsg($code);
                        }
                    }else{
                        $code = '10002';
                        $msg = $this->_getMsg($code);
                    }
                } catch (\Exception $e) {
                    $code = '10003';
                    $msg = $this->_getMsg($code);
                }
                
            }else{
                $code = '10002';
                $msg = $this->_getMsg($code);
            }
            
        }else{
            //post 数据空
            $this->_error[] = Configure::read('MSG.00004');
            $code = '00004';
            $msg = $this->_getMsg($code);
        }
        
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }
    
    private function _checkEditParams($params){
        return isset($params['id']);
    }
    
    /**
     * des加密解密算法
     * @param string $action
     */
    public function des($action = 'encrypt'){
        $this->viewClass = 'Json';
        $code = $this->_code;
        
        if ($this->_data){
            if( is_array($this->_data) ){
                foreach ($this->_data as $key => $value){
                    if ($action == 'encrypt'){
                        $data[$key] = $this->Des->encrypt($value);
                    }
                    if ($action == 'decrypt'){
                        $data[$key] = $this->Des->decrypt($value);
                    }
                }
                
            }else{
                
                if ($action == 'encrypt'){
                    $data =  $this->Des->encrypt($this->_data);
                }
                if ($action == 'decrypt'){
                    $data =  $this->Des->decrypt($this->_data);
                }
            }
        }else{
            //post 数据空
            $this->_error[] = Configure::read('MSG.50001');
            $code = '50001';
            $msg = $this->_getMsg($code);
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }
    
    /**
     * ras加密解密算法
     * @param string $action
     */
    public function rsa($action = 'encrypt'){
        $this->viewClass = 'Json';
        $code = $this->_code;
        
        if ($this->_data){
            if( is_array($this->_data) ){
                foreach ($this->_data as $key => $value){
                    if ($action == 'encrypt'){
                        $data[$key] = $this->Rsa->encrypt($value);
                    }
                    if ($action == 'decrypt'){
                        $data[$key] = $this->Rsa->decrypt($value);
                    }
                }
                
            }else{
                
                if ($action == 'encrypt'){
                    $data =  $this->Rsa->encrypt($this->_data);
                }
                if ($action == 'decrypt'){
                    $data =  $this->Rsa->decrypt($this->_data);
                }
            }
        }else{
            //post 数据空
            $this->_error[] = Configure::read('MSG.50001');
            $code = '50001';
            $msg = $this->_getMsg($code);
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }
    
    
    /**
     * 获取接口中传递过来的参数。
     * 支持form提交以及body提交
     * @return Ambigous <unknown, string, multitype:>
     */
    private function _getData(){
        $data = $this->request->data?$this->request->data:file_get_contents('php://input', 'r');
        
        //处理非x-form的格式
        if (is_string($data)){
            $data_tmp = json_decode($data,true);
            if (json_last_error() == JSON_ERROR_NONE){
                $data = $data_tmp;
            }
        }
        
        Log::debug("Data Posted :".json_encode($data),['action'=>$this->request->params['action'],'host'=>$this->request->host()]);
        
        return $data;
    }
    /**
     * 获取操作信息
     * @param string $code
     * @param string $param
     * @return string
     */
    private function _getMsg($code = '00000',$param = ''){
        //extract
        return sprintf(Configure::read('MSG.'.$code),$param);
    }
    
    /**
     * 注册
     */
    public function test(){
        $this->viewClass = 'Json';
        $code = $this->_code;
    
        if ($this->_data && is_array($this->_data)){
    
            if($this->_checkRegistParams($this->_data)){
    
            }else{
                $code = '10002';
                $msg = $this->_getMsg($code);
            }
    
        }else{
            //post 数据空
            $this->_error[] = Configure::read('MSG.00004');
            $code = '00004';
            $msg = $this->_getMsg($code);
        }
    
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }
    
}