<?php

defined('G_IN_SYSTEM')or exit("no");

class card extends SystemAction {
	//json获取获取栏目
	public function __construct(){		
		$this->db=System::load_sys_class('model');
	}

	public function bubble_sort($array) { 
		$count = count($array); 
		if ($count <= 0) return false; 
		for($i=0; $i<$count; $i++) { 
			for($j=$count-1; $j>$i; $j--) { 
    			if ($array[$j]['ltime'] > $array[$j-1]['ltime']) { 
     				$tmp = $array[$j];
    				$array[$j] = $array[$j-1];
    				$array[$j-1] = $tmp; 
    			} 
    		}
    	}
		return $array; 
	}

	public function bubble_sorts($array) { 
		$count = count($array); 
		if ($count <= 0) return false; 
		for($i=0; $i<$count; $i++) { 
			for($j=$count-1; $j>$i; $j--) { 
    			if ($array[$j]['time'] > $array[$j-1]['time']) { 
     				$tmp = $array[$j];
    				$array[$j] = $array[$j-1];
    				$array[$j-1] = $tmp; 
    			} 
    		}
    	}
		return $array; 
	}

	public function bubble_sortm($array) { 
		$count = count($array); 
		if ($count <= 0) return false; 
		for($i=0; $i<$count; $i++) { 
			for($j=$count-1; $j>$i; $j--) { 
    			if ($array[$j]['time'] < $array[$j-1]['time']) { 
     				$tmp = $array[$j];
    				$array[$j] = $array[$j-1];
    				$array[$j-1] = $tmp; 
    			} 
    		}
    	}
		return $array; 
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
		$total = $this->db->GetCount("select * from `@#_quanzi_tiezi` where `qzid` = 1 and tiezi = 0 and shenhe = 'Y'");
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
		$Tdata = $this->db->GetPage("SELECT id,title,neirong content,hueifu total,hueiyuan uid,type,time,img,reward  FROM `@#_quanzi_tiezi` where  `qzid` = 1 and tiezi = 0 and shenhe = 'Y'  order by time desc",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));	
		if(!$Tdata) {
			$code = 100;
			$msg = "数据为空";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);die;
		}
		foreach($Tdata as $k => $v) {
			$ltime = $this->db->GetOne("SELECT MAX(`time`) times FROM `@#_quanzi_tiezi` WHERE `tiezi` = '$v[id]'");
			if($ltime[times]) {
				$Tdata[$k]['ltime'] = $ltime['times'];	
			}else {
				$Tdata[$k]['ltime'] = $v['time'];
			}
				
		}
		
		$Tdata = $this->bubble_sort($Tdata);
		// print_r($Tdata);die;
		foreach($Tdata as $k => $v) {
			if($v['ltime']) {
				unset($Tdata[$k]['ltime']);
			}
		}

		// print_r($Tdata);die;


		if($info['code'] == 200) {
			foreach($Tdata as $v) {
				if($v['type'] == 1) {
					$v['username'] = "管理员";
					$v['identity'] = "admin";
					$v['reward'] = 0;//未打赏
					// $arr = explode(',',$v['img']);
					// $arrs = '';
					// foreach($arr as $key => $vall) {
					// 	if($vall) {
					// 		$arrs[] = 'gangmaduobao.com/'.$vall;
					// 	}						
					// }
					// $v['img'] = $arrs;
					$v['img'] = 'gangmaduobao.com/'.$v['img'];
					unset($v['type']);
					unset($v['uid']);
					$data['data'][] = $v;
				}else {
					$user = $this->db->GetOne("select username from `@#_member` where uid = '$v[uid]'  LIMIT 1");
					$v['username'] = $user['username'];
					$v['identity'] = "user";
					// $arr = explode(',',$v['img']);
					// $arrs = '';
					// foreach($arr as $key => $vall) {
					// 	if($vall) {
					// 		$arrs[] = 'gangmaduobao.com/'.$vall;
					// 	}						
					// }
					// $v['img'] = $arrs;
					$v['img'] = 'gangmaduobao.com/'.$v['img'];
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

			$idarr = $this->db->GetList("select id from `@#_quanzi_tiezi` where hueiyuan = '$info[uid]' and tiezi != 0");
			$idss = '';
			foreach ($idarr as $key => $value) {
				if($idss) {
					$idss .= ','.$value['id'];
				}else {
					$idss = $value['id'];
				}
			}
			$rul = $this->db->GetList("select * from `@#_quanzi_tiezi` where pid in($idss) and ifsee = 0 order by time desc");
			// print_r($rul);die;
			$lid = $this->db->GetList("select id from `@#_quanzi_tiezi` where hueiyuan = '$info[uid]' and tiezi = 0");
			$ids = '';
			foreach ($lid as $key => $value) {
				if($ids) {
					$ids .= ','.$value['id'];
				}else {
					$ids = $value['id'];
				}
			}
			$anws = $this->db->GetList("SELECT * FROM `@#_quanzi_tiezi` WHERE tiezi IN($ids)  AND ifsees = 0  order by time desc");
			foreach ($anws as $key => $vl) {
				if($vl['hueiyuan'] == '$info[uid]' && $vl['pid'] == $vl['tiezi']) {
					unset($anws[$key]);
				}
			}
			// print_r($anws);die;
			if($rul) {
				foreach ($rul as $vl) {
					$anws[] = $vl;
				}
			}
			// print_r($anws);die;
			
			if($anws) {
				$keys = array();
				for ($i=0; $i<count($anws); $i++) {
					for ($j=$i+1; $j<count($anws); $j++) {
						if($anws[$i]['id'] == $anws[$j]['id']) {
							$keys[] = $j; 
						}
					}
				}
				foreach ($keys as $v) {
					unset($anws[$v]);
				}
				// print_r($anws);die;
				if(count($anws)>1) {
					$data['reply'] = count($anws)."条新消息";//多人回复返回回复人数
				}else {
					foreach($anws as $v) {
						$user = $this->db->GetOne("select username from `@#_member` where uid = '$v[hueiyuan]'");
						if($v['hueiyuan'] == $info['uid']) {
							$data['reply'] = '';
						}
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
					$v['reward'] = 0;//未登录
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

	public function com($id,$pid,$uid) {//帖子id 父id 被评论者====递归函数，获得相关评论
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
					if($uid == $v['hueiyuan']) {
						$arr[$k]['pname'] = '';	
					}
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
			//评论状态更改
			$uid =  $this->db->GetOne("select hueiyuan from `@#_quanzi_tiezi` where id = '$cardid'");
			if($uid['hueiyuan'] == $info['uid']) {
				$rult = $this->db->Query("update `@#_quanzi_tiezi` set ifsees = 1,dianji = dianji + 1  where tiezi = '$cardid' or id = '$cardid'");
				if(!$rult) {
					$code = 100;
					$msg = "操作失败";
					$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
					echo json_encode($json);die;
				}
			}else {
				$arr = $this->db->GetList("select id from `@#_quanzi_tiezi` where tiezi = '$cardid' and hueiyuan = '$info[uid]'");
				$ids = '';
				foreach($arr as $v) {
					if($ids) {
						$ids .= ','.$v['id'];
					}else {
						$ids = $v['id'];
					}
				}
				if($ids) {
					$rults = $this->db->Query("update `@#_quanzi_tiezi` set ifsee = 1,dianji = dianji + 1  where tiezi = '$cardid' and pid in($ids)");
					if(!$rults) {
						$code = 100;
						$msg = "操作失败";
						$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
						echo json_encode($json);die;
					}
				}
			}
			// $idss = $this->db->GetList("select distinct hueiyuan from `@#_quanzi_tiezi` where tiezi = '$cardid'");
			// $ids = '';
			// foreach($idss as $v) {
			// 	if($ids) {
			// 		$ids .= ','.$v['hueiyuan'];
			// 	}else {
			// 		$ids = $v['hueiyuan'];
			// 	}
			// }
			
			//返回参数
			$Cdata = $this->db->GetOne("select * from `@#_quanzi_tiezi` where id = '$cardid' and tiezi = 0 and pid = 0 and shenhe = 'Y'");
			if(!$Cdata) {
				$code = 100;
				$msg = "操作失败";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}
			if($Cdata['hueiyuan'] != '管理员') {//会员发帖
				$user = $this->db->GetOne("select username from `@#_member` where uid = '$Cdata[hueiyuan]' limit 1");
				$data['data']['id'] = $Cdata['id'];
				$data['data']['title'] = $Cdata['title'];

				$data['data']['img'] = 'gangmaduobao.com/'.$Cdata['img'];
				// $imgs = explode(',',$Cdata['img']);
				// foreach($imgs as $k => $v) {
				// 	$data['data']['img'][] ="gangmaduobao.com/".$v; 
				// }
				$data['data']['time'] = $Cdata['time'];
				$data['data']['content'] = $Cdata['neirong'];
				$data['data']['total'] = $Cdata['hueifu'];
				$data['data']['author'] = $user['username'];
				$comments = array();
				$comt = $this->com($cardid,$cardid,$Cdata['hueiyuan']);//评论
				foreach($comt as $k => $val) {
					unset($val['id']);
					unset($val['hueiyuan']);
					$comments[] = $val;
				}
				
				$comments = $this->bubble_sortm($comments);
				if(!$comments) {
					$data['data']['comments'] = array();
				}else {
					$data['data']['comments'] = $comments;
				}
				$rew = explode(',',$Cdata['reward']);//判断赏
				if(in_array($info['uid'],$rew)) {
					$data['data']['reward'] = 1;//已打赏
				}else {
					$data['data']['reward'] = 0;//未打赏
				}		
				// print_r($comments);die;
			}else {//后台发帖
				$data['data']['id']= $Cdata['id'];
				$data['data']['title']= $Cdata['title'];
				$data['data']['img']= 'gangmaduobao.com/'.$Cdata['img'];
				// $imgs = explode(',',$Cdata['img']);
				// foreach($imgs as $k => $v) {
				// 	$data['data']['img'][] ="gangmaduobao.com/".$v; 
				// }
				$data['data']['time'] = $Cdata['time'];
				$data['data']['total'] = $Cdata['hueifu'];
				$data['data']['content']= $Cdata['neirong'];
				$data['data']['author']= '管理员';
				$comments = array();
				$comt = $this->com($cardid,$cardid,'管理员');//评论
				foreach($comt as $k => $val) {
					unset($val['id']);
					unset($val['hueiyuan']);
					$comments[] = $val;
				}
				$comments = $this->bubble_sortm($comments);
				// print_r($comments);die;
				if(!$comments) {
					$data['data']['comments'] = array();
				}else {
					$data['data']['comments'] = $comments;
				}
				$data['data']['reward'] = 0;//未打赏
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
			$code = 100;
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
			$ptime = $this->db->GetOne("select time,shenhe from `@#_quanzi_tiezi` where id = '$cardid' limit 1");//被评论贴时间，状态
			if($ptime['shenhe'] == 'N') {
				$code = 100;
				$msg = "操作失败";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}

			if($ptime['time'] == $times) {
				$pid['id'] = $cardid;
			}else {
				$pid = $this->db->GetOne("select id,hueiyuan from `@#_quanzi_tiezi` where  tiezi = '$cardid' and time = '$times' limit 1");
				if($pid['hueiyuan'] == $info['uid']) {
					$code = 100;
					$msg = "自己评论不可回复";
					$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
					echo json_encode($json);die;
				}
			}
			$sql = "insert into `@#_quanzi_tiezi`(`hueiyuan`,`neirong`,`time`,`tiezi`,`pid`,`shenhe`) values('$info[uid]','$content','$time','$cardid','$pid[id]','Y')";
			$res = $this->db->Query($sql);
			$rult = $this->db->Query("update `@#_quanzi_tiezi` set hueifu = hueifu +1 where id = '$cardid'");
			// echo $sql;die;
			if($res && $rult) {
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
			$code = 100;
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
			$img       = stripslashes($_POST['img']);//去掉船餐过程中的反斜杠
			$time      = time();
			$user      = $info['uid'];
			$qzid      = 1;
			$imgname   = date('Ymdhis',time());
			$new_file  = '';
			$pic_path = 'images/upload/' . date("Ymd");
			// $imgurl = '';
			if(!file_exists($pic_path)) {
				if(!mkdir($pic_path, 0777)) {
					$code = 100;
					$msg = "目录创建失败";
					$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
					echo json_encode($json);die;
				}
				
			}
			// $imgs = explode('-',$img);//多图片上传
			// foreach($imgs as $key => $val) {
			// 	if($val) {
			// 		if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $val, $result)){
			// 			$type = $result[2];
			// 			// $imgname  = $imgname.rand(100,999);
			// 			$new_file = "{$pic_path}/{$imgname}.{$type}";//图片存储路径
			// 			if (!file_put_contents($new_file, base64_decode(str_replace($result[1], '', $val)))){
			// 				$code = 100;
			// 				$msg = "发帖失败";
			// 				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			// 				echo json_encode($json);die;
			// 			}
			// 		}else {
			// 			$tmp = base64_decode($val);
			// 			// $imgname  = $imgname.rand(100,999);
			// 			$new_file = "{$pic_path}/{$imgname}.jpg";//图片存储路径
			// 			if (!file_put_contents($new_file, $tmp)){
			// 				$code = 100;
			// 				$msg = "发帖失败";
			// 				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			// 				echo json_encode($json);die;
			// 			}	
			// 		}
			// 	}
				// if($imgurl) {
				// 	$imgurl .= ",".$new_file;
				// }else {
				// 	$imgurl = $new_file;
				// }
			// }

			if($img) {
				if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img, $result)){
					$type = $result[2];
					$new_file = "{$pic_path}/{$imgname}.{$type}";//图片存储路径
					if (!file_put_contents($new_file, base64_decode(str_replace($result[1], '', $img)))){
						$code = 100;
						$msg = "发帖失败";
						$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
						echo json_encode($json);die;
					}
				}else {
					$tmp = base64_decode($img);
					$new_file = "{$pic_path}/{$imgname}.jpg";//图片存储路径
					if (!file_put_contents($new_file, $tmp)){
						$code = 100;
						$msg = "发帖失败";
						$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
						echo json_encode($json);die;
					}	
				}
			}
			if($title && $content) {
				// $sql = "insert into `@#_quanzi_tiezi`(`qzid`,`hueiyuan`,`title`,`neirong`,`time`,`img`) values('$qzid','$user','$title','$content','$time','$imgurl')";
				$sql = "insert into `@#_quanzi_tiezi`(`qzid`,`hueiyuan`,`title`,`neirong`,`time`,`img`) values('$qzid','$user','$title','$content','$time','$new_file')";
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
	
	//打赏
	public function json_reward() {
		$code = '';
		$msg  = '';
		$data = array();
		$cardid  = abs(intval($_POST['id']));//帖子id 后台发帖不允许点赏
		$token = trim($_POST['token']);
		$num = abs(intval($_POST['money']));//赏夺宝币数目
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
			if(!$num){
				$code = 100;
				$msg = "赏夺宝币数目不能为空";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}
			$Cdata = $this->db->GetOne("select * from `@#_quanzi_tiezi` where id = '$cardid' limit 1");//帖子信息
			if($Cdata['shenhe'] == 'N') {
				$code = 100;
				$msg = "操作失败";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}

			if($Cdata['hueiyuan'] == '管理员') {
				$code = 100;
				$msg = "管理员发帖不许赏";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}

			// $users = $this->db->GetOne("select username from `@#_member` where uid = '$Cdata[hueiyuan]' limit 1");
			// print_r($Udata);die;
			if($Udata['uid'] == $Cdata['hueiyuan']) {
				$code = 100;
				$msg = "自己发帖不许赏";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}
			
			//夺宝币消费
			// $money = $this->db->GetOne("select score from `@#_reward`");

			if($Udata['money'] && $Udata['money'] >= $num) {
				$money = $Udata['money'] -$num;
				$res  = $this->db->Query("update `@#_member` set money = '$money' where uid = '$info[uid]'");
				//消费明细
				$rulo = $this->db->Query("INSERT INTO `@#_member_account` (`uid`, `type`, `pay`, `content`, `money`, `time`) VALUES ('".$info['uid']."', '-1', '账户', '赏', '$num', '".time()."')");
			}else {
				$code = 100;
				$msg = "夺宝币不足";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}

			if($Cdata['reward']) {
				$rew = explode(',',$Cdata['reward']);
				if(in_array($Udata['uid'],$rew)) {
					$ress = $this->db->Query("update `@#_member` set money = money + '$unm' where uid = '$Cdata[hueiyuan]'");
					//消费明细
					$rult = $this->db->Query("INSERT INTO `@#_member_account` (`uid`, `type`, `pay`, `content`, `money`, `time`) VALUES ('".$Cdata['hueiyuan']."', '1', '账户', '赏', '$num', '".time()."')");
					$ret  = 1;

				}else {
					$reward = $Cdata['reward'].",".$Udata['uid'];
					$ress = $this->db->Query("update `@#_member` set money = money + '$num' where uid = '$Cdata[hueiyuan]'");
					//消费明细
					$rult = $this->db->Query("INSERT INTO `@#_member_account` (`uid`, `type`, `pay`, `content`, `money`, `time`) VALUES ('".$Cdata['hueiyuan']."', '1', '账户', '赏', '$num', '".time()."')");
					$ret  = $this->db->Query("update `@#_quanzi_tiezi` set reward = '$reward' where id = '$cardid'");
				}
			}else {
				$ress = $this->db->Query("update `@#_member` set money = money + '$num' where uid = '$Cdata[hueiyuan]'");
				//消费明细
				$rult = $this->db->Query("INSERT INTO `@#_member_account` (`uid`, `type`, `pay`, `content`, `money`, `time`) VALUES ('".$Cdata['hueiyuan']."', '1', '账户', '赏', '$num', '".time()."')");
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
			
		}else {
			$code = 100;
			$msg = "操作失败";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);
		}			
	}

	//评论提示信息列表接口
	public function json_message() {
		$code = '';
		$msg  = '';
		$data = array();
		$token = trim($_POST['token']);
		$info = System::token_uid($token);
		// print_r($info);die;
		if($info['code'] == 100) {
			$code = 300;
			$msg = "用户未登陆";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);die;
		}
		$idarr = $this->db->GetList("select id from `@#_quanzi_tiezi` where hueiyuan = '$info[uid]' and tiezi != 0");
			$idss = '';
			foreach ($idarr as $key => $value) {
				if($idss) {
					$idss .= ','.$value['id'];
				}else {
					$idss = $value['id'];
				}
			}
			$rul = $this->db->GetList("select * from `@#_quanzi_tiezi` where pid in($idss) and ifsee = 0 order by time desc");
			// print_r($rul);die;
			$lid = $this->db->GetList("select id from `@#_quanzi_tiezi` where hueiyuan = '$info[uid]' and tiezi = 0");
			$ids = '';
			foreach ($lid as $key => $value) {
				if($ids) {
					$ids .= ','.$value['id'];
				}else {
					$ids = $value['id'];
				}
			}
			$anws = $this->db->GetList("SELECT * FROM `@#_quanzi_tiezi` WHERE tiezi IN($ids)  AND ifsees = 0  order by time desc");
			foreach ($anws as $key => $vl) {
				if($vl['hueiyuan'] == '$info[uid]' && $vl['pid'] == $vl['tiezi']) {
					unset($anws[$key]);
				}
			}

			if($rul) {
				foreach ($rul as $vl) {
					$anws[] = $vl;
				}
			}
			// print_r($anws);die;
			$keys = array();
			for ($i=0; $i<count($anws); $i++) {
				for ($j=$i+1; $j<count($anws); $j++) {
					if($anws[$i]['id'] == $anws[$j]['id']) {
						$keys[] = $j; 
					}
				}
			}
			foreach ($keys as $v) {
				unset($anws[$v]);
			}
			$keys = array();
			foreach ($anws as $key => $value) {
				unset($value['qzid']);
				unset($value['hueifu']);
				unset($value['dianji']);
				unset($value['zhiding']);
				unset($value['jinghua']);
				unset($value['zuihou']);
				unset($value['shenhe']);
				unset($value['type']);
				unset($value['reward']);
				unset($value['img']);
				unset($value['ifsee']);
				unset($value['ifsees']);
				unset($value['pid']);
				unset($value['title']);
				unset($value['id']);
				$keys[] = $value;
			}

			$anws = $this->bubble_sorts($keys);
		// print_r($anws);die;
		if($anws) {
			foreach($anws as $v) {
				if($v['hueiyuan'] != '管理员'){
					$user = $this->db->GetOne("select username, img from `@#_member` where uid = '$v[hueiyuan]'");
					$card = $this->db->GetOne("select id, img from `@#_quanzi_tiezi` where id = '$v[tiezi]'");
					if($v['hueiyuan'] != $info['uid']) {
						$v['id'] = $card['id'];
						$v['user'] = $user['username'];
						$v['img'] = 'gangmaduobao.com/'.$user['img'];
						if($card['img']) {
							$v['thumb'] = 'gangmaduobao.com/'.$card['img'];
						}else {
							$v['thumb'] = $card['img'];
						}
						unset($v['hueiyuan']);
						unset($v['tiezi']);
						$data[] = $v;
					}
				}else {
					$user = '管理员';
					$card = $this->db->GetOne("select id, img from `@#_quanzi_tiezi` where id = '$v[tiezi]'");
					if($v['hueiyuan'] != $info['uid']) {
						$v['id'] = $card['id'];
						$v['user'] = $user['username'];
						$v['img'] = 'gangmaduobao.com/'.$user['img'];
						if($card['img']) {
							$v['thumb'] = 'gangmaduobao.com/'.$card['img'];
						}else {
							$v['thumb'] = $card['img'];
						}
						unset($v['hueiyuan']);
						unset($v['tiezi']);
						$data[] = $v;
					}
				}
				
			}
			if($ids) {
				$rult = $this->db->Query("update `@#_quanzi_tiezi` set ifsees = 1  where tiezi in($ids) or id in($ids)");
				if(!$rult) {
					$code = 100;
					$msg = "操作失败";
					$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
					echo json_encode($json);die;
				}
			}
			if($idss) {
				$rults = $this->db->Query("update `@#_quanzi_tiezi` set ifsee = 1  where pid in($idss) ");
				if(!$rults) {
					$code = 100;
					$msg = "操作失败";
					$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
					echo json_encode($json);die;
				}
			}
			
			$code = 200;
			$msg = "查询成功";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);
		}

	}

}


?>