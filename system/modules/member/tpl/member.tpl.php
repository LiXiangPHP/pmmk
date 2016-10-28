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

<div class="bk10"></div>

<div class="bk10"></div>



<div class="bk10"></div>
<form action="#" method="post" name="myform">
<div class="table-list lr10">

        <table width="100%" cellspacing="0">
     	<thead>
        		<tr>
                    <th width="5%">百分比</th>       
                    <th width="25%">操作</th> 

				</tr>
        </thead>
        <tbody>				
        	
            <tr>
            
                <td><input type="text"  style='width: 50px' value='<?php echo $bili['bili'];?>' id='bili'>%</td>
                <td><input type="button" class="button" value=" 保存 " id='<?php echo $bili['id'];?>'  onclick='change_set(this)'/></td>
               
                
            </tr>
            
        </tbody>
     </table>     

    </form>
	


<script>

function change_set(o)
{
    var bili = $('#bili').val();
    var id = $(o).attr('id');
    $.ajax( {  
         url:'?/member/member/ticheng',// 跳转到 action  
          data:{  
                   
                   bili : bili,
                   id   : id
        },  
         type:'post',
         success:function(data) {  
            if(data)
            {
                alert('修改成功');
                

            }
            else{
                alert('修改失败');
            }
          }  
        });
}
</script>

</body>
</html> 