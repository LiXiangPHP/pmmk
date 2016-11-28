<?php

class paylog extends SystemAction {
	public function lg(){
		$token = isset($_POST['token']) ? $_POST['token'] : null;
		$info = System::token_uid($token);
		if ($info['code']==200) {
			$db = System::load_sys_class('model');
			$money =  $db->GetOne("select money from `@#_member` where uid='$info[uid]' ");
			$data = $data = $db->GetList("select time,pay_type,money from `@#_member_addmoney_record` where uid='$info[uid]' and status like '%已付款%'");
			if($data&&$money) {
				$code = 200;
				$msg = "查询成功";
			}else {
				$code = 400;
				$msg = "数据为空";
			}
			$json = array('code' => $code, 'msg' => $msg, 'moneytoal' => $money, 'data' => $data);
			echo json_encode($json);
		}else{
			$json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
			echo json_encode($json);
		}
		
		

	}
}

?>