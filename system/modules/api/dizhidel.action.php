<?php

class dizhidel extends SystemAction {

	public function dzdel(){
		$uid = isset($_POST['uid']) ? $_POST['uid'] : null;
		$info = System::token_uid($uid);
		if ($info['code']==200) {
			$db = System::load_sys_class('model');
			$id = isset($_POST['id']) ? $_POST['id'] : null; 
			$shaidanx = $db->getOne("select * from `@#_member_dizhi` where uid='$info[uid]' and id=$id ");
			 if(empty($shaidanx)){
				$code = 100;
			 	$msg = "没有数据";  
		     }else{
	    		  if($db->Query("DELETE FROM `@#_member_dizhi` where uid='$info[uid]' and id=$id ")!==false){
	    			 $code = 200;
	    			 $msg = "删除成功";
				  }else{
						$code = 400;
						$msg = "删除失败";
				  }
	        }
			$json = array('code' => $code, 'msg' => $msg);
			echo json_encode($json);		
		}else{
			$json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
			echo json_encode($json);
		}
		
	}

}

?>