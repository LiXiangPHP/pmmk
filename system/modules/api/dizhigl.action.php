<?php

class dizhigl extends SystemAction {


	public function dz(){
		$id = isset($_POST['id']) ? $_POST['id'] : null;
		$info = System::token_uid($id);
		if ($info['code']==200) {
			$db = System::load_sys_class('model');
			$data =  $db->GetList("select id,uid,sheng,shi,jiedao,youbian,shouhuoren,mobile  from `@#_member_dizhi` where uid='$info[uid]' ");
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