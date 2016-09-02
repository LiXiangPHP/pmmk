<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台首页</title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_PLUGIN_PATH; ?>/calendar/calendar-blue.css" type="text/css"> 
<script type="text/javascript" charset="utf-8" src="<?php echo G_PLUGIN_PATH; ?>/calendar/calendar.js"></script>
<style>
body{ background-color:#fff}
.header-data{
	border: 1px solid #FFBE7A;
	zoom: 1;
	background: #FFFCED;
	padding: 8px 10px;
	line-height: 20px;
}
.table-list  tr {
	text-align:center;
}

</style>
</head>
<body>
<div class="header lr10">
	<?php echo $this->headerment();?>
</div>
<div class="bk10"></div>

<div class="bk10"></div>
<form action="#" method="post" name="myform">
<div class="table-list lr10">
	 <table width="100%" cellspacing="0">
     	<thead>
        		<tr>
					<th>批量操作</th>  
					<th>ID</th>  
					<th>所属会员</th>  
                    <th>提问内容</th>
                    <th>审核状态</th>
                    <th>提问时间</th>        
                    <th>管理</th>
				</tr>
        </thead>
    <tbody>				
        	<?php foreach($helplist AS $v) { ?>
            <tr>
              <td align='center'><input name='hcheck[]' type='checkbox' value="<?php echo $v['id'];?>" class='input-text-c'></td>
                <td><?php echo $v['id'];?></td>
				<td><?php echo $v['username']; ?></a></td>
                <td><?php echo $v['issue'];?></td>
                <td><?php echo $v['is_check'] == 0?'未审核':'已回复'; ?></td>
                <td><?php echo $v['times'];?></td>
                <td class="action">
                <a href="<?php echo G_ADMIN_PATH; ?>/help/help_reply/<?php echo $v['id'];?>">回复</a>
                <span class='span_fenge lr5'>|</span>    
                <a href="javascript:window.parent.Del('<?php echo G_ADMIN_PATH.'/help/help_del/'.$v['id'];?>', '确认删除这条记录吗？');">删除</a>
				</td>
            </tr>
            <?php } ?>
        </tbody>
     </table>
     </form>
   <div class="btn_paixu">
  	<div style="width:80px; text-align:center; margin-left:65px;">
          <input type="checkbox" class="button" name="all" onclick="checkAll(this)"/>&nbsp;&nbsp;全选
    </div>
	<script type="text/javascript">
		function checkAll(obj) {
			var checkboxs = document.getElementsByName('hcheck[]');
			for(var i=0;i<checkboxs.length;i++) {
				checkboxs[i].checked = obj.checked;
			}
		}
	</script>
  </div>
  <div class="btn_paixu">
  	<div style="width:80px; text-align:center; margin-left:65px;">
          <input type="button" class="button" value="删除" onclick="myform.action='<?php echo G_MODULE_PATH; ?>/help/help_delall';myform.submit();"/>
    </div>
  </div>
<div id="pages"><ul><li>共 <?php echo $total; ?> 条</li><?php echo $page->show('one','li'); ?></ul></div>
</div>

</body>
</html>