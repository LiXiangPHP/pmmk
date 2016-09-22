<?php

class about extends SystemAction {
	public function issue(){
		
		$db = System::load_sys_class('model');
		$uid = isset($_POST['uid']) ? $_POST['uid'] : null;
		$issue = isset($_POST['issue']) ? $_POST['issue'] : null;
		
		if($issue&&$uid) {
			$code = 200;
			$msg = "添加成功";
			$data =  $db->Query("INSERT INTO `@#_help` (`uid`, `issue`) VALUES ('$uid',  '$issue')");
		}else {
			$code = 100;
			$msg = "数据为空";
		}
		$json = array('code' => $code, 'msg' => $msg); 


		echo json_encode($json);
		

	}
}

?>