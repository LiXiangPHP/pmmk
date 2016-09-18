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
<script src="<?php echo G_GLOBAL_STYLE; ?>/global/js/jquery-1.8.3.min.js"></script>
<script src="<?php echo G_PLUGIN_PATH; ?>/uploadify/api-uploadify.js" type="text/javascript"></script> 
<script type="text/javascript">
var editurl=Array();
editurl['editurl']='<?php echo G_PLUGIN_PATH; ?>/ueditor/';
editurl['imageupurl']='<?php echo G_ADMIN_PATH; ?>/ueditor/upimage/';
editurl['imageManager']='<?php echo G_ADMIN_PATH; ?>/ueditor/imagemanager';
</script>
<script type="text/javascript" charset="utf-8" src="<?php echo G_PLUGIN_PATH; ?>/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo G_PLUGIN_PATH; ?>/ueditor/ueditor.all.min.js"></script>
<style>
	.bg{background:#fff url(<?php echo G_GLOBAL_STYLE; ?>/global/image/ruler.gif) repeat-x scroll 0 9px }
</style>
</head>
<body>
<div class="header-title lr10">
	<?php //echo $this->headerment();?>
    <b>热门问题详情</b>
</div>
<div class="bk10"></div>
<div class="table_form lr10">
<form method="post" action="">
	<table width="100%"  cellspacing="0" cellpadding="0">
		
		 <tr>
			<td align="right">问题内容：</td>
			<td><textarea name="description" disabled="disable" class="wid400" onKeyUp="gbcount(this,300,'textdescription');" style="height:60px"><?php echo $info['issue']; ?></textarea>
            </td>
            <td align="right">回复详情：</td>
			<td><textarea name="detail" disabled="disable" class="wid400" onKeyUp="gbcount(this,300,'textdescription');" style="height:60px"><?php echo $info['reply']; ?></textarea>
            </td>
		</tr>
               
        <tr height="60px">
			<td align="right"></td>
		</tr>
	</table>
</form>
</div>
<span id="title_colorpanel" style="position:absolute; left:568px; top:115px" class="colorpanel"></span>
<script type="text/javascript">
    //实例化编辑器
    var ue = UE.getEditor('myeditor');
    ue.addListener('ready',function(){
        this.focus()
    });
	
	var info=new Array();
    function gbcount(message,maxlen,id){
		
		if(!info[id]){
			info[id]=document.getElementById(id);
		}			
        var lenE = message.value.length;
        var lenC = 0;
        var enter = message.value.match(/\r/g);
        var CJK = message.value.match(/[^\x00-\xff]/g);//计算中文
        if (CJK != null) lenC += CJK.length;
        if (enter != null) lenC -= enter.length;		
		var lenZ=lenE+lenC;		
		if(lenZ > maxlen){
			info[id].innerHTML=''+0+'';
			return false;
		}
		info[id].innerHTML=''+(maxlen-lenZ)+'';
		
    }
	function set_title_color(color) {
	$('#title').css('color',color);
	$('#title_style_color').val(color);
}

function set_title_bold(){
	if($('#title_style_bold').val()=='bold'){
		$('#title_style_bold').val('');	
		$('#title').css('font-weight','');
	}else{
		$('#title').css('font-weight','bold');
		$('#title_style_bold').val('bold');
	}
}
	//API JS
	//window.parent.api_off_on_open('open');
</script>
</body>
</html> 