<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
<script src="<?php echo G_GLOBAL_STYLE; ?>/global/js/jquery-1.8.3.min.js"></script>
</head>
<body>
<div class="header lr10">
   	<?php echo $this->headerment();?>
</div>
<div class="bk10"></div>

<div class="table_form lr10">
<form action="" method="post" id="myform">
<table width="100%" class="lr10">
    <tr>
    	<td width="80">用户名</td> 
   		<td><?php echo $info['username']; ?></td>
    </tr>
    <tr>
    	<td>密码</td>
    	<td><input type="password" name="password" class="input-text" id="password" value=""></input></td>
    </tr>
    <tr>
    	<td>确认密码</td> 
    	<td><input type="password" name="pwdconfirm" class="input-text" id="pwdconfirm" value=""></input></td>
    </tr>
    <tr>
    	<td>E-mail</td>
    	<td><?php echo $info['useremail']; ?></td>
	</tr>
        <tr>
			<td align="right" width="100">系统设置权限：</td>
			<td>
				<input name="xitong" value="1" <?php if($info['xitong']){ ?> checked="checked" <?php } ?> type="radio">开
				<input name="xitong" value="0" <?php if(!$info['xitong']){ ?> checked="checked" <?php } ?> type="radio">关
			</td>

	</tr>
	    <tr>
			<td align="right" width="100">内容管理权限：</td>
			<td>
				<input name="neirong" value="1" <?php if($info['neirong']){ ?> checked="checked" <?php } ?>  type="radio">开
				<input name="neirong" value="0" <?php if(!$info['neirong']){ ?> checked="checked" <?php } ?> type="radio">关
			</td>

	</tr>
	    <tr>
			<td align="right" width="100">商品管理权限：</td>
			<td>
				<input name="shop" value="1" <?php if($info['shop']){ ?> checked="checked" <?php } ?>  type="radio">开
				<input name="shop" value="0" <?php if(!$info['shop']){ ?> checked="checked" <?php } ?> type="radio">关
			</td>

	</tr>

	    <tr>
			<td align="right" width="100">用户管理权限：</td>
			<td>
				<input name="user" value="1" <?php if($info['user']){ ?> checked="checked" <?php } ?>  type="radio">开
				<input name="user" value="0" <?php if(!$info['user']){ ?> checked="checked" <?php } ?> type="radio">关
			</td>

	</tr>
	    <tr>
			<td align="right" width="100">界面管理权限：</td>
			<td>
				<input name="jiemian" value="1" <?php if($info['yemian']){ ?> checked="checked" <?php } ?>  type="radio">开
				<input name="jiemian" value="0" <?php if(!$info['yemian']){ ?> checked="checked" <?php } ?> type="radio">关
			</td>

	</tr>
	    <tr>
			<td align="right" width="100">云应用权限：</td>
			<td>
				<input name="yun" value="1" <?php if($info['yun']){ ?> checked="checked" <?php } ?>  type="radio">开
				<input name="yun" value="0" <?php if(!$info['yun']){ ?> checked="checked" <?php } ?> type="radio">关
			</td>

	</tr>
</table>
   	<div class="bk15"></div>
    <input type="hidden" name="submit-1" />
    <input type="button" value=" 提交 " id="dosubmit" class="button">
</form>
</div><!--table-list end-->
<script type="text/javascript">
var error='';
var bool=false;
var id='';


$(document).ready(function(){		
		
	   document.getElementById('dosubmit').onclick=function(){
		   		bool=false;
				var myform=document.getElementById('myform');
				if(!myform.password.value){	
					error='密码不能为空';
					id='password';
					bool=false;
				}
				if(!myform.pwdconfirm.value && myform.password.value){
					error='请在次输入密码';
					id='pwdconfirm';
					bool=true;
				}
				
				
				if(bool){					
					window.parent.message(error,8,2);
					$('#'+id).focus();
					return false;
				}else{
					if(myform.password.value!=myform.pwdconfirm.value){
						window.parent.message("2次密码不相等",8,2);
						return false;
					}
					
					document.getElementById('myform').submit();					
				}
	   }
				
	
		
});

</script>
</body>
</html> 