<?php $this->start('title'); ?>
索贝云账号 - 账号安全
<?php $this->end(); ?>
<style>
.error{
	color: red;
}
</style>
<div class="ui-grid-row">
    <div class="ui-tab ui-grid-25" >
        <ul class="ui-tab-items">
            <li class="ui-tab-item ui-tab-item-current">
                <a href="<?= $this->Url->build(array('controller'=>'Accounts','action'=>'security')); ?>">账号安全</a>
            </li>
            <li class="ui-tab-item">
                <a href="<?= $this->Url->build(array('controller'=>'Accounts','action'=>'profile',str_pad($this->Session->read('Auth.User.id'),8,'0',STR_PAD_LEFT))); ?>">个人信息</a>
            </li>
            <li class="ui-tab-item">
                <a href="<?= $this->Url->build(array('controller'=>'Services','action'=>'index')); ?>">索贝服务</a>
            </li>
        </ul>
    </div>
</div>
<div class="ui-grid-row">
    <div class="ui-grid-25 n-frame">
        <div class="content-info">
            <div class="title-item">
                <h1 class="dis-inb">安全等级</h1>
                <p style="margin-left:20px;" class=" dis-inb wap_colb2">
                    <span class="ui-form-required">50</span>分
                </p>
                <p style="margin-left:20px;" class=" dis-inb">
				    <span class="score_outer_1">存在<em class="ui-form-required">2</em>项风险</span>
		        </p>
            </div>
            <div class="service-list ui-grid-row">
                <div class="ui-grid-25">
                    
                    <div class="ui-grid-23 security-line">
                        <dl>
                            <dt><!-- icon --><?= $this->Html->image('cake.icon.png'); ?></dt>
                            <dd>
                                <h4><span class="security-title" >账号密码</span></h4>
                                <p>用于保护账号信息和登录安全</p>
                            </dd>
                            <dd class="security-button-box">
                                <a href="javascript:void(0);" id="pwd_editer" class="n-btn">修改</a>
                            </dd>
                        </dl>
                    </div>
                    
                    <div class="ui-grid-23 security-line">
                        <dl>
                            <dt><!-- icon --><?= $this->Html->image('cake.icon.png'); ?></dt>
                            <dd>
                                <h4><span class="security-title" >安全邮箱</span><span class="security-title-content"><?= $this->Session->read('Auth.User.email'); ?></span></h4>
                                <p>安全邮箱可以用于登录小米帐号，重置密码或其他安全验证</p>
                            </dd>
                            <dd class="security-button-box">
                                <a href="javascript:void(0);" id="email_editer" class="n-btn">修改</a>
                            </dd>
                        </dl>
                    </div>
                    
                    <div class="ui-grid-23 security-line" >
                        <dl>
                            <dt><!-- icon --><?= $this->Html->image('cake.icon.png'); ?></dt>
                            <dd>
                                <h4><span class="security-title" >安全手机</span><span class="security-title-content"><?= $this->Session->read('Auth.User.mobile'); ?></span></h4>
                                <p>安全手机将可用于登录小米帐号和重置密码，建议立即设置</p>
                            </dd>
                            <dd class="security-button-box">
                                <a href="javascript:void(0);" id="mobile_editer" class="n-btn">修改</a>
                            </dd>
                        </dl>
                    </div>
                    
                    <div class="ui-grid-23 security-line" style="border-bottom: none;">
                        <dl>
                            <dt><!-- icon --><?= $this->Html->image('cake.icon.png'); ?></dt>
                            <dd>
                                <h4><span class="security-title" >密保问题</span></h4>
                                <p class="error">密保问题用于安全验证，建议立即设置</p>
                            </dd>
                            <dd class="security-button-box">
                                <a href="javascript:void(0);" id="question_editer" class="n-btn">修改</a>
                            </dd>
                        </dl>
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</div>


<div id="editPwdDialog" style="display: none;">
    <div class="swapbox">
    <!-- start -->
        <form class="ui-form" name="" method="post" action="" id="editPwdDialogForm">
            <fieldset>
                <div class="ui-form-item">
                    <label for="" class="ui-label">旧密码:</label>
                    <input class="ui-input" name="password0" type="password" "> 
                </div>
                <div class="ui-form-item">
                    <label for="" class="ui-label">新密码:</label>
                    <input class="ui-input" name="password" type="password" "> 
                </div>
                <div class="ui-form-item">
                    <label for="" class="ui-label">确认密码:</label>
                    <input class="ui-input" name="password1" type="password" "> 
                </div>
            </fieldset>
        </form>
        <!-- end -->
    </div>
