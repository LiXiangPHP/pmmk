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
			foreach($rules as $k => $v) {
				$arr['num'] = $v['number'];
				$arr['score'] = $v['points'];
				$data['rules'][] = $arr; 
			}

			$arr = array();
			for($k = 0;$k <=9;$k++) {
				$arr[]['time'] = date('Y-m-d',strtotime('-'.$k.' days'));
			}
		
			$arrs = array();
			for($k=0;$k <= $member['sign_in_time'];$k++) {
				$arrs[] = date('Y-m-d',strtotime('-'.$k.' days',$member['sign_in_date']));
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
			$code = 300;
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
		# 每日签到增加福分
		$score = $this->db->GetOne("select points from `@#_signrules` where number = 1 limit 1");
		if($score) {
			$time_score = $score['points'];
		}else {
			$time_score = 0;
		}
		$member = $this->db->GetOne("select * from `@#_member` where uid = '$info[uid]' limit 1");
		$days = $this->db->GetList("select * from `@#_signrules` order by number asc");
		
		# 连续签到最大的天数
		$num = count($days);
		$max_day = $days[$num-1]['number']; 
		
		# 连续签到增加的福分（在后面查询替换）

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

		if ( date('Y-m-d',$member['sign_in_date']) == date('Y-m-d',strtotime('-1 day')) ){
			# 连续签到
			if ( $member['sign_in_time'] >= $max_day ) {//签到天数
				$member['sign_in_time'] = 0;
			}

			$sign_in_time = $member['sign_in_time'] + 1;
			$sign_in_time_all = $member['sign_in_time_all'] + 1;
			$sign_in_date = time();
			$score = $member['score'] + $time_score;
			// print_r($days);die;
			$big = false;
			for($k = 1;$k <= $num-2;$k++) {
				if ( $sign_in_time >= $days[$k]['number'] && $sign_in_time < $days[$k+1]['number']) {# 领取大礼包了
					$score += $days[$k]['points'];
					$big = true;
				} else if ( $k+1 == $num-1 && $sign_in_time == $max_day) {
					$score += $days[$k]['points'];
					$member['sign_in_time'] = 0;
					$big = true;
				}
			}
			// 积分明细记录
			// $this->db->Query("INSERT INTO `@#_member_account` (`uid`, `type`, `pay`, `content`, `money`, `time`) VALUES ('".$member['uid']."', '1', '福分', '每日签到', '$time_score', '".time()."')");
			$res = $this->db->Query("UPDATE `@#_member` SET score='".$score."',sign_in_time='".$sign_in_time."', sign_in_time_all='".$sign_in_time_all."', sign_in_date='".$sign_in_date."' where uid='".$member['uid']."'");
			if($res) {
				if ($big) {
					// $rult = $this->db->Query("INSERT INTO `@#_member_account` (`uid`, `type`, `pay`, `content`, `money`, `time`) VALUES ('".$member['uid']."', '1', '福分', '签到大礼包', '$time_day_score', '".time()."')");
					$code = 200;
					$msg = "签到成功领取礼包";
					$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
					echo json_encode($json);die;
				} else {
					$code = 200;
					$msg = "签到成功";
					$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
					echo json_encode($json);die;
				}
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
			$score = $member['score'] + $time_score;
			// $ress = $this->db->Query("INSERT INTO `@#_member_account` (`uid`, `type`, `pay`, `content`, `money`, `time`) VALUES ('".$member['uid']."', '1', '福分', '每日签到', '$time_score', '".time()."')");
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
			//积分转换夺宝币
			if ($type==1) {
				$zj = isset($_POST['zj']) ? $_POST['zj'] : null;
				$blarr = $db->GetOne("select scoredhb,duobaodhb  from `@#_proportionality` ");
				$bj = $blarr['scoredhb'];
				$bd = $blarr['duobaodhb'];
				$jbarr = $db->GetOne("select score,money  from `@#_member` where uid='$info[uid]' ");
				$j = $jbarr['score'];
				$b = $jbarr['money'];
				$zh = $bj/$bd;
				$zb = $zj/$zh;
				if ($zb&&$zj) {
					$nj = $j-$zj;
					$nb = $b+$zb;
					$time = time();
					$uid = $info[uid];
					$type1 = 1;
					$type2 = '-1';
					$pay1 = "福分";
					$pay2 = "账户";
					$content = "积分兑换夺宝币";
					$data = $db->Query("update `@#_member` set score=$nj,money=$nb where uid='$info[uid]'") ;
					$jfgb = $db->Query("INSERT INTO `@#_member_account` (`uid`,`type`,`pay`,`content`,`money`,`time`) VALUES ('$uid','$type2','$pay1','$content','$zj','$time')");
					$dbgb = $db->Query("INSERT INTO `@#_member_account` (`uid`,`type`,`pay`,`content`,`money`,`time`) VALUES ('$uid','$type1','$pay2','$content','$zb','$time')");
					$code = 200;
					$msg = "兑换成功";
				}else {
					$code = 400;
					$msg = "兑换失败";
				}
				$json = array('code' => $code, 'msg' => $msg);
				echo json_encode($json);
			}
			//夺宝币转换积分
			if ($type==2) {
				$zb = isset($_POST['zb']) ? $_POST['zb'] : null;
				$blarr = $db->GetOne("select scoredhb,duobaodhb  from `@#_proportionality` ");
				$bj = $blarr['scoredhb'];
				$bd = $blarr['duobaodhb'];
				$jbarr = $db->GetOne("select score,money  from `@#_member` where uid='$info[uid]' ");
				$j = $jbarr['score'];
				$b = $jbarr['money'];
				$zh = $bj/$bd;
				$zj = $zb*$zh;
				if ($zb&&$zj) {
					$nj = $j+$zj;
					$nb = $b-$zb;
					$time = time();
					$uid = $info[uid];
					$type1 = 1;
					$type2 = '-1';
					$pay1 = "福分";
					$pay2 = "账户";
					$content = "夺宝币兑换积分";
					$data = $db->Query("update `@#_member` set score=$nj,money=$nb where uid='$info[uid]'") ;
					$jfgb = $db->Query("INSERT INTO `@#_member_account` (`uid`,`type`,`pay`,`content`,`money`,`time`) VALUES ('$uid','$type1','$pay1','$content','$zj','$time')");
					$dbgb = $db->Query("INSERT INTO `@#_member_account` (`uid`,`type`,`pay`,`content`,`money`,`time`) VALUES ('$uid','$type2','pay2','$content','$zb','$time')");
					$code = 200;
					$msg = "添加成功";
				}else {
					$code = 400;
					$msg = "添加失败";
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
				$num = 10;
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
				$data = $db->GetPage("select type,content,money,time from `@#_member_account` where uid='$info[uid]' and pay like '%福分%' ",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
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
				$total = $db->GetCount("select * from `@#_member_account` where uid='$info[uid]' and pay like '%账户%'  ");	
				$num = 10;
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
				$data = $db->GetPage("select type,content,money,time from `@#_member_account` where uid='$info[uid]' and pay like '%账户%' ",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
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

}

?>