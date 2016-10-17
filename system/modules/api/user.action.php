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
				$db->Query("UPDATE `@#_member` SET `user_ip` = '$user_ip',`login_time` = '$time', `token` = '$token' where `uid` = '$member[uid]'");
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
		// if($verify != $code)
		// {
		// 	$code = 100;
		// 	$msg = '验证码错误';
		// 	$json = array('code' => $code, 'msg' => $msg);
		// 	echo json_encode($json);die;
		// }
		if(!$verify)
		{
			$code = 100;
			$msg = '验证码不能为空';
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
			$data  = md5($name.$password.$time);
			$user_ip = _get_ip_dizhi();
			$db->Query("UPDATE `@#_member` SET `user_ip` = '$user_ip',`login_time` = '$time', `token` = '$data' where `username` = '$name'");

			$code = 200;
			$msg = '注册成功';
			
			$json = array('code' => $code, 'msg' => $msg ,'data'=>$data);
			echo json_encode($json);die;
		}
	}


	//个人信息
	public function json_person() {
		$code = '';
		$msg  = '';
		$data = array();
		$db = System::load_sys_class('model');
		$token = trim($_POST['token']);
		$info = System::token_uid($token);
		if($info['code'] == 200) {
			$name     = $_POST['name'];
			$sex   = $_POST['sex'];
			$img       = stripslashes($_POST['img']);//去掉船餐过程中的反斜杠
			$imgname   = 'member'.$info['uid'];
			$new_file  = '';
			$pic_path = 'images/upload/user';
			if(!file_exists($pic_path)) {
				if(!mkdir($pic_path, 0777)) {
					$code = 100;
					$msg = "目录创建失败";
					$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
					echo json_encode($json);die;
				}
				
			}
			if($img) {
				if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img, $result)){
					$type = $result[2];
					$new_file = "{$pic_path}/{$imgname}.{$type}";//图片存储路径
					if (!file_put_contents($new_file, base64_decode(str_replace($result[1], '', $img)))){
						$code = 100;
						$msg = "图片上传失败";
						$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
						echo json_encode($json);die;
					}
				}else {
					$tmp = base64_decode($img);
					$new_file = "{$pic_path}/{$imgname}.jpg";//图片存储路径
					if (!file_put_contents($new_file, $tmp)){
						$code = 100;
						$msg = "图片上传失败";
						$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
						echo json_encode($json);die;
					}	
				}
			}else {
				$code = 100;
				$msg = "修改失败";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}
			
			if($name && $sex) {
				$res = $db->Query("UPDATE `@#_member` SET `username` = '$name', `sex` = '$sex', `img` = '$new_file' where `uid` = '$info[uid]'");
				if($res) {
					$code = 200;
					$msg = "修改成功";
					$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
					echo json_encode($json);
				}else {
					$code = 100;
					$msg = "修改失败";
					$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
					echo json_encode($json);
				}
			}else {
				$code = 100;
				$msg = "操作失败";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);
			}
		}else {
			$code = 300;
			$msg = "用户未登录";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);
		}
	}

	//修改密码
	public function json_pwd() {
		$code = '';
		$msg  = '';
		$data = array();
		$db = System::load_sys_class('model');
		$token = trim($_POST['token']);
		$info = System::token_uid($token);
		if($info['code'] == 200) {
			$newpwd   = md5($_POST['newpassword']);
			$pwd = md5($_POST['password']);
			$user = $db->GetOne("select * from `@#_member` where `uid` = '$info[uid]' and `password` = '$pwd'");
			if(!$user) {
				$code = 100;
				$msg = "密码错误";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}
			if($newpwd) {				
				$res = $db->Query("update `@#_member` set `password` = '$newpwd' where `uid` = '$info[uid]'");
				if($res) {
					$code = 200;
					$msg = "修改成功";
					$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
					echo json_encode($json);die;
				}
			}else {
				$code = 100;
				$msg = "操作失败";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}
		}else {
			$code = 300;
			$msg = "用户未登录";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);
		}
	}

	//忘记密码
	public function json_reset() {
		$code = '';
		$msg  = '';
		$data = array();
		$db = System::load_sys_class('model');
		$mobile = $_POST['mobile'];
		$pwd = md5($_POST['password']);
		if($pwd && $mobile) {
			$res = $db->Query("update `@#_member` set `password` = '$pwd' where `mobile` = '$mobile'");
			if($res) {
				$code = 200;
				$msg = "修改成功";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);
			}else {
				$code = 100;
				$msg = "修改失败";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);
			}
		}else {
			$code = 100;
			$msg = "操作失败";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);
		}
	}	
		
}

?>