<?php

class user extends SystemAction {

	//登陆接口
	public function login(){
		$username=$_POST['username'];
		$password=md5($_POST['password']);
		$db = System::load_sys_class('model');	
		// echo "select * from `@#_member` where `mobile`='$username' and `password`='$password'";die;
		$member=$db->GetOne("select * from `@#_member` where `mobile`='$username' and `password`='$password'");
		if(!$member){
				$code = 100;
				$msg = "帐号不存在错误!";
			}		
			if(!is_array($member)){
				$code = 100;
				$msg = "帐号或密码错误!";
			}else{
				$time = time();
				$user_ip = _get_ip_dizhi();
				$token  = md5($username.$password.$time);
				$db->GetOne("UPDATE `@#_member` SET `user_ip` = '$user_ip',`login_time` = '$time', `token` = '$token' where `uid` = '$member[uid]'");
				$code = 200;
				$msg = "";
				$data = $token;
			
			}

			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);
				
		}	
		
}

?>