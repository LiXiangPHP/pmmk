<?php

class dizhitj extends SystemAction {
	public function tj(){
		$uid = isset($_POST['uid']) ? $_POST['uid'] : null;
		$info = System::token_uid($uid);
		if ($info['code']==200) {
			$db = System::load_sys_class('model');
			$uid = $info['uid'];
			$sheng = isset($_POST['sheng']) ? $_POST['sheng'] : null;
			$shi = isset($_POST['shi']) ? $_POST['shi'] : null;
			$jiedao = isset($_POST['jiedao']) ? $_POST['jiedao'] : null;
			$youbian = isset($_POST['youbian']) ? $_POST['youbian'] : null;
			$shouhuoren = isset($_POST['shouhuoren']) ? $_POST['shouhuoren'] : null;
			$mobile = isset($_POST['mobile']) ? $_POST['mobile'] : null;
			$default = isset($_POST['default']) ? $_POST['default'] : null;
			if($uid&&$sheng&&$shi&&$jiedao&&$youbian&&$shouhuoren&&$mobile&&$default) {
				$code = 200;
				$msg = "添加成功";
				$data =  $db->Query("INSERT INTO `@#_member_dizhi` (`default`, `mobile`,`shouhuoren`,`youbian`,`jiedao`,`shi`,`sheng`,`uid`) VALUES ('$default','$mobile','$shouhuoren','$youbian','$jiedao','$shi','$sheng','$uid')");
			}else {
				$code = 100;
				$msg = "添加失败";
			}
			$json = array('code' => $code, 'msg' => $msg); 


			echo json_encode($json);
		}else{
			$json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
			echo json_encode($json);
		}
		
		
		

	}
}

?>