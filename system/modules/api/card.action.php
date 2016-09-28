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
					$v['reward'] = 0;//未打赏
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
					$data['reply'] = count($reply)."条新消息";//多人回复返回回复人数
				}else {
					foreach($reply as $v) {
						$user = $this->db->GetOne("select username from `@#_member` where uid = '$v[hueiyuan]'");
						$data['reply'] = $user['username']."回复了你";//单人回复返回会员昵称
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
					$v['reward'] = 0;
					unset($v['type']);
					unset($v['uid']);
					$data['data'][] = $v;
				}else {
					$user = $this->db->GetOne("select username from `@#_member` where uid = '$v[uid]'  LIMIT 1");
					$v['username'] = $user['username'];
					$v['identity'] = "user";
					$v['reward'] = 0;//未登录
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

	public function com($id,$pid,$uid) {//帖子id 父id 被评论者
		if($id && $pid && $uid) {
			$arr  = $this->db->GetList("select id,hueiyuan,neirong,time  from `@#_quanzi_tiezi` where tiezi = '$id' and pid = '$pid'");
			// print_r($arr);die;
			if($arr) {
				foreach($arr as $k => $v) {
					if($uid == '管理员') {
						$name['username'] = '管理员';
					}else {
						$name = $this->db->GetOne("select username from `@#_member` where uid = '$uid' limit 1");
					}
					$user = $this->db->GetOne("select username from `@#_member` where uid = '$v[hueiyuan]' limit 1"); 
					$arr[$k]['pname'] = $name['username'];
					$arr[$k]['name']  = $user['username'];
					$child = $this->com($id,$v['id'],$v['hueiyuan']);
					if(is_array($child)) {
						$arr = array_merge($arr,$child);
					}
					// if(is_array($child)) {
					// 	foreach($child as $val) {
					// 		$arr[] = $val;
					// 	}
					// }					
				}
				// print_r($arr);die;
				return $arr;
			}else {
				return 1;
			}
		}
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
			//评论状态均更改为已查看状态
			$rult = $this->db->Query("update `@#_quanzi_tiezi` set ifsee = 1 where tiezi = '$cardid'");
			if(!$rult) {
				$code = 300;
				$msg = "操作失败";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}
			//返回参数
			$Cdata = $this->db->GetOne("select * from `@#_quanzi_tiezi` where id = '$cardid' and tiezi = 0 and pid = 0");
			if(!$Cdata) {
				$code = 300;
				$msg = "操作失败";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}
			if($Cdata['hueiyuan']) {//会员发帖
				$user = $this->db->GetOne("select username from `@#_member` where uid = '$Cdata[hueiyuan]' limit 1");
				$data['data']['id'] = $Cdata['id'];
				$data['data']['title'] = $Cdata['title'];
				$data['data']['img'] = $Cdata['img'];
				$data['data']['time'] = $Cdata['time'];
				$data['data']['content'] = $Cdata['content'];
				$data['data']['author'] = $user['username'];
				$comments = array();
				$comt = $this->com($cardid,$cardid,$Cdata['hueiyuan']);//评论
				foreach($comt as $k => $val) {
					$comments[$k]['pname'] = $val['pname'];
					$comments[$k]['name']  = $val['name'];
					if($val['pname'] == $val['name']) {
						$comments[$k]['pname'] = '';
					}
					$comments[$k]['con']   = $val['neirong'];
					$comments[$k]['time']  = $val['time'];
				}
				// print_r($comments);die;
				$data['data']['comments'] = $comments;				
				// print_r($data);die;
			}else {//后台发帖
				$data['data']['id']= $Cdata['id'];
				$data['data']['title']= $Cdata['title'];
				$data['data']['img']= $Cdata['img'];
				$data['data']['content']= $Cdata['neirong'];
				$data['data']['author']= '管理员';
				$comments = array();
				$comt = $this->com($cardid,$cardid,'管理员');//评论
				foreach($comt as $k => $val) {
					$comments[$k]['pname'] = $val['pname'];
					$comments[$k]['name']  = $val['name'];
					$comments[$k]['con']   = $val['neirong'];
					$comments[$k]['time']  = $val['time'];
				}
				$data['data']['comments'] = $comments;
			}
			if($data) {
				$code = 200;
				$msg = "操作成功";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);
			}else {
				$code = 100;
				$msg = "操作失败";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);
			}
			// print_r($data);die;
		}else {
			$code = 300;
			$msg = "操作失败";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);
		}
	}

	//帖子回复
	public function json_creply() {
		$code = '';
		$msg  = '';
		$data = array();
		$cardid   = abs(intval($_POST['id']));//帖子id
		// $user     = $_POST['name'];//被评论者
		$times     = abs(intval($_POST['time']));
		$content  = $_POST['content'];//内容
		$time     = time();
		$token = trim($_POST['token']);//评论者
		$info = System::token_uid($token);
		if($info['code'] == 100) {
			$code = 300;
			$msg = "用户未登录";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);die;
		}
		if($cardid  && $content && $times) {
			// $uid = $this->db->GetOne("select uid from `@#_member` where username = '$user' limit 1");//被评论者
			$pid = $this->db->GetOne("select id from `@#_quanzi_tiezi` where  tiezi = '$cardid' and time = '$times' limit 1");
			// $pid = $this->db->GetOne("select id from `@#_quanzi_tiezi` where hueiyuan = '$uid[uid]' and tiezi = '$cardid' and time = '$times' limit 1");//pid
			$sql = "insert into `@#_quanzi_tiezi`(`hueiyuan`,`neirong`,`time`,`tiezi`,`pid`) values('$info[uid]','$content','$time','$cardid','$pid[id]')";
			// echo $sql;die;
			if($this->db->Query($sql)) {
				$code = 200;
				$msg = "回贴成功";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);
			}else {
				$code = 100;
				$msg = "回帖失败";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);
			}
		}else {
			$code = 300;
			$msg = "操作失败";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);		
		}

	}

	//帖子发起
	public function json_cpublic() {
		$code = '';
		$msg  = '';
		$data = array();
		$token = trim($_POST['token']);
		$info = System::token_uid($token);
		if($info['code'] == 200) {
			$title     = $_POST['title'];
			$content   = $_POST['content'];
			$img       = $_POST['img'];
			// $img       = '';
			$time      = time();
			$user      = $info['uid'];
			$qzid      = 1;
			// $img = 'data:image/png;base64,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';
			// $tmp  = base64_decode($img);
			if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img, $result)){
				$type = $result[2];
				$new_file = "./images/upload/img.{$type}";//图片存储路径
				if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $img)))){
					echo '新文件保存成功：', $new_file;
				}
			}

			if($title && $content) {
				$sql = "insert into `@#_quanzi_tiezi`(`qzid`,`hueiyuan`,`title`,`neirong`,`time`,`img`) values('$qzid','$user','$title','$content','$time','$img')";
				// echo $sql;die;
				if($this->db->Query($sql)) {
					$code = 200;
					$msg = "发帖成功";
					$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
					echo json_encode($json);
				}else {
					$code = 100;
					$msg = "发帖失败";
					$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
					echo json_encode($json);
				}
			}else {
				$code = 300;
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

	//打赏
	public function json_reward() {
		$code = '';
		$msg  = '';
		$data = array();
		$cardid  = abs(intval($_POST['id']));//帖子id 后台发帖不允许点赏
		$token = trim($_POST['token']);
		$info = System::token_uid($token);
		// print_r($info);die;
		if($info['code'] == 100) {
			$code = 300;
			$msg = "用户未登陆";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);die;
		}
		$Udata = $this->db->GetOne("select * from `@#_member` where uid = '$info[uid]' limit 1");//点赏会员信息
		if($cardid) {
			$Cdata = $this->db->GetOne("select * from `@#_quanzi_tiezi` where id = '$cardid' limit 1");//帖子信息
			if(!$Cdata['hueiyuan']) {
				$code = 100;
				$msg = "管理员发帖不许赏";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}

			if($Udata['score'] && $Udata['score'] >= 1) {
				$res  = $this->db->Query("update `@#_member` set score = score-1 where uid = '$info[uid]'");
			}else {
				$code = 100;
				$msg = "积分不足";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}

			$users = $this->db->GetOne("select username from `@#_member` where uid = '$Cdata[hueiyuan]' limit 1");
			// print_r($Udata);die;
			if($Udata['username'] == $users['username']) {
				$code = 100;
				$msg = "自己发帖不许赏";
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