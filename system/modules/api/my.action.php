<?php

class my extends SystemAction {
	
	//我的接口需求：
	public function mm(){
		$token = isset($_POST['token']) ? $_POST['token'] : null;
		$info = System::token_uid($token);
		if ($info['code']==200) {
			$db = System::load_sys_class('model');
			$data =  $db->GetOne("select img,money,score from `@#_member` where uid='$info[uid]' ");
			if($data) {
				$code = 200;
				$msg = "查询成功";
			}else {
				$code = 400;
				$msg = "数据为空";
			}
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);
		}else{
			$json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
			echo json_encode($json);
		}
	}
	
	//基本信息
	public function mj(){
		$token = isset($_POST['token']) ? $_POST['token'] : null;
			$info = System::token_uid($token);
			if ($info['code']==200) {
				$db = System::load_sys_class('model');
				$data =  $db->GetOne("select img,username,sex from `@#_member` where uid='$info[uid]' ");
				if($data) {
					$code = 200;
					$msg = "查询成功";
				}else {
					$code = 400;
					$msg = "数据为空";
				}
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);
			}else{
				$json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
				echo json_encode($json);
			}

	}

	//我的帖子
	public function json_mycard() {
		$code = '';
		$msg  = '';
		$data = array();
		$token = trim($_POST['token']);
		$info = System::token_uid($token);
		if($info['code'] == 100) {
			$code = 300;
			$msg = "用户未登录";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);die;
		}
		$MCdata = $this->db->GetList("select id,title,neirong,img,time,reward,hueifu from `@#_quanzi_tiezi` where hueiyuan = '$info[uid]' and tiezi = 0 and pid = 0");
		foreach($MCdata as $k => $v) {
			$user = $this->db->GetList("select username from `@#_member` where uid in($v['reward'])");
			if($user) {
				$rewards = implode(',',$user);//点赏人昵称
			}else {
				$rewards = '';//未有人点赏
			}
			$total = $this->db->GetList("select count(*) from `@#_quanzi_tiezi` where tiezi = '$v[id]' and ");
			$data['data']['id'] = $v['id'];
			$data['data']['title'] = $v['title'];
			$data['data']['content'] = $v['neirong'];
			$data['data']['img'] = $v['img'];
			$data['data']['time'] = $v['time'];
			$data['data']['reward'] = $rewards;
		}
	}


}

?>