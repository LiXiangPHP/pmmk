<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
<script src="<?php echo G_GLOBAL_STYLE; ?>/global/js/jquery-1.8.3.min.js"></script>
<script src="<?php echo G_PLUGIN_PATH; ?>/uploadify/api-uploadify.js" type="text/javascript"></script> 
<style>
table th{ border-bottom:1px solid #eee; font-size:12px; font-weight:100; text-align:right; width:200px;}
table td{ padding-left:10px;}
input.button{ display:inline-block}
</style>
</head>
<body>
<div class="header lr10">
	<h3><a href = "<?php echo WEB_PATH; ?>/member/member/sign_lists">签到管理</a>&nbsp;&nbsp;&nbsp;&nbsp;</h3>
	<h3><a href = "<?php echo WEB_PATH; ?>/member/member/sign_rules">连续签到-奖励规则设置</a></h3>
</div>
<div class="bk10"></div>
<div class="table_form lr10">
<!--start-->
<form name="myform" action="" method="post" enctype="multipart/form-data">
  <table width="100%" cellspacing="0">
  
  	<?php foreach($signlist as $sign) { ?>
  	 <tr>
  	 <?php if($sign['number'] == 1) { ?>
  	 		<td width="300" align="right">连续签到&nbsp;&nbsp;<input type="text" name="day-<?php echo $sign['id'] ?>" value="<?php echo $sign['number'] ?>" disabled = 'disabled' class="input-text">&nbsp;&nbsp;天：</td>
  	 <?php }else{ ?>
  	 		<td width="300" align="right">连续签到&nbsp;&nbsp;<input type="text" name="day-<?php echo $sign['id'] ?>" value="<?php echo $sign['number'] ?>" class="input-text">&nbsp;&nbsp;天：</td>
  	 <?php } ?>	
			<td><input type="text" name="num-<?php echo $sign['id'] ?>" value="<?php echo $sign['points'];?>" class="input-text">&nbsp;&nbsp;积&nbsp;分</td>
		</tr>
	<?php } ?>	
		<tr>
        	<td width="300" align="right"></td>
            <td>		
            <input type="submit" class="button" name="submit" value="提交" >
            </td>
		</tr>
</table>
</form>
</div><!--table-list end-->

<script>
function upImage(){
	return document.getElementById('imgfield').click();
}
</script>
</body>
</html> 