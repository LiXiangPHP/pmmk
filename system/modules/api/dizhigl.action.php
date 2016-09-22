<?php

class dizhigl extends SystemAction {


	public function dz(){
		
		
		$db = System::load_sys_class('model');
		$id = isset($_POST['id']) ? $_POST['id'] : null;
		$data =  $db->GetList("select id,uid,sheng,shi,xian,jiedao,youbian,shouhuoren,mobile  from `@#_member_dizhi` where uid=$id ");
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

}

?>