<?php
/** 
* 响应单点登录请求，请求地址如下：
* 
* 为jsonp跨域请求
* 
* 参数列表:
* @param action    请求方法login:登录logout:登出
* @param _          发起请求的时间戳
* @param callback   回调函数名称
* @param c          cookie以及session信息的加密字符串
* 
* 
* http://www.sobeyyun.com/sso.php?action=login&callback=jQuery213036579320300370455_1432691249103&_=1432691249105&c=CQ5bvBmnZpXoJutBbWU7wPO%2FqyKFhMDDhDQsvyogJZM%2BowuDZLLbcVL7x6y0ErMnoewYDXX82JIyOnkDZwCK7%2ByflSKY7xpfxScwTSmQxZAISwad4evVDs40UR2rWoETf9sR0heAfpTbxZyv0u59Y1V5YOL3LMjv55Q9KRFgbYXouagkG4%2BFUz6J3mSLr2VrsI%2FWuibmB6mqF9s%2Fc7sZQ3iWsFZF8H%2FUvADIbcoTuRpJSDHkoCUowy3POoKBiIoXx0IgTtNiQJSX2mT34Ofe6pXLLE6efPHmhxFLMshxHbctTEqpU8KPhoxucawb%2BZJw8uZf0ZXAIygXs1zJKgGCsotLS2z7Ox13ZOi5B7fF%2BewmPPJUasHwQPhldFOn4DBpyMqgQLsQ9ZuQMEFfZKY0TTu5YNox6JIP9Ex0NyXRBus%3D
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年5月26日下午11:40:59
* @source sso.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

define('SSO_SERVER_HOST', 'http://passport.vboss.sobey.com');


//变量获取
$action = isset($_GET['action'])?$_GET['action']:'login';
$timestamp = isset($_GET['_'])?$_GET['_']:false;
$callback = isset($_GET['callback'])?$_GET['callback']:false;
$c_coded = isset($_GET['c'])?$_GET['c']:false;

//接口调用
if ($action == 'login'){//登入
    //为通过接口验证登录设置cookie pin，
    setcookie('pin',false);
    setcookie('pin',$c_coded);
    
    //以下代码应根据各个子系统处理
    //////////索贝云服务账号网站 登录开始////////
    
    //////////索贝云服务账号网站 登录结束////////
    
    
}else{//登出
    setcookie('pin',false);
    setcookie('pinId',false);
    //session_destroy();
}
