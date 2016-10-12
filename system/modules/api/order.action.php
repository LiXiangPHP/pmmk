<?php

class order extends SystemAction {
	//中奖收货
	public function od(){
		$token = isset($_POST['token']) ? $_POST['token'] : null;
		$info = System::token_uid($token);
		$type = isset($_POST['type']) ? $_POST['type'] : null;
		if ($info['code']==200) {
			//未发货
			if ($type==1) {
				$db = System::load_sys_class('model');
				$data = $db->GetList("select b.q_end_time,a.shopname,a.shopqishu,b.thumb from `@#_member_go_record` as a,`@#_shoplist` as b where a.shopid=b.id and  a.uid='$info[uid]' and a.huode>10000000 and status like '%未发货%' ");
				foreach($data as $k=>$v) {
				$data[$k]['thumb'] = "gangmaduobao.com/statics/uploads/".$v['thumb'];
				}
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
			//已发货，待收货
			if ($type==2) {
				$db = System::load_sys_class('model');
				$data = $db->GetList("select b.q_end_time,a.shopname,a.shopqishu,a.id,b.thumb from `@#_member_go_record` as a,`@#_shoplist` as b where a.shopid=b.id and  a.uid='$info[uid]' and a.huode>10000000 and status like '%已发货,待收货%' ");
				foreach($data as $k=>$v) {
				$data[$k]['thumb'] = "gangmaduobao.com/statics/uploads/".$v['thumb'];
				}
				if($data) {
					$code = 200;
					$msg = "查询成功";
				}else {
					$code = 400;
					$msg = "数据为空";
				}
				$json = array('code' => $code, 'msg' => $msg,  'data' => $data);
				echo json_encode($json);
			}
			//已收货
			if ($type==3) {
				$db = System::load_sys_class('model');
				$data = $db->GetList("select b.q_end_time,a.shopname,a.shopqishu,a.id,b.thumb from `@#_member_go_record` as a,`@#_shoplist` as b where a.shopid=b.id and  a.uid='$info[uid]' and a.huode>10000000 and status like '%已发货,已完成%' ");
				foreach($data as $k=>$v) {
				$data[$k]['thumb'] = "gangmaduobao.com/statics/uploads/".$v['thumb'];
				}
				if($data) {
					$code = 200;
					$msg = "查询成功";
				}else {
					$code = 400;
					$msg = "数据为空";
				}
				$json = array('code' => $code, 'msg' => $msg,  'data' => $data);
				echo json_encode($json);
			}
			
		}else{
			$json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
			echo json_encode($json);
		}
		
		

	}
	//确认收货接口
	public function qr(){
		$db = System::load_sys_class('model');
		$info = System::token_uid($token);
		$id = isset($_POST['id']) ? $_POST['id'] : null;
		$token = isset($_POST['token']) ? $_POST['token'] : null;
		$data = $db->GetOne("select * from `@#_member_go_record` where id=$id  ");
		if($data) {
					$code = 200;
					$msg = "成功";
					$data = $db->Query("update `@#_member_go_record` set status='已付款,已发货,已完成' where id=$id ") ;
				}else {
					$code = 400;
					$msg = "失败";
				}
				$json = array('code' => $code, 'msg' => $msg );
				echo json_encode($json);
	
	}
}

?>