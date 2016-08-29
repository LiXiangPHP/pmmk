<?php defined('G_IN_SYSTEM')or exit('No permission resources.'); ?><!-- 栏目页面顶部 -->

    <header class="header">
        <h1 class="fl"><span><?php echo $webname; ?></span><a href="<?php echo WEB_PATH; ?>/mobile/mobile"><img src="<?php echo G_UPLOAD_PATH; ?>/<?php echo Getlogo(); ?>"/></a></h1>
        <div class="fl u-slogan"></div>
        <div class="fr head-r">
            <a href="<?php echo WEB_PATH; ?>/mobile/user/login" class="z-Member"></a>
            <a id="btnCart" href="<?php echo WEB_PATH; ?>/mobile/cart/cartlist" class="z-shop"></a>
        </div>
    </header> 
    <!-- 栏目导航 -->
    