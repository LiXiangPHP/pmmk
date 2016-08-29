<?php defined('G_IN_SYSTEM')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, user-scalable=no, maximum-scale=1.0"/>
    <title>我的<?php echo _cfg('web_name_two'); ?> - <?php echo $webname; ?>触屏版</title>
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <link href="<?php echo G_TEMPLATES_CSS; ?>/mobile/comm.css?v=130715" rel="stylesheet" type="text/css" /><link href="<?php echo G_TEMPLATES_CSS; ?>/mobile/member.css?v=130726" rel="stylesheet" type="text/css" /><link href="<?php echo G_TEMPLATES_CSS; ?>/mobile/member(2).css" rel="stylesheet" type="text/css" /><script src="<?php echo G_TEMPLATES_JS; ?>/mobile/jquery190.js" language="javascript" type="text/javascript"></script>

</head>
<body>
<div class="h5-1yyg-v11">
    
<!-- 栏目页面顶部 -->


<!-- 内页顶部 -->

    <header class="g-header">
        <div class="head-l">
	        <a href="javascript:;" onclick="history.go(-1)" class="z-HReturn"><s></s><b>返回</b></a>
        </div>
        <h2><?php echo $title; ?></h2>
        <div class="head-r">
	        <a href="<?php echo WEB_PATH; ?>/mobile/mobile" class="z-Home"></a>
        </div>
    </header>

    <section class="clearfix g-member">
	    
	  <!--   <div class="m-round m-member-nav">
		    <ul id="ulFun">
			    <li><a href="<?php echo WEB_PATH; ?>/mobile/home/modify"><b class="z-arrow"></b>昵称<span class="fr"><?php echo $member['username']; ?></span></a></li>
		    </ul>
	    </div>
 -->
         <div class="m-round m-member-nav">
         <?php $ln=1;if(is_array($member_dizhi)) foreach($member_dizhi AS $v): ?>
            <ul id="ulFun">
                <li><?php echo $v['sheng']; ?>,<?php echo $v['shi']; ?>,<?php echo $v['xian']; ?><?php if($v['default']=='Y'): ?> <i style="color:red">默认地址</i>

                <span class="fr">  <a href="">编辑</a>

                <?php  else: ?>     <i style="color:green;cursor:pointer">设为默认</i> <span class="fr"> <a href="javascript:;" onclick="delAddress(<?php echo $v['id']; ?>)">删除</a></span> <span class="fr"> <a href="">编辑</a><?php endif; ?>
                </span></li>
            </ul>
         
        <?php  endforeach; $ln++; unset($ln); ?>

        </div>
        <div class="edit-wrapper">
<a id="btnT1" href="<?php echo WEB_PATH; ?>/mobile/home/addAddress" class="s-btn" >新增收货地址</a>
</div>
    </section>
    
<?php include templates("mobile/index","footer");?>
<script language="javascript" type="text/javascript">
  var Path = new Object();
  Path.Skin="<?php echo G_WEB_PATH; ?>/statics/templates/yungou";  
  Path.Webpath = "<?php echo WEB_PATH; ?>";
  
var Base={head:document.getElementsByTagName("head")[0]||document.documentElement,Myload:function(B,A){this.done=false;B.onload=B.onreadystatechange=function(){if(!this.done&&(!this.readyState||this.readyState==="loaded"||this.readyState==="complete")){this.done=true;A();B.onload=B.onreadystatechange=null;if(this.head&&B.parentNode){this.head.removeChild(B)}}}},getScript:function(A,C){var B=function(){};if(C!=undefined){B=C}var D=document.createElement("script");D.setAttribute("language","javascript");D.setAttribute("type","text/javascript");D.setAttribute("src",A);this.head.appendChild(D);this.Myload(D,B)},getStyle:function(A,B){var B=function(){};if(callBack!=undefined){B=callBack}var C=document.createElement("link");C.setAttribute("type","text/css");C.setAttribute("rel","stylesheet");C.setAttribute("href",A);this.head.appendChild(C);this.Myload(C,B)}}
function GetVerNum(){var D=new Date();return D.getFullYear().toString().substring(2,4)+'.'+(D.getMonth()+1)+'.'+D.getDate()+'.'+D.getHours()+'.'+(D.getMinutes()<10?'0':D.getMinutes().toString().substring(0,1))}
Base.getScript('<?php echo G_TEMPLATES_JS; ?>/mobile/Bottom.js');
Base.getScript('<?php echo G_TEMPLATES_JS; ?>/mobile/pageDialog.js');
var d = function(q) {
    $.PageDialog.fail(q)
};
var h = function(q) {
    $.PageDialog.ok("<s></s>" + q)
};
function delAddress(id){
    if (confirm("您确认要删除该条信息吗？")){
        var aj = $.ajax( {  
         url:'<?php echo WEB_PATH; ?>/mobile/ajax/delAddress/',// 跳转到 action  
          data:{  
                   
                   id : id,
        },  
         type:'post',
         success:function(data) {  
            var json = eval("(" + data + ")");
             if(json.code){  
                 // view("修改成功！");  
                 d(json.msg);
                 // d(data.msg); 
                  
             }else{  
                 h('删除成功');
                 location.href = Gobal.Webpath+"/mobile/home/address";
            }  
          },  
          error : function() {  
               // view("异常！");  
               h('错误') ;
          }  
        });
    }
}
</script>

</div>
</body>
</html>
