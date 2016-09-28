<?php

class paylog extends SystemAction {
	public function lg(){
		$db = System::load_sys_class('model');
		$token = isset($_POST['token']) ? $_POST['token'] : null;
		$info = System::token_uid($token);
		if ($info['code']==200) {												
			$money = $db->GetList("select money from `@#_member` where uid='$info[uid]' ");
			$paylog = $db->GetList("select time,pay_type,money from `@#_member` where uid='$info[uid]'");
			print_r($paylog);die();
			if($data) {
				$code = 200;
				$msg = "查询成功";
			}else {
				$code = 400;
				$msg = "数据为空";
			}
			$json = array('type' => $type,'code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);			
		}else{
			$json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
			echo json_encode($json);
		}	
		
	}
}

?>