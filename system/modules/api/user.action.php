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
	//注册接口
	//
	public function register()
	{

		$db = System::load_sys_class('model');	
		$name=$_POST['username'];
		$password=$_POST['password'];
		$verify=$_POST['verify'];
		$code = $_COOKIE['code'];
		if($verify != $code || !$verify)
		{
			$code = 100;
			$msg = '验证码错误';
			$json = array('code' => $code, 'msg' => $msg);
			echo json_encode($json);die;
		}
		$member=$db->GetOne("SELECT * FROM `@#_member` WHERE `mobile` = '$name' LIMIT 1");
		// print_r($member);die;
		if(is_array($member) && $member['mobile'] == $name){

				$code = 100;
				$msg = '该手机号已被注册';
				$json = array('code' => $code, 'msg' => $msg);
				echo json_encode($json);die;
			}

		$time=time();
		$userpassword=md5($password);
		$sql="INSERT INTO `@#_member`(username,mobile,password,img,emailcode,mobilecode,time)VALUES('$name','$name','$userpassword','photo/member.jpg','-1','1','$time')";
		$sqlreg = $db->Query($sql);
		if($sqlreg)
		{
			$code = 200;
			$msg = '注册成功';
			$data  = md5($name.$password.$time);
			$json = array('code' => $code, 'msg' => $msg ,'data'=>$data);
			echo json_encode($json);die;
		}
	}	
		
}

?>