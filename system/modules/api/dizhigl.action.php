<?php

class dizhigl extends SystemAction {


	public function dz(){
		$token = isset($_POST['token']) ? $_POST['token'] : null;
		$info = System::token_uid('token');
		if ($info['code']==200) {
			$db = System::load_sys_class('model');
			$data =  $db->GetList("select id,uid,sheng,shi,xian,jiedao,youbian,shouhuoren,mobile  from `@#_member_dizhi` where uid='$info[uid]' ");
			if($data) {
				$code = 200;
				$msg = "查询成功";
			}else {
				$code = 400;
				$msg = "数据为空";
			}
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);
		
		}else{
			$json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
			echo json_encode($json);
		}		
	}

}

?>