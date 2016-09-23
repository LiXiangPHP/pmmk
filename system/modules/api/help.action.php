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
		$uid = isset($_POST['uid']) ? $_POST['uid'] : null;
		$info = System::token_uid($uid);
		if ($info['code']==200) {
			
			$db = System::load_sys_class('model');
			$issue = isset($_POST['issue']) ? $_POST['issue'] : null;
			$uid= $info['uid'];
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
			}else{
				$json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
				echo json_encode($json);
			}		
		}
		//帮助
		public function hp(){
			$db = System::load_sys_class('model');
			$type = isset($_POST['type']) ? $_POST['type'] : null;
			if ($type==1) {
			$uid = isset($_POST['uid']) ? $_POST['uid'] : null;
			$info = System::token_uid($uid);
			if ($info['code']==200) {
				$uid = $info['uid'];
				$data =  $db->GetList("select issue,id from `@#_help` where uid=$uid ");
				if($data) {
				$code = 200;
				$msg = "查询成功";
				}else {
				$code = 100;
				$msg = "数据为空";
				}
				$json = array('type' => $type, 'code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);
			}else{
				$json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
				echo json_encode($json);
			}
			
			}elseif($type==2){
				$data =  $db->GetList("select issue,id from `@#_help` where hot=1 ");
				if($data) {
				$code = 200;
				$msg = "查询成功";
				}else {
				$code = 100;
				$msg = "数据为空";
				}
				$json = array('type' => $type,'code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);
			}else{
				$code = 300;
				$msg = "没有传入正确的type";
				$json = array('code' => $code, 'msg' => $msg);
			}
			///////////////////////////////////////////////////////
		}
	}		

?>