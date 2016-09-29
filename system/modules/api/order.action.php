<?php

class order extends SystemAction {
	public function od(){
		$token = isset($_POST['token']) ? $_POST['token'] : null;
		$info = System::token_uid($token);
		$type = isset($_POST['type']) ? $_POST['type'] : null;
		if ($info['code']==200) {
			if ($type==1) {
				$db = System::load_sys_class('model');
				$data = $db->GetList("select a.status,b.q_end_time,a.shopname,a.shopqishu,b.thumb from `@#_member_go_record` as a,`@#_shoplist` as b where a.shopid=b.id and  a.uid='$info[uid]' and a.huode>10000000 and status like '%未发货%' ");
				if($data) {
					$code = 200;
					$msg = "查询成功";
				}else {
					$code = 400;
					$msg = "数据为空";
				}
				$json = array('code' => $code, 'msg' => $msg, 'moneytoal' => $money, 'data' => $data);
				echo json_encode($json);
			}

			if ($type==2) {
				$db = System::load_sys_class('model');
				$data = $db->GetList("select a.status,b.q_end_time,a.shopname,a.shopqishu,b.thumb from `@#_member_go_record` as a,`@#_shoplist` as b where a.shopid=b.id and  a.uid='$info[uid]' and a.huode>10000000 and status like '%已发货,待收货%' ");
				if($data) {
					$code = 200;
					$msg = "查询成功";
				}else {
					$code = 400;
					$msg = "数据为空";
				}
				$json = array('code' => $code, 'msg' => $msg, 'moneytoal' => $money, 'data' => $data);
				echo json_encode($json);
			}

			if ($type==3) {
				$db = System::load_sys_class('model');
				$data = $db->GetList("select a.status,b.q_end_time,a.shopname,a.shopqishu,b.thumb from `@#_member_go_record` as a,`@#_shoplist` as b where a.shopid=b.id and  a.uid='$info[uid]' and a.huode>10000000 and status like '%已发货,已完成%' ");
				if($data) {
					$code = 200;
					$msg = "查询成功";
				}else {
					$code = 400;
					$msg = "数据为空";
				}
				$json = array('code' => $code, 'msg' => $msg, 'moneytoal' => $money, 'data' => $data);
				echo json_encode($json);
			}
			
		}else{
			$json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
			echo json_encode($json);
		}
		
		

	}
}

?>