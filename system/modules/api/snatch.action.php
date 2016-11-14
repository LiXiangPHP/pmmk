<?php

class snatch extends SystemAction {
	public function sc(){
		$db = System::load_sys_class('model');
		$type = isset($_POST['type']) ? $_POST['type'] : null;
		$token = isset($_POST['token']) ? $_POST['token'] : null;
		$info = System::token_uid($token);
		if ($info['code']==200) {
			$dtime = time();
			$time = explode ( " ", microtime () ); 
			$time = $time [1] . ".".($time [0]*1000); 
			$time = substr($time, 10,4);
			$dtime = time().$time+(int)System::load_sys_config('system','goods_end_time');
			$time = $dtime - 300  ;	
			//正在进行
			if($type==1){
			$newdata=array();
			$zhsz = array();
			$mygm = $db->GetList("select time,shopqishu,shopid from `@#_member_go_record` where uid='$info[uid]' ");
			
			foreach($mygm as $v){
				$zhsz[] = $db->GetList("select qishu,id,title,zongrenshu,canyurenshu,thumb from `@#_shoplist` where qishu='$v[shopqishu]' and id='$v[shopid]' and  q_user_code is null ");				
			}
			$data=array_filter($zhsz,create_function('$v','return !empty($v);'));
			foreach($data as $k=>$v){
	        	foreach($v as $i=>$j){
	            $newdata [] = $j;  
	        	}
			}
			foreach($newdata as $k=>$v) {
			$newdata[$k]['thumb'] = "gangmaduobao.com/statics/uploads/".$v['thumb'];
			}
			$nndata = array_reverse($newdata);
			if($nndata) {
				$code = 200;
				$msg = "查询成功";
			}else {
				$code = 400;
				$msg = "数据为空";
			}
			$json = array('type' => $type,'code' => $code, 'msg' => $msg, 'data' => $nndata);
			echo json_encode($json);			
			}

			// //待揭晓
			if($type==3){
			$zhsz = array();
			$newdata=array();
			$mygm = $db->GetList("select time,shopqishu,shopid from `@#_member_go_record` where uid='$info[uid]' group by shopid ");
			foreach($mygm as $v){
				$zhsz[] = $db->GetList("select a.qishu,a.id,a.q_end_time,a.title,a.thumb,a.money,b.mobile,a.q_end_time from `@#_shoplist` as a,`@#_member` as b where a.q_uid=b.uid and a.qishu='$v[shopqishu]' and a.id='$v[shopid]' and  a.q_end_time is not null and a.q_user is not null and a.`q_end_time` >= $time ");				
			}
			$data=array_filter($zhsz,create_function('$v','return !empty($v);'));
			foreach($data as $k=>$v){
	        	foreach($v as $i=>$j){
	            $newdata [] = $j;  
	        	}
			}
			foreach($newdata as $k=>$v) {
			$newdata[$k]['thumb'] = "gangmaduobao.com/statics/uploads/".$v['thumb'];
			}
			if($newdata) {
				$code = 200;
				$msg = "查询成功";
				$time = $dtime;
			}else {
				$code = 400;
				$msg = "数据为空";
			}
			$json = array('type' => $type,'code' => $code, 'msg' => $msg,'time' => $time, 'data' => $newdata);
			echo json_encode($json);				
			}
			//已揭晓
			if($type==2){
			$zhsz = array();
			$newdata=array();
			$mygm = $db->GetList("select time,shopqishu,shopid from `@#_member_go_record` where uid='$info[uid]' group by shopid ");
			foreach($mygm as $v){
				$zhsz[] = $db->GetList("select a.qishu,a.id,a.q_end_time,a.title,a.thumb,a.money,b.mobile from `@#_shoplist` as a,`@#_member` as b where a.q_uid=b.uid and a.qishu='$v[shopqishu]' and a.id='$v[shopid]' and  a.q_end_time is not null and a.q_user is not null and a.`q_end_time` < $time ");				
			}
			$data=array_filter($zhsz,create_function('$v','return !empty($v);'));
			foreach($data as $k=>$v){
	        	foreach($v as $i=>$j){
	            $newdata [] = $j;  
	        	}
			}
			foreach($newdata as $k=>$v) {
			$newdata[$k]['thumb'] = "gangmaduobao.com/statics/uploads/".$v['thumb'];
			}
			if($newdata) {
				$code = 200;
				$msg = "查询成功";
			}else {
				$code = 400;
				$msg = "数据为空";
			}
			$json = array('type' => $type,'code' => $code, 'msg' => $msg, 'data' => $newdata);
			echo json_encode($json);			
			}

		}else{
			$json = array('code' => 300, 'msg' => '请登录');
			echo json_encode($json);
		}	
		
	}
}

?>