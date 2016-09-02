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
<style>
body{ background-color:#fff}
tr{ text-align:center}
</style>
</head>
<body>
<div class="header lr10">
	<?php echo $this->headerment();?>
</div>
<div class="bk10"></div>

<div class="bk10"></div>



<div class="bk10"></div>
<form action="#" method="post" name="myform">
<div class="table-list lr10">
	
	

        <table width="100%" cellspacing="0">
     	<thead>
        		<tr>
                    <th width="5%">ID</th>  
                    <th width="10%">用户名</th>       
                    <th width="20%">下注</th>    
                    <th width="8%">赔率</th>             
                    <th width="10%">期数</th>
                    <th width="25%">下注时间</th>
                    <th width="15%">管理</th>
				</tr>
        </thead>
        <tbody>				
        	<?php foreach($betlist as $v) { ?>
            <tr>

                <td><?php echo $v['id'];?></td>
                <td><?php $username = $this->db->GetOne("SELECT username FROM `@#_member` WHERE uid =".$v['uid']); echo $username['username']; ?></td>
                <td><span style=""><?php echo $v['name'];?></span></td>
                <td><span style=""><?php echo $v['odds'];?></span></td>
                <td><span style=""><?php echo $v['issue'];?></span></td>
                <td><span style=""><?php echo $v['time'];?></span></td>

                <td class="action"> 
                [<a href="<?php echo G_ADMIN_PATH; ?>/content/goods_edit/<?php echo $v['id'];?>">修改</a>] 
				[<a href="javascript:;"  id="<?php echo $v['id'];?>" class="del_good">删除</a>]
				</td>
            </tr>
            <?php } ?>
        </tbody>
     </table>     

    </form>
	
   <div class="btn_paixu">
  	
  </div>
    	<div id="pages"><ul><li>共 <?php echo $total; ?> 条</li><?php echo $page->show('one','li'); ?></ul></div>
</div>


<script>
$(function(){
	
	$("a.del_good").click(function(){
		
		var id = $(this).attr("id");
		var str = "<?php echo G_ADMIN_PATH; ?>/paoma/bet_del/"+id;
		var o = confirm("确认删除该下注信息.不可恢复");
		if(o){
			window.parent.btn_map(str);
		}	
	
	});


});
</script>




<?php if($this->segment(4)=='maxqishu'): ?>
<!--期数修改弹出框-->
<style>
#paywindow{position:absolute;z-index:999; display:none}
#paywindow_b{width:372px;height:360px;background:#2a8aba; filter:alpha(opacity=60);opacity: 0.6;position:absolute;left:0px;top:0px; display:block}
#paywindow_c{width:360px;height:338px;background:#fff;display:block;position:absolute;left:6px;top:6px;}
.p_win_title{ line-height:40px;height:40px;background:#f8f8f8;}
.p_win_title b{float:left}
.p_win_title a{float:right;padding:0px 10px;color:#f60}
.p_win_content h1{font-size:25px;font-weight:bold;}
.p_win_but,.p_win_mes,.p_win_ctitle,.p_win_text{ margin:10px 20px;}
.p_win_mes{border-bottom:1px solid #eee;line-height:35px;}
.p_win_mes span{margin-left:10px;}
.p_win_ctitle{overflow:hidden;}
.p_win_x_b{float:left; width:73px;height:68px;background-repeat:no-repeat;}
.p_win_x_t{ font-size:18px; font-weight:bold;font-family: "Helvetica Neue",\5FAE\8F6F\96C5\9ED1,Tohoma;color:#f00; text-align:center}
.p_win_but{ height:40px; line-height:40px;}
.p_win_but a{ padding:8px 15px; background:#f60; color:#fff;border:1px solid #f50; margin:0px 15px;font-family: "Helvetica Neue",\5FAE\8F6F\96C5\9ED1,Tohoma; font-size:15px; }
.p_win_but a:hover{ background:#f50}
.p_win_text a{ font-size:13px; color:#f60}
.pay_window_quit:hover{ color:#f00}
</style>
<div id="paywindow">
	<div id="paywindow_b"></div>
	<div id="paywindow_c">
		<div class="p_win_title"><a href="javascript:void();" class="pay_window_quit">[关闭]</a><b>：：：重设期数</b></div>
		<div class="p_win_content">			
			<div class="p_win_mes">
            	 <b>标题:　　　</b><span id="max_title">...</span>   		
            </div>
			<div class="p_win_mes">
            	 <b>原最大期数:</b><span id="max_qishu">0</span>　期
            </div>
			<div class="p_win_mes">
            	 <b>新最大期数:</b><input type="text" id="max_new_qishu" class="input-text" style="width:50px;margin-left:10px;">需要大于原最大期数
            </div>
			<div class="p_win_mes">
            	 <b>新商品售价:</b><input type="text" id="max_one_money" class="input-text" style="width:50px;margin-left:10px;">商品每一次购买的价格
            </div>
			<div class="p_win_mes">
            	 <b>新商品总价:</b><input type="text" id="max_new_money" class="input-text" style="width:50px;margin-left:10px;">商品的总价格		
            </div>
            <div class="p_win_mes">    	    	
    			 	<input type="button" value=" 更新并新建一期 " class="button" id="max_button" onclick="set_shop_qishu(this)">              
            </div>	
		</div>
	</div>
</div>

<script>
$(function(){
	var width = ($(window).width()-372)/2;
	var height = ($(window).height()-360)/2;
	$("#paywindow").css("left",width);
	$("#paywindow").css("top",height);
		
	$(".pay_window_quit").click(function(){
		$("#paywindow").hide();								 
	});
	
		
	$(".pay_window_show").click(function(){
		var gid    = $(this).attr("gid");
		var gtitle = $(this).attr("gtitle");
		var gqishu = $(this).attr("gqishu");
		var gmoney = $(this).attr("gmoney");
		var gonemoney = $(this).attr("gonemoney");
		
		$("#max_one_money").val(gonemoney);
		$("#max_new_money").val(gmoney);
		
		$("#max_new_qishu").val(parseInt(gqishu)+100);
		$("#max_qishu").text(gqishu);
		$("#max_title").text(gtitle);
		$("#max_button").attr("onclick","set_shop_qishu("+gid+")");		
		$("#paywindow").show();	
	
	});
		
});



function set_shop_qishu(T){
	
	var yqishu = parseInt($("#max_qishu").text());
	var tqishu = parseInt($("#max_new_qishu").val());
	var money = parseInt($("#max_new_money").val());
	var onemoney = parseInt($("#max_one_money").val());
	
	if(!money || !onemoney || (money < onemoney)){
		window.parent.message("商品价格填写不正确!",8,2);
		return;
	}
	if(tqishu <= yqishu){	
		window.parent.message("新期数不能小于原来的商品期数",8,2);
		return;
	}
	if(!tqishu){	
		window.parent.message("新期数不能为空",8,2);
		return;
	}
	
	$.post("<?php echo G_MODULE_PATH; ?>/content/goods_max_qishu/dosubmit/",{"gid":T,"qishu":tqishu,"money":money,"onemoney":onemoney},function(datas){
		var data = jQuery.parseJSON(datas);		
		if(data.err == '-1'){		
			window.parent.message(data.meg,8,2);		
			return;
		}else{			
			window.parent.message(data.meg,8,2);
			window.parent.btn_iframef5();
			return;
		}		
		
	});
}
</script>
<!--期数修改弹出框-->
<?php endif; ?>
</body>
</html> 