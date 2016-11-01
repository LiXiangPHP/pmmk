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
			$data['img'] = "gangmaduobao.com/".$data['img'];
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
				$data['img'] = "gangmaduobao.com/".$data['img'];
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
		// $pagenum = abs(intval($_POST['p']));
		$token = trim($_POST['token']);
		$info = System::token_uid($token);
		if($info['code'] == 100) {
			$code = 300;
			$msg = "用户未登录";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);die;
		}

		$MCdata = $this->db->GetList("select id,title,neirong content,img,reward,hueifu comment,time,shenhe from `@#_quanzi_tiezi` where hueiyuan = '$info[uid]' and tiezi = 0 and pid = 0 order by time desc");	
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
			$v['img']   = 'gangmaduobao.com/'.$v['img'];
			if($v['shenhe'] == 'Y') {
				unset($v['shenhe']);
				$data['pass'][] = $v;
			}else {
				unset($v['shenhe']);
				$data['ping'][] = $v;
			}
			
		}

		if(!$data['pass']) {
			$data['pass'] = '';
		}
		if(!$data['ping']) {
			$data['ping'] = '';
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

	
	//每日签到
	public function json_signday() {
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
		$member = $this->db->GetOne("select * from `@#_member` where uid = '$info[uid]' limit 1");
		$rules  = $this->db->GetList("select * from `@#_signrules` ");
		if($rules && $member) {
			$data['time'] = $member['sign_in_time'];
			$string = '';
			foreach($rules as $v) {
				if($string) {
					$string .= '连续签到'.$v['number'].'天以上，可获'.$v['points'].'积分';
				}else {
					$string = '连续签到'.$v['number'].'天以上，可获'.$v['points'].'积分';
				}
			}
			$data['rules'] = $string;
			$arr = array();
			for($k = 0;$k <=9;$k++) {
				$arr[]['time'] = date('Y-m-d',strtotime('-'.$k.' days'));
			}
			
			$arr = array_reverse($arr);
			$arrs = array();
			for($k=0;$k <= $member['sign_in_time'];$k++) {
				if($k == $member['sign_in_time']) {
					$arrs[] = date('Y-m-d',$member['sign_in_date']);
				}else {
					$arrs[] = date('Y-m-d',strtotime('-'.$k.' days',$member['sign_in_date']));
				}
			}

			foreach($arr as $k => $val) {
				if(in_array($val['time'], $arrs)) {
					$val['status'] = '已签到';
				}else {
					$val['status'] = '未签到';
				}
				$data['days'][] = $val; 
			}
			
			if($data) {
				$code = 200;
				$msg = "查询成功";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}else {
				$code = 100;
				$msg = "操作失败";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}
		}else {
			$code = 100;
			$msg = "操作失败";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);die;
		}
	}

	//签到
	public function json_sign() {
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

		# 签到时间限制（不能夸天哦。。）
		$time_start = '00:01';
		$time_stop= '23:59';
		
		$member = $this->db->GetOne("select * from `@#_member` where uid = '$info[uid]' limit 1");
		$days = $this->db->GetList("select * from `@#_signrules` order by number asc");		
		$num = count($days);
		$max_day = $days[$num-1]['number'];# 连续签到最大的天数
		$max_point = $days[$num-1]['points'];

		if ( !$member['mobile'] || $member['mobilecode']!='1' ) {
			$code = 100;
			$msg = "用户手机未验证不可签到";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);die;
		}

		if ( date('Y-m-d',$member['sign_in_date']) == date('Y-m-d') ) {
			$code = 100;
			$msg = "已签到";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);die;
		}

		if ( strtotime(date('Y-m-d').$time_start ) > time() || strtotime(date('Y-m-d').$time_stop ) < time() ) {
			$code = 100;
			$msg = "超出签到时间";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);die;
		}

		# 连续签到
		if ( date('Y-m-d',$member['sign_in_date']) == date('Y-m-d',strtotime('-1 day')) ){
			
			$sign_in_time = $member['sign_in_time'] + 1;
			$sign_in_time_all = $member['sign_in_time_all'] + 1;
			$sign_in_date = time();
			
			if ( $sign_in_time >= $max_day ) {# 领取签到积分
				$score = $member['score'] + $max_point;
				$money = $max_point;
			}else {
				for($k = 1;$k <= $num-1;$k++) {
					if ( $sign_in_time >= $days[$k]['number'] && $sign_in_time < $days[$k+1]['number']) {
						$score = $member['score'] + $days[$k]['points'];
						$money = $days[$k]['points'];
					}
				}
			}

			// 积分明细记录
			$this->db->Query("INSERT INTO `@#_member_account` (`uid`, `type`, `pay`, `content`, `money`, `time`) VALUES ('".$member['uid']."', '1', '福分', '每日签到', '$money', '".time()."')");
			
			$res = $this->db->Query("UPDATE `@#_member` SET score='".$score."',sign_in_time='".$sign_in_time."', sign_in_time_all='".$sign_in_time_all."', sign_in_date='".$sign_in_date."' where uid='".$member['uid']."'");
			if($res) {
				$code = 200;
				$msg = "签到成功";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}else {
				$code = 100;
				$msg = "签到失败";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}
		} else {
			//签到不连续
			$sign_in_time = 1;
			$sign_in_time_all = $member['sign_in_time_all'] + 1;
			$sign_in_date = time();
			$score = $member['score'] + $days[0]['points'];
			$money = $days[0]['points'];
			//积分消费明细
			$ress = $this->db->Query("INSERT INTO `@#_member_account` (`uid`, `type`, `pay`, `content`, `money`, `time`) VALUES ('".$member['uid']."', '1', '福分', '每日签到', '$money', '".time()."')");
			$res = $this->db->Query("UPDATE `@#_member` SET score='".$score."',sign_in_time='".$sign_in_time."', sign_in_time_all='".$sign_in_time_all."', sign_in_date='".$sign_in_date."' where uid='".$member['uid']."'");
			if($res) {
				$code = 200;
				$msg = "签到成功";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}else {
				$code = 100;
				$msg = "签到失败";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}
			
		}
	}

	//积分兑换展示
	public function dhzs() {
		$db = System::load_sys_class('model');
			$data = $db->GetOne("select scoredhb,duobaodhb  from `@#_proportionality` ");
			if($data) {
					$code = 200;
					$msg = "查询成功";
				}else {
					$code = 400;
					$msg = "数据为空";
				}
				$json = array('code' => $code, 'msg' => $msg,  'data' => $data);
				echo json_encode($json);		
	}

	//积分确认
	public function jfqr() {
		$db = System::load_sys_class('model');
		$token = isset($_POST['token']) ? $_POST['token'] : null;
		$info = System::token_uid($token);
		$type = isset($_POST['type']) ? $_POST['type'] : null;
		if ($info['code']==200) {
			if ($type==1) {
				$zj = isset($_POST['zj']) ? $_POST['zj'] : null;
				$blarr = $db->GetOne("select scoredhb,duobaodhb  from `@#_proportionality` ");
				$bj = $blarr['scoredhb'];
				$bd = $blarr['duobaodhb'];
				$jbarr = $db->GetOne("select score,money  from `@#_member` where uid='$info[uid]' ");
				$j = $jbarr['score'];
				$b = $jbarr['money'];
				if ($jbarr['score']=>$zj) {
					$zh = $bj/$bd;
					$zb = $zj/$zh;
					if ($zb) {
						$nj = $j-$zj;
						$nb = $b+$zb;
						$data = $db->Query("update `@#_member` set score=$nj,money=$nb where uid='$info[uid]'") ;
						$code = 200;
						$msg = "添加成功";
						$db->Query("INSERT INTO `@#_member_account` (`uid`, `type`, `pay`, `content`, `money`, `time`) VALUES ('".$info['uid']."', '-1', '福分', '积分兑换夺宝币', '$zj', '".time()."')");
						$db->Query("INSERT INTO `@#_member_account` (`uid`, `type`, `pay`, `content`, `money`, `time`) VALUES ('".$info['uid']."', '2', '账户', '积分兑换夺宝币', '$zb', '".time()."')");
					}else {
						$code = 400;
						$msg = "添加失败";
					}					
				}else{
					$code = 500;
					$msg = "积分不够";
				}
				$json = array('code' => $code, 'msg' => $msg);
				echo json_encode($json);
				
			}
			if ($type==2) {
				$zb = isset($_POST['zb']) ? $_POST['zb'] : null;
				$blarr = $db->GetOne("select scoredhb,duobaodhb  from `@#_proportionality` ");
				$bj = $blarr['scoredhb'];
				$bd = $blarr['duobaodhb'];
				$jbarr = $db->GetOne("select score,money  from `@#_member` where uid='$info[uid]' ");
				$j = $jbarr['score'];
				$b = $jbarr['money'];
				if ($jbarr['money']=>$zb) {
					$zh = $bj/$bd;
					$zj = $zb*$zh;
					if ($zb) {
						$nj = $j+$zj;
						$nb = $b-$zb;
						$data = $db->Query("update `@#_member` set score=$nj,money=$nb where uid='$info[uid]'") ;
						$code = 200;
						$msg = "添加成功";
						$db->Query("INSERT INTO `@#_member_account` (`uid`, `type`, `pay`, `content`, `money`, `time`) VALUES ('".$info['uid']."', '1', '福分', '夺宝币兑换积分', '$zj', '".time()."')");
						$db->Query("INSERT INTO `@#_member_account` (`uid`, `type`, `pay`, `content`, `money`, `time`) VALUES ('".$info['uid']."', '-1', '账户', '夺宝币兑换积分', '$zb', '".time()."')");
					}else {
						$code = 400;
						$msg = "添加失败";
					}
				}else{
					$code = 500;
					$msg = "夺宝币不够";
				}				
				$json = array('code' => $code, 'msg' => $msg);
				echo json_encode($json);
			}
		}else{
			$json = array('code' => 300, 'msg' => '请登录');
			echo json_encode($json);
		}
			
	}
	//消费记录
	public function xfjl() {
		$db = System::load_sys_class('model');
		$token = isset($_POST['token']) ? $_POST['token'] : null;
		$info = System::token_uid($token);
		$type = isset($_POST['type']) ? $_POST['type'] : null;
		if ($info['code']==200) {
			//积分纪录
			if ($type==1) {	
				$pagenum = isset($_POST['pagenum']) ? $_POST['pagenum'] : null;
				if(empty($pagenum)) {
				$pagenum=1;
				}
				$total = $db->GetCount("select * from `@#_member_account` where uid='$info[uid]' and pay like '%福分%'  ");	
				$num = 12;
				$yushu = $total%$num;
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
				$data = $db->GetPage("select type,content,money,time from `@#_member_account` where uid='$info[uid]' and pay like '%福分%' order by time desc ",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
				if($data) {
					$code = 200;
					$msg = "查询成功";
				}else {
					$code = 400;
					$msg = "数据为空";
				}
				$json = array('code' => $code, 'msg' => $msg,'ptotal'=> $yeshu, 'data' => $data);
				echo json_encode($json);			
			}
			//夺宝币记录
			if ($type==2) {	
				$pagenum = isset($_POST['pagenum']) ? $_POST['pagenum'] : null;
				if(empty($pagenum)) {
				$pagenum=1;
				}
				$total = $db->GetCount("select * from `@#_member_account` where uid='$info[uid]' and pay like '%福分%'  ");	
				$num = 12;
				$yushu = $total%$num;
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
				$data = $db->GetPage("select type,content,money,time from `@#_member_account` where uid='$info[uid]' and pay like '%账户%' order by time desc ",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
				if($data) {
					$code = 200;
					$msg = "查询成功";
				}else {
					$code = 400;
					$msg = "数据为空";
				}
				$json = array('code' => $code, 'msg' => $msg,'ptotal'=> $yeshu, 'data' => $data);
				echo json_encode($json);			
			}


		}else{
				$json = array('code' => 300, 'msg' => '请登录');
				echo json_encode($json);
		}
		
			
				
	}
	//晒单
	public function shaidan()
	{	
		$data = array();
		$db = System::load_sys_class('model');
		$token = isset($_POST['token']) ? $_POST['token'] : "kong";
		$info = System::token_uid($token);
		if($info['code'] == 100) {
			$code = 300;
			$msg = "用户未登陆";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);die;
		}
		$shaidan=$db->Getlist("select sd_id,sd_userid,sd_shopid,sd_content,sd_photolist,sd_ping,sd_time from `@#_shaidan` where `sd_userid`='$info[uid]' order by `sd_id`");

		foreach ($shaidan as $k => $v) {
			$aa = $db->GetOne("select title from `@#_shoplist` where `id`='$v[sd_shopid]' ");			
			$shaidan[$k]['sd_shopid'] = strip_tags($aa['title']);
			$shaidan[$k]['sd_content'] = strip_tags($v['sd_content']);
			// $shaidan[$k]['sd_photolist'] = array_filter(explode(';',$v['sd_photolist']));
			$arr = array_filter(explode(',',$v['sd_photolist']));
			foreach ($arr as $key => $value) {
				$arr[$key] = 'gangmaduobao.com/'.$value;
			}
			$shaidan[$k]['sd_photolist'] =  $arr;
			$data['data'][] = $shaidan[$k];
		}
		if($shaidan) {
			$uu = $db->GetOne("select username,img from `@#_member` where `uid`='$info[uid]' ");	
			$data['user'] = strip_tags($uu['username']);
			$data['userimg'] = "gangmaduobao.com/".strip_tags($uu['img']);
		}else {
			$code = 100;
			echo json_encode(array("code"=>$code,"data"=>$data)); die;
		}
		
		// print_R($shaidan);die;
		$code = 200;
		echo json_encode(array("code"=>$code,"data"=>$data));
	}

}

?>