<?php

class about extends SystemAction {

	public function ab(){
		$db = System::load_sys_class('model');		
		$data = $db->GetOne("select count from `@#_about` order by id DESC LIMIT 1" );
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