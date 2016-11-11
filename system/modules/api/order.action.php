<?php

class order extends SystemAction {
	//中奖收货
	public function od(){
		$token = isset($_POST['token']) ? $_POST['token'] : null;
		$info = System::token_uid($token);
		$type = isset($_POST['type']) ? $_POST['type'] : null;
		if ($info['code']==200) {
			$dtime = time();
			$time = explode ( " ", microtime () ); 
			$time = $time [1] . ".".($time [0]*1000); 
			$time = substr($time, 10,4);
			$dtime = time().$time+(int)System::load_sys_config('system','goods_end_time');
			$time = $dtime - 300  ;	
			//未发货
			if ($type==1) {
				$db = System::load_sys_class('model');
				$data = $db->GetList("select b.q_end_time,a.shopname,a.shopqishu,b.thumb from `@#_member_go_record` as a,`@#_shoplist` as b where a.shopid=b.id and  a.uid='$info[uid]' and a.huode>10000000 and status like '%未发货%' and b.q_end_time < $time ");
				foreach($data as $k=>$v) {
				$data[$k]['thumb'] = "gangmaduobao.com/statics/uploads/".$v['thumb'];
				}
				if($data) {
					$code = 200;
					$msg = "查询成功";
				}else {
					$code = 400;
					$msg = "数据为空";
				}
				$json = array('code' => $code, 'msg' => $msg, 'moneytoal' => $money, 'data' => $data);
				echo json_encode($json);
			}
			//已发货，待收货
			if ($type==2) {
				$db = System::load_sys_class('model');
				$data = $db->GetList("select b.q_end_time,a.shopname,a.shopqishu,a.id,b.thumb from `@#_member_go_record` as a,`@#_shoplist` as b where a.shopid=b.id and  a.uid='$info[uid]' and a.huode>10000000 and status like '%已发货,待收货%' ");
				foreach($data as $k=>$v) {
				$data[$k]['thumb'] = "gangmaduobao.com/statics/uploads/".$v['thumb'];
				}
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
			//已收货
			if ($type==3) {
				$db = System::load_sys_class('model');
				$data = $db->GetList("select b.q_end_time,a.shopname,a.shopqishu,a.yishaidan,a.id,b.thumb from `@#_member_go_record` as a,`@#_shoplist` as b where a.shopid=b.id and  a.uid='$info[uid]' and a.huode>10000000 and status like '%已发货,已完成%' ");
				foreach($data as $k=>$v) {
				$data[$k]['thumb'] = "gangmaduobao.com/statics/uploads/".$v['thumb'];
				}
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
			
		}else{
			$json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
			echo json_encode($json);
		}
		
		

	}
	//确认收货接口
	public function qr(){
		$db = System::load_sys_class('model');
		$token = isset($_POST['token']) ? $_POST['token'] : null;
		$info = System::token_uid($token);
		if ($info['code']==200) {
			$id = isset($_POST['id']) ? $_POST['id'] : null;		
			$data = $db->GetOne("select * from `@#_member_go_record` where id=$id  ");
			if($data) {
						$code = 200;
						$msg = "成功";
						$data = $db->Query("update `@#_member_go_record` set status='已付款,已发货,已完成' where id=$id ") ;
					}else {
						$code = 400;
						$msg = "失败";
					}
					$json = array('code' => $code, 'msg' => $msg );
					echo json_encode($json);
		}else{
			$json = array('code' => 300, 'msg' => '请登录');
			echo json_encode($json);
		}
		
	
	}
	//我要晒单
	public function wysd(){
		$token = isset($_POST['token']) ? $_POST['token'] : null;
		$db = System::load_sys_class('model');
		$info = System::token_uid($token);
		if ($info['code']==200) {
		$img       = stripslashes($_POST['img']);//去掉船餐过程中的反斜杠
		$imgname   = date('Ymdhis',time());
		$new_file  = '';
		$pic_path = 'statics/uploads/shaidan/' . date("Ymd");
		if(!file_exists($pic_path)) {
			if(!mkdir($pic_path, 0777)) {
				$code = 100;
				$msg = "目录创建失败";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);die;
			}
				
		}
		$imgs = explode('-',$img);
			foreach($imgs as $key => $val) {
				if($val) {
					if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $val, $result)){
						$type = $result[2];
						$imgname  = $imgname.rand(100,999);
						$new_file = "{$pic_path}/{$imgname}.{$type}";//图片存储路径
						if (!file_put_contents($new_file, base64_decode(str_replace($result[1], '', $val)))){
							$code = 100;
							$msg = "发帖失败";
							$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
							echo json_encode($json);die;
						}
					}else {
						$tmp = base64_decode($val);
						$imgname  = $imgname.rand(100,999);
						$new_file = "{$pic_path}/{$imgname}.jpg";//图片存储路径
						if (!file_put_contents($new_file, $tmp)){
							$code = 100;
							$msg = "发帖失败";
							$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
							echo json_encode($json);die;
						}	
					}
				}
				if($imgurl) {
					$imgurl .= ",".$new_file;
				}else {
					$imgurl = $new_file;
				}
			}


		$id = isset($_POST['id']) ? $_POST['id'] : null;
		$array = $db->GetOne("select * from `@#_member_go_record` where id=$id  ");
		$sd_userid = $array['uid'];
		$sd_shopid = $array['shopid'];
		$sd_qishu = $array['shopqishu'];
		$sd_content = isset($_POST['content']) ? $_POST['content'] : null;
		$sd_photolist = $imgurl;
		$sd_time = time();
		if($sd_content) {
					$code = 200;
					$msg = "成功";
					$data = $db->Query("INSERT INTO `@#_shaidan`(`sd_userid`,`sd_shopid`,`sd_qishu`,`sd_content`,`sd_photolist`,`sd_time`)VALUES('$sd_userid','$sd_shopid','$sd_qishu','$sd_content','$sd_photolist','$sd_time')");
					$data1 = $db->Query("update `@#_member_go_record` set yishaidan='1' where id=$id ") ;
				}else {
					$code = 400;
					$msg = "失败";
				}
				$json = array('code' => $code, 'msg' => $msg );
				echo json_encode($json);

		}else{
			$json = array('code' => 300, 'msg' => '请登录');
			echo json_encode($json);
		}
		

	}
}

?>