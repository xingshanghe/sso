<?php $this->start('title'); ?>
索贝云账号 - 个人信息
<?php $this->end(); ?>

<div class="ui-grid-row">
    <div class="ui-tab ui-grid-25" >
        <ul class="ui-tab-items">
            <li class="ui-tab-item ">
                <a href="<?= $this->Url->build(array('controller'=>'Accounts','action'=>'security')); ?>">账号安全</a>
            </li>
            <li class="ui-tab-item ui-tab-item-current">
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
            <div class="ui-grid-8">
                <div class="naInfoImgBox">
                    <div class="account-img-area">
                        <div class="account-img-bg-area"></div>
                    </div>
                    <div class="account-img-chg-link"><a href="javascript:void(0);" id="editAvatarLink" title="修改头像" >修改头像</a></div>
                </div>
            </div>
            <div class="ui-grid-16">
                <div class="framedatabox">
                    <div class="fdata">
                        <a class="color4a9 fn-right" href="javascript:void(0);" title="编辑" id="editInfoLink">编辑</a>
                        <h3>基础资料</h3>    
                    </div>
                    <div class="fdata">
                        <p><span>姓名：</span><span class="value"><?= $info['username'] ?></span></p>     
                    </div>
                    <div class="fdata">
                        <p><span>生日：</span><span class="value"><?= $info['birthday'] ?></span></p>     
                    </div>
                    <div class="fdata">
                        <p><span>性别：</span><span class="value"><?= $info['sex']=='male'?"男":"女" ?></span></p>     
                    </div>
                    <div class="fdata">
                        <p><span>公司名称：</span><span class="value"><?= $info['organization'] ?></span></p>     
                    </div>
                </div>
                <div class="framedatabox">
    			    <div class="fdata">
    				    <h3>高级设置</h3>    
    				</div>
    				<div class="fdata click-row">
    				    <a class="color4a9 fn-right" target="_blank" href="#" title="管理">管理</a>              
    				    <p><span>银行卡</span><span class="arrow_r"></span></p>     
    				</div>            
    				<div class="fdata click-row">
    				    <a class="color4a9 fn-right" target="_blank" href="" title="管理" id="switchRegion">修改</a>
    				    <p><span>帐号地区：  </span><span><em id="region" _code="CN">中国</em><i class="arrow_r"></i></span></p>     
    				</div>
    			  </div>
            </div>
    </div>
</div>

<div id="editInfoDialog" style="display: none;">
    <div class="swapbox">
    <!-- start -->
        <form class="ui-form" name="" method="post" action="" id="editInfoDialogForm">
            <fieldset>
                <div class="ui-form-item">
                    <label for="" class="ui-label">姓名:</label>
                    <input class="ui-input" name="username" type="text" value="<?= $info['username'] ?>"> 
                </div>
        
                <div class="ui-form-item">
                    <label for="" class="ui-label">生日:</label>
                    <input class="ui-input" name="birthday" type="text" value="<?= $info['birthday'] ?>">
                </div>
                <div class="ui-form-item">
                    <label for="" class="ui-label">性别:</label>
                    <select id="province" name="sex" class="ui-input fn-left" >
                        <option value="male" <?= $info['sex']=='male'?'selected="selected"':''; ?>>男</option>
                        <option value="female" <?= $info['sex']=='female'?'selected="selected"':''; ?>>女</option>
                    </select>
                </div>
                <div class="ui-form-item">
                    <label for="" class="ui-label"> 公司名称:</label>
                    <input class="ui-input" type="text" name="organization" value="<?= $info['organization'] ?>">
                </div>
                <!--  
                <div class="ui-form-item">
                    <input id="infoSubmiter" type="submit" class="ui-button ui-button-lblue" value="确定">
                </div>
                -->
            </fieldset>
        </form>
        <!-- end -->
    </div>
</div>

<div id="editAvatarDialog" style="display: none;">
    <div class="swapbox">
    <!-- start -->
        <form class="ui-form" name="" method="post" action="" id="editAvatarDialogForm">
            <fieldset>
                <div class="ui-form-item">
                    <label for="" class="ui-label">请上传图片</label>
                    <input type="button" value="上传头像" class="btn_tip btn_commom " onclick="return false;">
                    <input class="uplodefile " name="userfile" autocomplete="off" disableautocomplete="" type="file">
                </div>
            </fieldset>
        </form>
        <!-- end -->
    </div>
</div>



<?php  $this->start('script_last'); ?>
<script type="text/javascript">
$('#editInfoLink').click(function(){
	dialog({
		title:'基本资料编辑',
		content:$('#editInfoDialog'),
		button:[{
			   value:'确定',
			   callback:function(){
				   //TODO 校验
				   $.ajax({
					    url:'<?= $this->Url->build(array('controller'=>'Accounts','action'=>'profile',$info['id']));?>',
					    type:'POST',
					    dataType:'json',
					    data:$('#editInfoDialogForm').serialize(),
					    success:function(data){
					        if(data.code == 0){
					        	  dialog({
						    		content:'操作成功！',
							    	}).show();
					        	  location.reload(); 
						    }else{
						    	var d = dialog({
						    		content:'操作失败！',
							    	}).show();
						    	setTimeout(function () {
						    	    d.close().remove();
						    	}, 2000);
							}
					        
						},
						error:function(data){
						    console.log(data);
						}
					});
				    return false;
			   },
			   autofocus:false,
			   }],
	}).showModal();
});

$('#editAvatarLink').click(function(){
	dialog({
		title:'修改头像',
		content:$('#editAvatarDialog'),
		button:[{
			   value:'确定',
			   callback:function(){
				   //TODO 校验
				   $.ajax({
					    url:'<?= $this->Url->build(array('controller'=>'Accounts','action'=>'profile',$info['id']));?>',
					    type:'POST',
					    dataType:'json',
					    data:$('#editAvatarDialogForm').serialize(),
					    success:function(data){
					        if(data.code == 0){
					        	  dialog({
						    		content:'操作成功！',
							    	}).show();
					        	  location.reload(); 
						    }else{
						    	var d = dialog({
						    		content:'操作失败！',
							    	}).show();
						    	return false;
							}
					        
						},
						error:function(data){
						    console.log(data);
						}
					});
				    
			   },
			   autofocus:false,
			   }],
	}).showModal();
});
</script>
<?php $this->end(); ?>