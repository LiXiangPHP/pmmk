<?php

class index1 extends SystemAction {


	public function glist(){
		
		$db = System::load_sys_class('model');
		//banner
		$bannerurl = $db->GetList("select img from `@#_slide` ");
		//最新获奖信息
		$member_record=$db->GetList("select username,shopname from `@#_member_go_record` order by id DESC LIMIT 7");
		//商品列表
		$hotshop = $db->GetList("select id,thumb,title,money,qishu,canyurenshu,shenyurenshu from `@#_shoplist` leftjion  where `q_uid` is null and `renqi` = '1' order by id DESC LIMIT 6 ");
		$data = array(
		'banner'=>$bannerurl,
		'zxhjxx'=>$member_record,
		'xplb'=>$hotshop,
		);
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