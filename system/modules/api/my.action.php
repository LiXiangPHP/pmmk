<?php

class my extends SystemAction {
//我的接口需求：
	public function m(){
		
		$db = System::load_sys_class('model');
		$id = isset($_POST['id']) ? $_POST['id'] : null;
		$data =  $db->GetOne("select img,money,score from `@#_member` where uid=$id ");
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
//基本信息页面:
	public function mbasic(){
		
		$db = System::load_sys_class('model');
		$id = isset($_POST['id']) ? $_POST['id'] : null;
		$data =  $db->GetOne("select img,sex,username from `@#_member` where uid=$id ");
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

?>