</div>

<div id="editEmailDialog" style="display: none;">
    <div class="swapbox">
    <!-- start -->
        <form class="ui-form" name="" method="post" action="" id="editEmailDialogForm">
            <fieldset>
                <div class="ui-form-item">
                    <label for="" class="ui-label">邮箱:</label>
                    <input class="ui-input" name="email" type="email" value="<?= $this->Session->read('Auth.User.email'); ?>"> 
                </div>
            </fieldset>
        </form>
        <!-- end -->
    </div>
</div>

<div id="editMobileDialog" style="display: none;">
    <div class="swapbox">
    <!-- start -->
        <form class="ui-form" name="" method="post" action="" id="editMobileDialogForm">
            <fieldset>
                <div class="ui-form-item">
                    <label for="" class="ui-label">邮箱:</label>
                    <input class="ui-input" name="mobile" type="text" value="<?= $this->Session->read('Auth.User.mobile'); ?>"> 
                </div>
            </fieldset>
        </form>
        <!-- end -->
    </div>
</div>

<?php  $this->start('script_last'); ?>
<script type="text/javascript">

$('#pwd_editer').click(function(){
	dialog({
		title:'修改密码',
		content:$('#editPwdDialog'),
		button:[{
			   value:'确定',
			   callback:function(){
				   //TODO 校验
				   $.ajax({
					    url:'<?= $this->Url->build(array('controller'=>'Accounts','action'=>'password',$this->Session->read('Auth.User.id')));?>',
					    type:'POST',
					    dataType:'json',
					    data:$('#editPwdDialogForm').serialize(),
					    success:function(data){
					        if(data.code == 0){
					        	  dialog({
						    		content:'操作成功！',
							    	}).show();
					        	  location.reload(); 
						    }else{
						    	var d = dialog({
						    		content:data.msg,
							    	}).show();
						    	setTimeout(function () {
						    	    d.close().remove();
						    	}, 2000);
							}
						},
						error:function(data){
							var d = dialog({
					    		content:'服务异常,请联系管理员',
						    	}).show();
					    	setTimeout(function () {
					    	    d.close().remove();
					    	}, 2000);
						}
					});
				   return false;
			   },
			   autofocus:false,
			   }],
	}).showModal();
});

$('#email_editer').click(function(){
	dialog({
		title:'修改安全邮箱',
		content:$('#editEmailDialog'),
		button:[{
			   value:'确定',
			   callback:function(){
				   //TODO 校验
				   $.ajax({
					    url:'<?= $this->Url->build(array('controller'=>'Accounts','action'=>'email',$this->Session->read('Auth.User.id')));?>',
					    type:'POST',
					    dataType:'json',
					    data:$('#editEmailDialogForm').serialize(),
					    success:function(data){
					        if(data.code == 0){
					        	  dialog({
						    		content:'操作成功！',
							    	}).show();
					        	  location.reload(); 
						    }else{
						    	var d = dialog({
						    		content:data.msg,
							    	}).show();
						    	setTimeout(function () {
						    	    d.close().remove();
						    	}, 2000);
							}
						},
						error:function(data){
							var d = dialog({
					    		content:'服务异常,请联系管理员',
						    	}).show();
					    	setTimeout(function () {
					    	    d.close().remove();
					    	}, 2000);
						}
					});
				   return false;
			   },
			   autofocus:false,
			   }],
	}).showModal();
});


$('#mobile_editer').click(function(){
	dialog({
		title:'修改安全手机',
		content:$('#editMobileDialog'),
		button:[{
			   value:'确定',
			   callback:function(){
				   //TODO 校验
				   $.ajax({
					    url:'<?= $this->Url->build(array('controller'=>'Accounts','action'=>'mobile',$this->Session->read('Auth.User.id')));?>',
					    type:'POST',
					    dataType:'json',
					    data:$('#editMobileDialogForm').serialize(),
					    success:function(data){
					        if(data.code == 0){
					        	  dialog({
						    		content:'操作成功！',
							    	}).show();
					        	  location.reload(); 
						    }else{
						    	var d = dialog({
						    		content:data.msg,
							    	}).show();
						    	setTimeout(function () {
						    	    d.close().remove();
						    	}, 2000);
							}
						},
						error:function(data){
							var d = dialog({
					    		content:'服务异常,请联系管理员',
						    	}).show();
					    	setTimeout(function () {
					    	    d.close().remove();
					    	}, 2000);
						}
					});
				   return false;
			   },
			   autofocus:false,
			   }],
	}).showModal();
});


</script>
<?php $this->end(); ?>