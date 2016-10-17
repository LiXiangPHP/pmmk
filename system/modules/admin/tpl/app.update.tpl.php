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
	<h3><a href = "<?php echo WEB_PATH; ?>/member/member/scorerules">APP版本更新</a>&nbsp;&nbsp;&nbsp;&nbsp;</h3>
</div>
<div class="bk10"></div>
<div class="table_form lr10">
<!--start-->
<form name="myform" action="" method="post" enctype="multipart/form-data">
  <table width="100%" cellspacing="0">
  
  	<?php foreach($Alist as $sign) { ?>
  	 <tr>
  	 		<td width="300" align="right">版&nbsp;&nbsp;本:<input type="text" name="banben" value="<?php echo $sign['banben'] ?>" class="input-text"></td>	
			<td>新版本&nbsp;url&nbsp;:<input type="text" style="width:400px;" name="aurl" value="<?php echo $sign['gengxinurl'];?>" class="input-text"></td>
		</tr>
	<?php } ?>	
		<tr>
        	<td width="300" align="right">
            <input type="hidden"  name="id" value="<?php echo $sign['id'];?>" >
            <input type="submit" class="button" name="submit" value="提交" > 
          </td>
            <td>  </td>
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