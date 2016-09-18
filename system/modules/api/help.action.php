<?php

class help extends SystemAction {

//问题详情
	public function issuelist(){
		
		$db = System::load_sys_class('model');
		$id = isset($_POST['id']) ? $_POST['id'] : null;
		$data =  $db->GetOne("select issue,reply from `@#_help` where id=$id ");
		if($data) {
			$code = 200;
			$msg = "查询成功";
		}else {
			$code = 100;
			$msg = "数据为空";
		}
		$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
		echo json_encode($json);
		

	}
//提问
	public function issue(){
		
		$db = System::load_sys_class('model');
		$uid = isset($_POST['uid']) ? $_POST['uid'] : null;
		$issue = isset($_POST['issue']) ? $_POST['issue'] : null;
		
		if($issue) {
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