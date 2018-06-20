<?php $this->start('title'); ?>
索贝云账号 - 登陆
<?php $this->end(); ?>


<!-- content -->
<style>
.error{
	background: #FDEEE9;
    border: 1px solid #fadcd3;
	padding:3px 12px;
}
.warning{
	background: #fefea4;
    border: 1px solid #e6e098;
	padding:3px 12px;
}
.msg{
	border: 1px solid #b2e2ea;
    background: #e5f2f8;
	padding:3px 12px;
}
</style>

<div class="register-box">
    <div class="log-slogan">
        索贝账号
        <span  class="to-regist fn-right"><a href="<?= $this->Url->build(array('controller'=>'accounts','action'=>'regist')); ?>">立即注册</a></span>
    </div>
    <div class="log-form" id="js-form-mobile"> 
        <form id="log-form" action="" method="post">
            <div id="info-box" class="user-agreement">
                <?php 
                if($this->Session->check('Flash.flash')){
                    echo  $this->Flash->render();
                }else{
                    echo '<div class="warning">公共场所不建议自动登录，以防账号丢失</div>';
                }
                ?>
            </div>
            <div class="cell">
                <input type="text" name="username" id="username" class="text" placeholder="手机号/邮箱/用户名"  />
            </div>
            <div class="cell">
                <input type="password" name="password" id="password" placeholder="密码" class="text" />
            </div>
            <!-- !短信验证码 -->
            <div class="cell vcode">
                <input type="text" name="code" id="code" class="text" placeholder="验证码"  maxlength="4" />
                <a href="javascript:;" id="js-get-mobile-vcode" class=""><img alt="验证码" src="<?= $this->Url->build(array('controller'=>'Accounts','action'=>'captcha')); ?>" title="" /></a>
                <span><a id="js-change-mobile-vcode" href="javascript:;">换一个</a></span>
                <!--  <a href="javascript:;" id="js-get-mobile-vcode" class="button btn-disabled"> 获取验证码</a> --> 
            </div>
            <div class="user-agreement ">
              <input type="checkbox" id="js-remember-password" checked="false" />
              <label for="js-remember-password">记住密码</label>
              <label  for="js-forget-password"><a href="#">忘记密码?</a></label>
            </div>
            
            
            <div class="bottom cell"> 
                <a id="js-btn" href="javascript:;" class="button btn-green"> 立即登陆</a>
            </div>
        </form>
     </div>
</div>

<?php $this->start('script_last'); ?>

<?php 
    if (isset($sso_logout)&&(!empty($sso_logout))){
        foreach ($sso_logout as $value){
?>
<script type="text/javascript">
$.ajax({
	url:"<?= $value ?>",
	dataType:'jsonp',
	crossDomain:true
});
</script>
<?php 
        }
    }
?>
<script type="text/javascript">
$(function(){
	$('#js-btn').click(function(){
		$('#info-box').html('<div class="msg">登录中,请稍后</div>');
		$('#js-btn').html('正在登录...');
		//TODO 前台校验
		$('#log-form').submit();
	});
	$('#js-get-mobile-vcode,#js-change-mobile-vcode').click(function(){
	    $('#js-get-mobile-vcode').html('<img alt="验证码" src="<?= $this->Url->build(array('controller'=>'Accounts','action'=>'captcha')); ?>?t='+Date.parse(new Date())+'" title="" />');
	});
});
</script>
<?php $this->end(); ?>
