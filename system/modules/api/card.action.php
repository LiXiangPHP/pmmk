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
		$token = trim($_POST['token']);
		$info = System::token_uid($token);
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
		$Tdata = $this->db->GetPage("SELECT id,title,neirong content,hueifu total,hueiyuan uid,type,time,img,reward  FROM `@#_quanzi_tiezi` where  `qzid` = 1 and tiezi = 0 ",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));	
		if(!$Tdata) {
			$code = 100;
			$msg = "数据为空";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);die;
		}
		if($info['code'] == 200) {
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
					$rew = explode(',',$v['reward']);
					if(in_array($info['uid'],$rew)) {
						$v['reward'] = 1;//已打赏
					}else {
						$v['reward'] = 0;//未打赏
					}
					unset($v['type']);
					unset($v['uid']);
					$data['data'][] = $v;
				}
			}
			$anws = $this->db->GetList("select id from `@#_quanzi_tiezi` where qzid = 1 and hueiyuan = '$info[uid]' and tiezi != 0");
			$ids = '';
			foreach($anws as $v) {
				if($ids) {
					$ids .= ','.$v['id'];
				}else {
					$ids = $v['id'];
				}
			}
			$sql = "select * from `@#_quanzi_tiezi` where qzid = 1 and hueiyuan != '$info[uid]' and ifsee = 0 and and pid in ($ids)";
			$reply = $this->db->GetList("select * from `@#_quanzi_tiezi` where qzid = 1 and hueiyuan != '$info[uid]' and ifsee = 0 and  pid in($ids)");
			// print_r($reply);die;
			if($reply) {
				if(count($reply)>1) {
					$data['reply'] = count($reply);//多人回复返回回复人数
				}else {
					foreach($reply as $v) {
						$user = $this->db->GetOne("select username from `@#_member` where uid = '$v[hueiyuan]'");
						$data['reply'] = $user['username'];//单人回复返回会员昵称
					}
				}
			}else {
				$data['reply'] = "";//未有人回复则为空
			}
		}else {
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
					$v['reward'] = 2;//未登录
					unset($v['type']);
					unset($v['uid']);
					$data['data'][] = $v;
				}
			}
			$data['reply'] = "";//未登录为空
		}

		if($data['data']) {
			$data['ptotal'] = $yeshu;
		}
		// echo "<pre>";
		// print_r($data);die;	
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
		$cardid  = abs(intval($_POST['id']));//帖子id
		$token = trim($_POST['token']);
		$info = System::token_uid($token);
		if($info['code'] == 100) {
			$code = 300;
			$msg = "用户未登陆";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);die;
		}
		if($cardid) {
			$Cdata = $this->db->GetOne("select * from `@#_quanzi_tiezi` where id = '$cardid'");
			print_r($Cdata);die;
		}else {
			$code = 300;
			$msg = "操作失败";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);
		}
	}

	//帖子回复
	public function json_creply() {

	}

	//帖子回复
	public function json_cpublic() {

	}

	//打赏
	public function json_reward() {
		$code = '';
		$msg  = '';
		$data = array();
		$cardid  = abs(intval($_POST['id']));//帖子id 后台发帖不允许点赏
		$token = trim($_POST['token']);
		$info = System::token_uid($token);
		if($info['code'] == 100) {
			$code = 300;
			$msg = "用户未登陆";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);die;
		}
		$Udata = $this->db->GetOne("select * from `@#_member` where uid = '$info[uid]' limit 1");//点赏会员信息
		if($cardid) {
			$Cdata = $this->db->GetOne("select * from `@#_quanzi_tiezi` where id = '$cardid' limit 1");//帖子信息
			if($Udata['score'] && $Udata['score'] >= 1) {
				$res  = $this->db->Query("update `@#_member` set score = score-1 where uid = '$info[uid]'");
			}else {
				$code = 100;
				$msg = "积分不足";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}
			// echo "<pre>";
			// print_r($Cdata);die;
			if($Cdata['reward']) {
				$rew = explode(',',$Cdata['reward']);
				if(in_array($Udata['uid'],$rew)) {
					$ress = $this->db->Query("update `@#_member` set score = score + 1 where uid = '$Cdata[hueiyuan]'");
					$ret  = 1;

				}else {
					$reward = $Cdata['reward'].",".$Udata['uid'];
					$ress = $this->db->Query("update `@#_member` set score = score + 1 where uid = '$Cdata[hueiyuan]'");
					$ret  = $this->db->Query("update `@#_quanzi_tiezi` set reward = '$reward' where id = '$cardid'");
				}
			}else {
				$ress = $this->db->Query("update `@#_member` set score = score + 1 where uid = '$Cdata[hueiyuan]'");
				$ret  = $this->db->Query("update `@#_quanzi_tiezi` set reward = '$Udata[uid]' where id = '$cardid'");
			}
			
			if($res && $ress && $ret) {
				$code = 200;
				$msg = "点赏成功";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);
			}else {
				$code = 100;
				$msg = "点赏失败";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);
			}
			// echo "<pre>";
			// print_r($Udata);die;
		}else {
			$code = 300;
			$msg = "操作失败";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);
		}			
	}
}


?>