<?php

defined('G_IN_SYSTEM')or exit("no");

class shop extends SystemAction {
	//json获取获取栏目
	public function __construct(){		
		$this->db=System::load_sys_class('model');
	}

	/*获得商品分类数据*/
	public function json_shopcate() {
		$code = '';
		$msg = '';
		$data = array();
		$data = $this->db->GetList("select cateid,name from `@#_category` where model = 1 and parentid != 0");
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

	/*获得商品数据*/
	public function json_shoplist(){
		$code = '';
		$msg = '';
		$data = array();
		$cateid =  abs(intval($_POST['cid']));
		$pagenum = abs(intval($_POST['p']));
		if(empty($pagenum)) {
			$pagenum=1;
		} 
		if($cateid){
			$total = $this->db->GetCount("select * from `@#_shoplist` where `cateid` = '$cateid'");
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
			$data = $this->db->GetPage("SELECT id,`qishu` periods,`title`,`money`,`zongrenshu` total,`canyurenshu` part,`shenyurenshu` remain  FROM `@#_shoplist` where `cateid` = '$cateid'",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
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
	}

	/*获得晒单数据*/
	public function json_shopshare() {
		$code = '';
		$msg = '';
		$data = array();
		$pagenum = abs(intval($_POST['p']));
		if(empty($pagenum)) {
			$pagenum=1;
		}
		$total = $this->db->GetCount("select * from `@#_shaidan`");
		$num = 4;
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
		$data = $this->db->GetPage("SELECT b.img,b.username, a.sd_id id,a.sd_qishu periods,a.sd_shopid shopid,a.sd_content content,a.sd_photolist photolist,a.sd_time time,a.sd_ping comments  FROM `@#_shaidan` a, `@#_member` b where a.sd_userid = b.uid",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
		foreach($data as $key => $val) {
			$title = $this->db->GetOne("select title from `@#_shoplist` where id = ".$val['sd_shopid']." limit 1");
			$data[$key]['title'] = $title['title'];
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

	/*获得商品详情数据*/
	public function json_detail() {
		$code = '';
		$msg = '';
		$data = array();
		$gid = abs(intval($_POST['gid']));
		if($gid) {
			$data = $this->db->GetOne("SELECT qishu periods,title,picarr,zongrenshu total,canyurenshu part,shenyurenshu remain FROM `@#_shoplist` where id = '$gid' limit 1");
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
	}

	/*获得往期商品数据*/
	public function json_publish() {
		$code = '';
		$msg = '';
		$data = array();
		$itemid = abs(intval($_POST['gid']));
		$pagenum = abs(intval($_POST['p']));
		if(empty($pagenum)) {
			$pagenum=1;
		}
		$item = $this->db->GetOne("select * from `@#_shoplist` where `id`='$itemid' LIMIT 1");
		if($item) {
			$total = $this->db->GetCount("select * from `@#_shoplist` where q_showtime = 'N' and q_user_code IS NOT NULL and sid = '$item[sid]'");
			$num = 4;
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
			$data = $this->db->GetPage("SELECT title,username,img,user_ip,qishu,q_user_code,canyurenshu FROM `@#_shoplist` a, `@#_member` b where q_showtime = 'N' and q_user_code IS NOT NULL and sid = '$item[sid]' and a.q_uid = b.uid",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));

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
	}

	/*获得商品晒单数据*/
	public function json_goodshare() {
		$code = '';
		$msg = '';
		$data = array();
		$id =  abs(intval($_POST['gid']));
		$pagenum = abs(intval($_POST['p']));
		if(empty($pagenum)) {
			$pagenum=1;
		}
		$total = $this->db->GetCount("select * from `@#_shaidan` where sd_shopid = $id ");
		$num = 4;
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
		$data = $this->db->GetPage("SELECT b.img,b.username,a.sd_qishu,a.sd_shopid,a.sd_content,a.sd_photolist,a.sd_time,a.sd_ping FROM `@#_shaidan` a, `@#_member` b where a.sd_userid = b.uid and a.sd_shopid = $id ",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
		foreach($data as $key => $val) {
			$title = $this->db->GetOne("select title from `@#_shoplist` where id = ".$val['sd_shopid']." limit 1");
			$data[$key]['title'] = $title['title'];
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

	/*获得夺宝记录数据*/
	public function json_indiana() {
		$code = '';
		$msg = '';
		$data = array();
		$id = abs(intval($_POST['gid']));
		$pagenum = abs(intval($_POST['p']));
		if(empty($pagenum)) {
			$pagenum=1;
		}
		$item = $this->db->GetOne("select * from `@#_shoplist` where `id`='$id' LIMIT 1");
		if($item) {
			$arr = $this->db->GetList("select id from `@#_shoplist` where sid = '$item[sid]'");
			$ids = '';
			foreach ($arr as $key => $value) {
				if(empty($ids)) {
					$ids .= $value['id'];
				}else {
					$ids .= ",".$value['id'];
				}
			}
			$total = $this->db->GetCount("select * from `@#_member_go_record` where  shopid in($ids)");
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
			$data = $this->db->GetPage("SELECT a.canyurenshu,b.username,b.uphoto,b.time,b.ip FROM `@#_shoplist` a, `@#_member_go_record` b where a.id = b.shopid and b.shopid in($ids)",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
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
	}

	/*获得商品搜索数据*/
	public function json_search() {
		$code = '';
		$msg = '';
		$data = array();
		$keywords = $_POST['keys'];
		$pagenum = abs(intval($_POST['p']));
		if(empty($pagenum)) {
			$pagenum=1;
		}
		if($keywords) {
			$total = $this->db->GetCount("select * from `@#_shoplist` where `title` like '%$keywords%'");
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
			$data = $this->db->GetPage("select `qishu`,`title`,`money`,`zongrenshu`,`canyurenshu`,`shenyurenshu` from `@#_shoplist` where title like '%$keywords%' ",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
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
	}
}

?>