<?php

defined('G_IN_SYSTEM')or exit("no");

class card extends SystemAction {
	//json获取获取栏目
	public function __construct(){		
		$this->db=System::load_sys_class('model');
	}

	//获取帖子列表
	public function json_cardlist() {
		$code = '';
		$msg  = '';
		$data = array();
		$pagenum = abs(intval($_POST['p']));
		if(empty($pagenum)) {
			$pagenum=1;
		}
		$total = $this->db->GetCount("select * from `@#_quanzi_tiezi` where `qzid` = 1 and tiezi = 0");
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
		$Tdata = $this->db->GetPage("SELECT id,title,neirong content,hueifu total,hueiyuan uid,type,time,img  FROM `@#_quanzi_tiezi` where  `qzid` = 1 and tiezi = 0 ",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
		foreach($Tdata as $v) {
			if($v['type'] == 1) {
				$v['username'] = "管理员";
				$v['identity'] = "admin";
				unset($v['type']);
				unset($v['uid']);
				$data['data'][] = $v;
			}else {
				$user = $this->db->GetOne("select username from `@#_member` where uid = '$v[uid]'  LIMIT 1");
				$v['username'] = $user['username'];
				$v['identity'] = "user";
				unset($v['type']);
				unset($v['uid']);
				$data['data'][] = $v;
			}
			
		}
		if($data['data']) {
			$data['ptotal'] = $yeshu;
		}
		// echo "<pre>";
		// print_r($Tdata);die;	
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

	//获取帖子详情
	public function json_cdetail() {
		$code = '';
		$msg  = '';
		$data = array();
		$cid  = abs(intval($_POST['id']));

	}

	//帖子回复
	public function json_creply() {

	}

	//帖子回复
	public function json_cpublic() {

	}

	//打赏
	public function json_reward() {
		
	}
}


?>