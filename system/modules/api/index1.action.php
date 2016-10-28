<?php

class index1 extends SystemAction {

	//首页展示
	public function glist(){
		
		$db = System::load_sys_class('model');
		//banner
		$bannerurl = $db->GetList("select img from `@#_slide` ");
		foreach($bannerurl as $k=>$v) {
				$bannerurl[$k]['img'] = "gangmaduobao.com/statics/uploads/".$v['img'];
			}
		//最新获奖信息
		$member_record=$db->GetList("select a.username,a.shopname from `@#_member_go_record` as a,`@#_shoplist` as b where a.shopid=b.id and b.q_uid is not null  order by a.id DESC LIMIT 7");
		//商品列表
		$hotshop = $db->GetList("select id,thumb,title,money,qishu,canyurenshu,shenyurenshu from `@#_shoplist` where `renqi` = '1' and `shenyurenshu`>0 order by id DESC LIMIT 6 ");
		
		foreach($hotshop as $k=>$v) {
				$hotshop[$k]['thumb'] = "gangmaduobao.com/statics/uploads/".$v['thumb'];
			}
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
	//开奖
	public function kj(){
		$type = isset($_POST['type']) ? $_POST['type'] : null;
		$db = System::load_sys_class('model');
		//待开奖
		if ($type==1) {	
		// $pagenum = abs(intval($_POST['p']));
		$pagenum = isset($_POST['pagenum']) ? $_POST['pagenum'] : null;
		if(empty($pagenum)) {
		$pagenum=1;
		}
		$total = $db->GetCount("select * from `@#_shoplist` where `q_uid` is null ");	
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
		$data = $db->GetPage("select id,canyurenshu,shenyurenshu,title,money,thumb from `@#_shoplist` where `q_uid` is null order by shenyurenshu asc ",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
		foreach($data as $k=>$v) {
				$data[$k]['thumb'] = "gangmaduobao.com/statics/uploads/".$v['thumb'];
			}
		if($data) {
				
				$code = 200;
				$msg = "查询成功";
			}else {
				$code = 100;
				$msg = "数据为空";
			}
			$json = array('code' => $code, 'msg' => $msg, 'ptotal'=> $yeshu,'data' => $data);
			echo json_encode($json);

		}
		//最新揭晓
		if ($type==2) {	
			$data = $db->GetPage("select a.username,a.shopname,b.q_user_code,b.q_end_time,b.thumb,b.id from `@#_member_go_record` as a,`@#_shoplist` as b where a.shopid=b.id and b.q_uid is not null  order by a.id DESC LIMIT 12");
			foreach($data as $k=>$v) {
				$data[$k]['thumb'] = "gangmaduobao.com/statics/uploads/".$v['thumb'];
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
	}

}

?>