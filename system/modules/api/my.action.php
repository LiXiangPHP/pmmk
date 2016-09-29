<?php
defined('G_IN_SYSTEM')or exit("no");
class my extends SystemAction {
	
	//json获取获取栏目
	public function __construct(){		
		$this->db=System::load_sys_class('model');
	}


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
		$pagenum = abs(intval($_POST['p']));
		$token = trim($_POST['token']);
		$info = System::token_uid($token);
		if($info['code'] == 100) {
			$code = 300;
			$msg = "用户未登录";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);die;
		}
		$total = $this->db->GetCount("select * from `@#_quanzi_tiezi` where hueiyuan = '$info[uid]' and tiezi = 0 and pid = 0");
		$num = 10;
		$yushu=$total%$num;
		if($yushu > 0) {
			$yeshu=floor($total/$num)+1;
		}else {
			$yeshu=floor($total/$num);
		}
		if($pagenum >= $yeshu) {
			$pagenum = $yeshu;
		}
		$page=System::load_sys_class('page');
		$page->config($total,$num,$pagenum,"0");
		$MCdata = $this->db->GetPage("select id,title,neirong content,img,reward,hueifu comment,time from `@#_quanzi_tiezi` where hueiyuan = '$info[uid]' and tiezi = 0 and pid = 0",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));	
		// print_r($MCdata);die;
		foreach($MCdata as $v) {
			$user = $this->db->GetList("select username from `@#_member` where uid in($v[reward])");
			if($user) {
				foreach($user as $val) {
					if($rewards) {
						$rewards .= ','.$val['username'];//点赏人昵称
					}else {
						$rewards = $val['username'];
					}
				}
			}else {
				$rewards = '';//未有人点赏
			}
			$v['reward'] = $rewards;
			$v['img']   .= 'gangmaduobao.com/'.$v['img'];
			$data['data'][] = $v;
		}
		if($data['data']) {
			$data['ptotal'] = $yeshu;
		}
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