<?php
/** 
* class
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年5月19日下午9:51:35
* @source AccountsController.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

namespace App\Controller;

use Cake\Validation\Validation;
use Cake\Core\Configure;
use App\Auth\SobeyPasswordHasher;
use Cake\Network\Http\Client;
//use Cake\Utility\Text;

class AccountsController extends SobeyController
{
    
    public $sessionKey = 'Auth.User';
    
    public function initialize(){
        parent::initialize();
        
        if($this->request->is('post')&&($this->request->params['action'] == 'login')){
            //处理email，phone登录
            $this->request->data['email'] = $this->request->data['mobile'] = $this->request->data['loginname'] = $this->request->data['username'];
            if (Validation::email($this->request->data['email'])){
                $this->Auth->config('authenticate',[
                    'SobeyForm'  =>  [
                        'fields'  => [ 'username' => 'email' ]
                    ]
                ]);
            }elseif (preg_match("/^(\+?86-?)?(18|15|13|17)[0-9]{9}$/", $this->request->data['mobile'])){
                $this->Auth->config('authenticate',[
                    'SobeyForm'  =>  [
                        'fields'  => [ 'username' => 'mobile' ]
                    ]
                ]);
            }else{
                $this->Auth->config('authenticate',[
                    'SobeyForm'  =>  [
                        'fields'  => [ 'username' => 'loginname' ]
                    ]
                ]);
            }
            
        }
        
        $this->Auth->allow(['captcha','regist','sso','login']);
    }
    
    /**
     * 账号注册
     */
    public function regist()
    {
        $this->layout = 'regist';
    }
    
    
    /**
     * 账号登陆
     */
    public function login()
    {
        //处理单点登录退出
        if(isset($this->request->query['action'])&&($this->request->query['action']=='logout')){
            $http = new Client();
            $obj_response_logout = $http->get('http://127.0.0.1/api/exits?_token='.$this->_token);
            $data_response_logout =  json_decode($obj_response_logout->body,true);
            $this->set('sso_logout',$data_response_logout['data']['sso']);
        }
        
        //已经登录过，修复，重复跳转到/login
        if ($this->Auth->user()){
            $this->redirect(array('controller'=>'accounts','action'=>'security'));
        }
        
        $this->layout = 'login';
        
        $time = time();
        $alc = $this->Des->encrypt($time);
        //TODO 本应为访问列表，权限值。
        $alcKey = Configure::read('Security.alc.key');
        
        $this->Cookie->write($alcKey,$alc);
        $this->Cookie->write('_',$time);
        
        //处理登陆操作
        if ($this->request->is('post')){
            //校验cookie ALC, 30秒失效
            if (time() - $this->Cookie->read('_')  <= Configure::read('Security.alc.expires')){
                //校验验证码
                if($this->request->session()->read('captcha') == $this->request->data['code']){
                    $account = $this->Auth->identify();
                    //校验用户名/手机号/email 密码
                    if ($account){
                        $this->Auth->setUser($account);
                        //debug($this->Auth->redirectUrl());die;
                        //需要单点登录
                        $this->Cookie->write('logining',1);
                        
                        return $this->redirect($this->Auth->redirectUrl());
                    }else{
                        $this->Flash->error('账号或密码错误，请重试！');
                    }
                }else{
                    $this->Flash->error('验证码错误，请重试！');
                }
            }else{
                $this->Flash->error('请重新登录！');
            }
        }
        
    }
    
    
    public function logout(){
        $this->Cookie->delete('pin');
        $this->request->session()->destroy();
        $this->redirect('/accounts/login?action=logout');
    }
    
    /**
     * 索贝账号首页
     */
    public function security()
    {
    }
    
    /**
     * 响应sso登录
     * ?action=login&callback=jQuery213036579320300370455_1432691249103&_=1432691249105&c=CQ5bvBmnZpXoJutBbWU7wPO%2FqyKFhMDDhDQsvyogJZM%2BowuDZLLbcVL7x6y0ErMnoewYDXX82JIyOnkDZwCK7%2ByflSKY7xpfxScwTSmQxZAISwad4evVDs40UR2rWoETf9sR0heAfpTbxZyv0u59Y1V5YOL3LMjv55Q9KRFgbYXouagkG4%2BFUz6J3mSLr2VrsI%2FWuibmB6mqF9s%2Fc7sZQ3iWsFZF8H%2FUvADIbcoTuRpJSDHkoCUowy3POoKBiIoXx0IgTtNiQJSX2mT34Ofe6pXLLE6efPHmhxFLMshxHbctTEqpU8KPhoxucawb%2BZJw8uZf0ZXAIygXs1zJKgGCsotLS2z7Ox13ZOi5B7fF%2BewmPPJUasHwQPhldFOn4DBpyMqgQLsQ9ZuQMEFfZKY0TTu5YNox6JIP9Ex0NyXRBus%3D
     */
    public function sso()
    {
        $this->viewClass = 'Json';
        $this->set('_jsonp',true);
        $this->set('_serialize',[]);
        //获取
        $queryArr = $this->request->query;
        $action = isset($queryArr['action'])?$queryArr['action']:'login';
        $c = isset($queryArr['c'])?$queryArr['c']:'';
        $http = new Client();
        switch ($action){
            case 'logout':
                //$this->Cookie->delete('pin');
                $this->Auth->logout();
                break;
            case 'login':
                //$this->Cookie->write('pin');
                $obj_response = $http->post('http://127.0.0.1/api/isLogin?_token=',[
                    'pin'=>$c
                ]);
                
                $data_response =  json_decode($obj_response->body,true);
                //接口调用成功本地登录
                if (isset($data_response['code'])&&$data_response['code'] == 0){
                    $this->Auth->setUser($data_response['data']['info']);
                }
                
                break;
            default:
                ;
        }
        
    }
    
    
    
    /**
     * 索贝账号 账号安全 修改密码
     */
    public function password($id = 0){
        //修改操作
        if ($this->request->is('ajax')&&$this->request->is('post')){
            $this->autoRender = false;
            
            if ($id == 0){
                $id = $this->request->session()->read('Auth.User.id');
            }

            $account = $this->Accounts->get($id);
            //password0  老密码 password新密码 password1 确认新密码
            //校验
            $data = $this->request->data;
            if ($data['password'] != $data['password1']){
                echo json_encode(array('code'=>2,'msg'=>'两次密码不一致'));exit;
            }
            
            if (!(new SobeyPasswordHasher(array('salt'=>$account->salt)))->check($data['password0'],$account->password)){
                echo json_encode(array('code'=>3,'msg'=>'初始密码不正确'));exit;
            }
            
            $account = $this->Accounts->patchEntity($account,$data);
            $account->id = $id;
            
            $account->password = (new SobeyPasswordHasher(array('salt'=>$account->salt)))->hash($data['password']);
            
            if ($this->Accounts->save($account)){
                echo json_encode(array('code'=>0,'msg'=>'操作成功'));exit;
            }else{
                echo json_encode(array('code'=>1,'msg'=>'操作失败'));exit;
            }
        }
    }
    
    /**
     * 索贝账号 账号安全 修改安全邮箱
     */
    public function email($id = 0){
        //修改操作
        if ($this->request->is('ajax')&&$this->request->is('post')){
            $this->autoRender = false;
    
            if ($id == 0){
                $id = $this->request->session()->read('Auth.User.id');
            }
    
            $data = $this->request->data;
            
            if (!Validation::email($data['email'])){
                echo json_encode(array('code'=>2,'msg'=>'邮箱地址不合法'));exit;
            }
            
            $account = $this->Accounts->get($id);
            //校验
    
            $account = $this->Accounts->patchEntity($account,$data);
            $account->id = $id;
            if ($this->Accounts->save($account)){
                //登录处理
                $this->Auth->logout();
                $this->Auth->setUser($this->Accounts->get($id)->toArray());
                
                echo json_encode(array('code'=>0,'msg'=>'操作成功'));exit;
            }else{
                echo json_encode(array('code'=>1,'msg'=>'操作失败'));exit;
            }
        }
    }
    
    
    /**
     * 索贝账号 账号安全 修改安全手机
     */
    public function mobile($id = 0){
        //修改操作
        if ($this->request->is('ajax')&&$this->request->is('post')){
            $this->autoRender = false;
    
            if ($id == 0){
                $id = $this->request->session()->read('Auth.User.id');
            }
    
            $data = $this->request->data;
            
            if (!preg_match("/^(\+?86-?)?(18|15|13)[0-9]{9}$/", $data['mobile'])){
                echo json_encode(array('code'=>2,'msg'=>'手机号码不合法'));exit;
            }
            
            $account = $this->Accounts->get($id);
            //校验
    
            $account = $this->Accounts->patchEntity($account,$data);
            $account->id = $id;
            if ($this->Accounts->save($account)){
                
                //登录处理
                $this->Auth->logout();
                $this->Auth->setUser($this->Accounts->get($id)->toArray());
                
                echo json_encode(array('code'=>0,'msg'=>'操作成功'));exit;
            }else{
                echo json_encode(array('code'=>1,'msg'=>'操作失败'));exit;
            }
        }
    }
    
    /**
     * 索贝账号修改资料
     */
    public function profile($id)
    {
        $id = intval($id) == intval($this->request->session()->read('Auth.User.id'))?intval($id):intval($this->request->session()->read('Auth.User.id'));
        
        //修改操作
        if ($this->request->is('ajax')&&$this->request->is('post')){
            $this->autoRender = false;
            //$account = $info = $this->Accounts->find()->contain(array('AccountInfos'))->where(array('Accounts.id'=>$id))->first();
            //$account_info = $this->Accounts->AccountInfos->find()->where()->first();
            //$accountInfoTable = TableRegistry::get('AccountInfos');
            $account_info = $this->Accounts->find()->where(array('Accounts.id'=>$id))->first();
            
            $account_info = $this->Accounts->patchEntity($account_info,$this->request->data);
            $account_info->id = $id;
            if ($this->Accounts->save($account_info)){
                echo json_encode(array('code'=>0,'msg'=>'操作成功'));exit;
            }else{
                echo json_encode(array('code'=>1,'msg'=>'操作失败'));exit;
            }
            
        }
        $info = $this->Accounts->find()->where(array('Accounts.id'=>$id))->first()->toArray();
        $this->set(compact('info'));
    }
    
    
    
    
    public function captcha(){
        
        $this->layout = false;
        $num = 4;
        $width = 60;
        $height = 20;
        
        $code = $this->_getCaptchaCode($num);
        $this->request->session()->write('captcha',$code);
        
        //创建图片，定义颜色值
        header('Content-Type: image/png');
        $im = imagecreate($width, $height);
        $black = imagecolorallocate($im, 0, 0, 0);
        $gray = imagecolorallocate($im, 206, 206, 206);
        $blue = imagecolorallocate($im,60,150,255);
        $bgcolor = imagecolorallocate($im, 255, 255, 255);
        
        //填充背景
        imagefill($im, 0, 0, $gray);
        
        //画边框
        imagerectangle($im, 0, 0, $width-1, $height-1, $gray);
        
        //随机绘制两条虚线，起干扰作用
        $style = array ($black,$black,$black,$black,$black,
            $gray,$gray,$gray,$gray,$gray
        );
        imagesetstyle($im, $style);
        $y1 = rand(0, $height);
        $y2 = rand(0, $height);
        imageline($im, 0, $y1, $width, $y2, IMG_COLOR_STYLED);
        
        //在画布上随机生成大量黑点，起干扰作用;
        for ($i = 0; $i < 30; $i++) {
            imagesetpixel($im, rand(0, $width), rand(0, $height), $black);
        }
        
        //将数字随机显示在画布上,字符的水平间距和位置都按一定波动范围随机生成
        $strx = rand(3, 8);
        for ($i = 0; $i < $num; $i++) {
            $strpos = rand(1, 6);
            imagestring($im, 5, $strx, $strpos, $strpos%2 == 0?strtoupper(substr($code, $i, 1)):substr($code, $i, 1), $blue);
            $strx += rand(8, 12);
        }
        imagepng($im);//输出图片
        imagedestroy($im);//释放图片所占内存
        
    }
    
    private function _getCaptchaCode($length = 4){
        $str = "23456789abcdefghjkmnqrstuvwxyz";
        $code = "";
        $str_len = strlen($str);
        for($i=0; $i<$length; $i++) {
            $code .= $str{mt_rand(0, $str_len-1)};
        }
        
        return $code;
    }
    
    
}