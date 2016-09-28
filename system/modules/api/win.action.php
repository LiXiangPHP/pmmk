<?php

class win extends SystemAction {
	public function wn(){
		$type = isset($_POST['type']) ? $_POST['type'] : null;
		$token = isset($_POST['token']) ? $_POST['token'] : null;
		$info = System::token_uid($token);
		if ($info['code']==200) {
			$db = System::load_sys_class('model');
			$mygm = $db->GetList("select shopqishu,shopid from `@#_member_go_record` where uid='$info[uid]' ");
			$zhsz = array();			
			foreach($mygm as $v){
				$zhsz[] = $db->GetList("select a.q_end_time,a.title,a.thumb,a.money,b.username from `@#_shoplist` as a,`@#_member` as b where a.q_uid=b.uid and a.qishu='$v[shopqishu]' and a.id='$v[shopid]' and  a.q_end_time is not null and a.q_user is not null ");			
			}
			$arr = array_filter($zhsz,create_function('$v','return !empty($v);'));

			print_r($arr);die();
			
		




		}else{
			$json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
			echo json_encode($json);
		}	
		
}

?>