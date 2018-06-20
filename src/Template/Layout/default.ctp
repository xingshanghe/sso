<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>
    <?= $this->Html->css(['one.css','style.css']) ?>
</head>
<body>
<div class="swap">
<div class="ui-grid-row">
    <div class="header">
        <span class="home"><a class="a-home" href="<?= $this->Url->build(array('controller'=>'Accounts','action'=>'security'));?>">索贝云账号</a></span>
        <a class="a-logout fn-right" href="<?= $this->Url->build(array('controller'=>'Accounts','action'=>'logout')); ?>">退出</a>
    </div>
    <!-- 头像 名字 -->
    <div class="account-area-box">
        <div class="account-area fn-clear">
            <div class="account-info">
                <p class="na-name"><?=  $this->Session->read('Auth.User.loginname'); ?></p>
                <p class="na-num"><?= str_pad($this->Session->read('Auth.User.id'), 8,'0',STR_PAD_LEFT); ?></p>
            </div>
            <div class="account-img-area fn-left">
                <div class="account-img-bg-area">
                    <?=  $this->Session->read('Auth.User.avatar'); ?>
                </div>
            </div>
            
        </div>
    </div>
    
</div>
<?= $this->fetch('content') ?>
<div class="footer">
版权所有 2015 索贝数码科技股份有限公司
</div>
</div>




<?= $this->Html->script('jquery/jQuery-2.1.3.min.js') ?>
<?= $this->Html->css('ui-dialog.css') ?>
<?= $this->Html->script('dialog-min.js') ?>

<?= $this->Html->script('lib.js') ?>


<?= $this->fetch('script_last');?>
</body>