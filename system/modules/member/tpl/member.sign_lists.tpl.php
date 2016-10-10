<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台首页</title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
<style>
body{ background-color:#fff}
</style>
</head>
<body>
<script>
function signs(id){
	if(confirm("您确认要删除签到信息")){
		window.location.href="<?php echo WEB_PATH;?>/member/member/sign_del"+id;
	}
}
</script>
<div class="header lr10">
	<h3><a href = "<?php echo WEB_PATH; ?>/member/member/sign_lists">签到管理</a>&nbsp;&nbsp;&nbsp;&nbsp;</h3>
	<h3><a href = "<?php echo WEB_PATH; ?>/member/member/sign_rules">连续签到-奖励规则设置</a></h3>
</div>
<div class="bk10"></div>
<div class="table-list lr10">

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="mgr_table">
	<tr class="thead" align="center">
		<td>ID</td>
		<td>用户名</td>
		<td>手机号</td>
		<td>最后签到</td>
		<td>连续签到</td>
		<td>所获积分</td>
		<td>管理</td>
	</tr>
	<?php foreach($signlist AS $v) { ?>
	<tr align="center" class="mgr_tr">
		<td height="30"><?php echo $v['uid'];?></td>		
		<td><?php echo $v['username'];?></td>
		<td class="number"><?php echo $v['mobile'];?></td>
		<td><?php echo date('Y-m-d h:i:s',$v['sign_in_date']);?></td>
		<td><?php echo $v['sign_in_time'];?></td>
		<td><?php echo $v['oscore'];?></td>
		<td class="action">
		<span>[<a onclick="signs(<?php echo $v['id'];?>)" href="javascript:;">删除</a>]</span>
		</td>		
	</tr>
	<?php } ?> 
</table>

<?php if($total>$num) {?> 
<div id="pages"><ul><li>共 <?php echo $total; ?> 条</li><?php echo $page->show('one','li'); ?></ul></div>

<?php } ?> 	
</body>
</html> 
