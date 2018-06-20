<?php $this->start('title'); ?>
索贝云账号 - 索贝服务
<?php $this->end(); ?>

<div class="ui-grid-row">
    <div class="ui-tab ui-grid-25" >
        <ul class="ui-tab-items">
            <li class="ui-tab-item ">
                <a href="<?= $this->Url->build(array('controller'=>'Accounts','action'=>'security')); ?>">账号安全</a>
            </li>
            <li class="ui-tab-item">
                <a href="<?= $this->Url->build(array('controller'=>'Accounts','action'=>'profile',str_pad($this->Session->read('Auth.User.id'),8,'0',STR_PAD_LEFT))); ?>">个人信息</a>
            </li>
            <li class="ui-tab-item ui-tab-item-current">
                <a href="<?= $this->Url->build(array('controller'=>'Services','action'=>'index')); ?>">索贝服务</a>
            </li>
        </ul>
    </div>
</div>

<div class="ui-grid-row">
    <div class="ui-grid-25 n-frame">
        <div class="content-info">
            <div class="title-item">
                <h1>您的账号可以登录以下索贝提供的服务</h1>
            </div>
            <div class="service-list ui-grid-row">
                <div class="ui-grid-24">
                <?php 
                    foreach ($services as $key => $service){
                ?>
                <div class="ui-grid-11">
                    <dl>
                        <dt><!-- icon --><a target="_blank" class="icon-service" href="<?= $service->url;?>"><img src=<?= $service->icon?$service->icon:''; ?>></a></dt>
                        <dd>
                            <h4><a class="a-service-title" target="_blank" title="<?= $service->name; ?>" href="<?= $service->url;?>"><?= $service->name;?></a></h4>
                            <p><?= $service->description;?></p>
                        </dd>
                    </dl>
                </div>
                <?php       
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
</div>




<?php  $this->start('script_sobey'); ?>
<script type="text/javascript">
</script>
<?php $this->end(); ?>