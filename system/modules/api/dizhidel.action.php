<?php

class dizhidel extends SystemAction {

	public function dzdel(){
		$db = System::load_sys_class('model');
		$uid = isset($_POST['uid']) ? $_POST['uid'] : null; 
		$id = isset($_POST['id']) ? $_POST['id'] : null; 
		$shaidanx = $db->getlist("select * from `@#_member_dizhi` where uid=$uid and id=$id ");
		 if(empty($shaidanx)){
			$code = 100;
		 	$msg = "没有数据";  
	     }else{
    		  if($db->Query("DELETE FROM `@#_member_dizhi` where uid=$uid and id=$id ")!==false){
    			 $code = 200;
    			 $msg = "删除成功";
			  }else{
					$code = 300;
					$msg = "删除失败";
			  }
        }
		$json = array('code' => $code, 'msg' => $msg);
		echo json_encode($json);		
	}

}

?>