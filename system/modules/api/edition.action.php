<?php

class edition extends SystemAction {


	public function ed(){
		$data=array();
		$code='';
		$msg='';
		$db = System::load_sys_class('model');
		$data = $db->GetOne("select banben,gengxinurl from `@#_edition` ");
		if($data) {
			$code = 200;
			$msg = "查询成功";
		}else {
			$code = 100;
			$msg = "数据为空";
		}
		$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
		echo json_encode($json);die;
	}

}

?>