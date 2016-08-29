<?php defined('G_IN_SYSTEM')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html>
<head><meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title><?php echo $key; ?></title>
<meta content="app-id=518966501" name="apple-itunes-app" /><meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, user-scalable=no, maximum-scale=1.0" /><meta content="yes" name="apple-mobile-web-app-capable" /><meta content="black" name="apple-mobile-web-app-status-bar-style" /><meta content="telephone=no" name="format-detection" /><link href="<?php echo G_TEMPLATES_CSS; ?>/mobile/comm.css" rel="stylesheet" type="text/css" /><link href="<?php echo G_TEMPLATES_CSS; ?>/mobile/single.css" rel="stylesheet" type="text/css" /><script src="<?php echo G_TEMPLATES_JS; ?>/mobile/jquery190.js" language="javascript" type="text/javascript"></script><script id="pageJS" data="<?php echo G_TEMPLATES_JS; ?>/mobile/BuyRecord.js" language="javascript" type="text/javascript"></script></head>
<body>
<div class="h5-1yyg-v1" id="loadingPicBlock">
    
<!-- 栏目页面顶部 -->


<!-- 内页顶部 -->

    <header class="g-header">
        <div class="head-l">
	        <a href="javascript:;" onclick="history.go(-1)" class="z-HReturn"><s></s><b>返回</b></a>
        </div>
        <h2><?php echo $key; ?></h2>
        <div class="head-r">
	       <a href="<?php echo WEB_PATH; ?>/mobile/mobile" class="z-Home"></a>
        </div>
    </header>

    <input name="hidCodeID" type="hidden" id="hidCodeID" value="18101" />
    <input name="hidIsEnd" type="hidden" id="hidIsEnd" value="1" />

    <!-- 晒单记录 -->
    <section class="goodsCon">
	    <div class="cSingleCon">
			<p class="cEntry">已有<em class="orange"><?php echo count($shaidan); ?></em>个幸运者晒单，<em class="orange"><?php echo $sum; ?></em>条评论！</p>
	        <div id="postList" class="cSingleCon2" style="display:block;" z-minheight>
				<?php $ln=1;if(is_array($shaidan)) foreach($shaidan AS $sd): ?>
				<div class="cSingleInfo">
					<dl class="fl"><a href="<?php echo WEB_PATH; ?>/mobile/mobile/userindex/<?php echo $sd['sd_userid']; ?>"><img src="<?php echo G_UPLOAD_PATH; ?>/<?php echo get_user_key($sd['sd_userid'],'img'); ?>"><b></b></a></dl>
					<div class="cSingleR m-round" id="<?php echo $sd['sd_id']; ?>">
						<ul>
							<li><em class="blue" uweb="<?php echo $sd['sd_userid']; ?>"><?php echo userid($sd['sd_userid'],'username'); ?></em><strong>：</strong><span><?php echo $sd['sd_title']; ?></span></li>
							<?php 
								$qs=$this->db->GetOne("select * from `@#_shoplist` where `id`='$sd[sd_shopid]'");				
							 ?>							
							<li><h3><b>第<?php echo $qs['qishu']; ?>期晒单</b> <?php echo date('Y-m-d H:i:s',$sd['sd_time']); ?></h3></li>
							<li><p><?php echo $sd['sd_content']; ?></p></li>
							<li class="maxImg">
							<?php 
								$p=trim($sd['sd_photolist'],";");
								$img=explode(';',$p);
								foreach($img as $i){
									echo '<img src="'.G_UPLOAD_PATH.'/'.$i.'">';
								}								
							 ?>
							</li>
							<li><dd><s></s><strong><?php echo $sd['sd_zhan']; ?></strong>人羡慕嫉妒</dd><dd><i></i><strong><?php echo $sd['sd_ping']; ?></strong>条评论</dd></li>
						</ul><b class="z-arrow"></b>
					</div>
				</div>
				<?php  endforeach; $ln++; unset($ln); ?>
			</div>
        </div>
    </section>
   

    
<?php include templates("mobile/index","footer");?>
<script language="javascript" type="text/javascript">
//跳转页面
	$(".cSingleInfo li:not(:first)").click(function(){
		var id=$(this).parent().parent().attr('id');
		if(id){
			window.location.href="<?php echo WEB_PATH; ?>/mobile/shaidan/detail/"+id;
		}			
	});
	$(".cSingleInfo .blue").click(function(){
		var id=$(this).attr('uweb');
		if(id){
			window.location.href="<?php echo WEB_PATH; ?>/mobile/mobile/userindex/"+id;
		}	
	});	
</script>

</div>
</body>
</html>
