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
		$arr = $this->db->GetList("select cateid,name from `@#_category` where model = 1 and parentid = 0 and channel = 111");
		$data = array_merge($arr,$data);
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

	/*获得全部商品*/
	public function json_goodlist(){
		$code = '';
		$msg = '';
		$data = array();
		$pagenum = abs(intval($_POST['p']));
		if(empty($pagenum)) {
			$pagenum=1;
		}
		$total = $this->db->GetCount("select * from `@#_shoplist` where `shenyurenshu` > 0");
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
		$Sdata = $this->db->GetPage("SELECT id,`qishu` periods,`title`,`money`,`thumb`,`zongrenshu` total,`canyurenshu` part,`shenyurenshu` remain  FROM `@#_shoplist` where `shenyurenshu` > 0 order by time desc",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
		foreach($Sdata as $v) {
			if($v['thumb']) {
				$v['thumb'] = "gangmaduobao.com/statics/uploads/".$v['thumb'];
			}
			$data['data'][] = $v;
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

	/*获得全部人气商品*/
	public function json_hotlist(){
		$code = '';
		$msg = '';
		$data = array();
		$pagenum = abs(intval($_POST['p']));
		if(empty($pagenum)) {
			$pagenum=1;
		}
		$total = $this->db->GetCount("select * from `@#_shoplist` where `renqi` = '1' and `shenyurenshu` > 0");
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
		$Sdata = $this->db->GetPage("SELECT id,`qishu` periods,`title`,`money`,`thumb`,`zongrenshu` total,`canyurenshu` part,`shenyurenshu` remain  FROM `@#_shoplist` where `renqi` = 1 and `shenyurenshu` > 0 order by time desc",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
		foreach($Sdata as $v) {
			if($v['thumb']) {
				$v['thumb'] = "gangmaduobao.com/statics/uploads/".$v['thumb'];
			}
			$data['data'][] = $v;
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
			$total = $this->db->GetCount("select * from `@#_shoplist` where `cateid` = '$cateid' and `shenyurenshu` > 0");
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
			$Sdata = $this->db->GetPage("SELECT id,`qishu` periods,`title`,`money`,`thumb`,`zongrenshu` total,`canyurenshu` part,`shenyurenshu` remain  FROM `@#_shoplist` where `cateid` = '$cateid' and `shenyurenshu` > 0",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
			foreach($Sdata as $v) {
				if($v['thumb']) {
					$v['thumb'] = "gangmaduobao.com/statics/uploads/".$v['thumb'];
				}
				$data['data'][] = $v;
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
		}else {
			$code = 100;
			$msg = "操作失败";
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
		$Sdata = $this->db->GetPage("SELECT b.img,b.username, a.sd_id id,a.sd_qishu periods,a.sd_shopid shopid,a.sd_content content,a.sd_photolist photolist,a.sd_time time,a.sd_ping comments  FROM `@#_shaidan` a, `@#_member` b where a.sd_userid = b.uid  order by time desc",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
		foreach($Sdata as $v) {
			$aa = $this->db->GetOne("select title from `@#_shoplist` where `id`='$v[shopid]' ");			
			$v['title'] = strip_tags($aa['title']);
			$v['img'] = "gangmaduobao.com/".$v['img'];
			$photo = explode(",",$v['photolist']);
			foreach ($photo as $key => $value) {
				if($value) {
					$photo[$key] = "gangmaduobao.com/".$value;
				}else {
					unset($photo[$key]);
				}
			}
			$v['photolist'] = $photo;
			$data['data'][] = $v;
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

	/*获得商品详情数据*/
	public function json_detail() {
		$code = '';
		$msg = '';
		$data = array();
		$gid = abs(intval($_POST['gid']));
		$token = trim($_POST['token']);
		$dtime = time();
		$time = explode ( " ", microtime () ); 
		$time = $time [1] . ".".($time [0]*1000); 
		$time = substr($time, 10,4);
		$dtime = time().$time+(int)System::load_sys_config('system','goods_end_time');
		$time = $dtime - 300  ;
		if($token) {
			$info = System::token_uid($token);
			if($gid) {
				$data = $this->db->GetOne("SELECT id,qishu periods,title,`money`,picarr,zongrenshu total,canyurenshu part,shenyurenshu remain,q_end_time FROM `@#_shoplist` where id = '$gid' limit 1");
				if($data['remain'] == 0) {
					if($data['q_end_time'] < $time) {
						$data['state'] = "已揭晓";
						//计算过程（和，余数，结果）
						$arr = $this->db->GetOne("select * from `@#_shoplist` where id = '$gid'");
						if($arr['q_content']){
							$data['count']['type'] = 1;
							$data['count']['timeadd'] = $arr['q_counttime'];
							$data['count']['timemod'] = fmod($arr['q_counttime'],$arr['canyurenshu']);
							$data['count']['rul'] = 1000001;
						}else {
							$h=abs(date("H",$arr['q_end_time']));
							$i=date("i",$arr['q_end_time']);
							$s=date("s",$arr['q_end_time']);
							$w=substr($arr['q_end_time'],11,3);	
							$data['count']['type'] = 2;
							$data['count']['timeadd'] = $h.$i.$s.$w;
							$data['count']['timemod'] = fmod($data['count']['timeadd']*100,$arr['canyurenshu']);
							$data['count']['key'] = 1000001;
						}
					}
					if($data['q_end_time'] >= $time) {
						$data['state'] = "即将揭晓";
						$data['count'] = array();
					}
					unset($data['q_end_time']);					
				}else {
					$data['state'] = "夺宝中";
					$data['count'] = array();
				}
				$data['picarr'] = unserialize($data['picarr']);
				if(!$data['picarr']) {
					$data['picarr'] = array();
				}
				foreach ($data['picarr'] as $k => $v) {
					$data['picarr'][$k] = "gangmaduobao.com/statics/uploads/".$v;
				}
				$data['url'] = "gangmaduobao.com/?/mobile/mobile/goodsdesc/".$gid;
				$uids = $this->db->GetList("SELECT uid FROM `@#_member_go_record` where shopid = '$gid' and shopqishu = '$data[periods]'");
				$ids = '';
				foreach($uids as $v) {
					$ids[] = $v['uid'];
				}
				if(in_array($info['uid'], $ids)) {//参与夺宝显示是否中奖，云购几次，云购码
					$shop = $this->db->GetOne("select q_user_code,q_user from `@#_shoplist` where id = '$gid'");
					if($shop) {
						$shop['q_user'] = unserialize($shop['q_user']);
					}
					if($info['uid'] == $shop['q_user']['uid']) {
						$data['ustate'] = '恭喜本次夺宝获奖';
					}else {
						$data['ustate'] = '';
					}
					$num = $this->db->GetOne("SELECT count(id) num FROM `@#_member_go_record` where shopid = '$gid' and shopqishu = '$data[periods]' and uid = '$info[uid]'");
					$data['num'] = $num['num'];
					$codes = $this->db->GetList("SELECT goucode FROM `@#_member_go_record` where shopid = '$gid' and shopqishu = '$data[periods]' and uid = '$info[uid]'");
					foreach ($codes as $key => $value) {
						$cos = explode(',',$value['goucode']);
						foreach($cos as $vale) {
							$data['codes'][] = $vale;
						}
					}
					// print_r($codes);die;
				}else {
					$data['ustate'] = "您还未参与本期夺宝";
					$data['num'] = "";
					$data['codes'] = array();
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
			}else {
				$code = 100;
				$msg = "操作失败";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);
			}
		}else {
			if($gid) {
				$data = $this->db->GetOne("SELECT id,qishu periods,title,`money`,picarr,zongrenshu total,canyurenshu part,shenyurenshu remain,q_end_time FROM `@#_shoplist` where id = '$gid' limit 1");
				if($data['remain'] == 0) {
					if($data['q_end_time'] < $time) {
						$data['state'] = "已揭晓";
						//计算过程（和，余数，结果）
						$arr = $this->db->GetOne("select * from `@#_shoplist` where id = '$gid'");
						if($arr['q_content']){
							$data['count']['type'] = 1;
							$data['count']['timeadd'] = $arr['q_counttime'];
							$data['count']['timemod'] = fmod($arr['q_counttime'],$arr['canyurenshu']);
							$data['count']['rul'] = 1000001;
						}else {
							$h=abs(date("H",$arr['q_end_time']));
							$i=date("i",$arr['q_end_time']);
							$s=date("s",$arr['q_end_time']);
							$w=substr($arr['q_end_time'],11,3);	
							$data['count']['type'] = 2;
							$data['count']['timeadd'] = $h.$i.$s.$w;
							$data['count']['timemod'] = fmod($data['count']['timeadd']*100,$arr['canyurenshu']);
							$data['count']['key'] = 1000001;
						}
					}
					if($data['q_end_time'] >= $time) {
						$data['state'] = "即将揭晓";
						$data['count'] = array();
					}
					unset($data['q_end_time']);					
				}else {
					$data['state'] = "夺宝中";
					$data['count'] = array();
				}
				$data['picarr'] = unserialize($data['picarr']);
				if(!$data['picarr']) {
					$data['picarr'] = array();
				}
				foreach ($data['picarr'] as $k => $v) {
					$data['picarr'][$k] = "gangmaduobao.com/statics/uploads/".$v;
				}
				$data['url'] = "gangmaduobao.com/?/mobile/mobile/goodsdesc/".$gid;
				// $uids = $this->db->GetList("SELECT uid FROM `@#_member_go_record` where shopid = '$gid' and shopqishu = '$data[periods]'");
				// $ids = '';
				// foreach($uids as $v) {
				// 	$ids[] = $v['uid'];
				// }
				// if(in_array($info['uid'], $ids)) {
				// 	$data['ustate'] = "";//参与夺宝显示是否中奖，云购几次，云购码
				// }else {
				// 	$data['ustate'] = "您还未参与本期夺宝";
				// }
				$data['ustate'] = "";
				$data['num'] = "";
				$data['codes'] = array();
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
			}else {
				$code = 100;
				$msg = "操作失败";
				$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
				echo json_encode($json);
			}
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
			$Pdata = $this->db->GetPage("SELECT id,title,qishu periods,q_user_code gcode,canyurenshu part, q_end_time etime, q_user FROM `@#_shoplist` where q_showtime = 'N' and q_user_code IS NOT NULL and sid = '$item[sid]' order by periods desc",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));

			foreach($Pdata as $v) {
				$user = unserialize($v['q_user']);
				$v['username'] = $user['username'];
				$v['img'] = "gangmaduobao.com/".$user['img'];
				$ip = $this->db->GetOne("select ip from `@#_member` where uid = '$user[uid]'");
				$v['ip'] = $ip['ip'];
				unset($v['q_user']);
				$data['data'][] = $v;
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
		}else {
			$code = 100;
			$msg = "操作失败";
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
			$total = $this->db->GetCount("select * from `@#_shaidan` where  sd_shopid in($ids)");
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
			$Sdata = $this->db->GetPage("SELECT b.img,b.username,a.sd_id id,a.sd_title title,a.sd_qishu periods,a.sd_shopid shopid,a.sd_content content,a.sd_photolist photolist,a.sd_time time,a.sd_ping comments FROM `@#_shaidan` a, `@#_member` b where a.sd_userid = b.uid and a.sd_shopid in($ids)  order by time desc",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
			$comnum = 0;
			foreach($Sdata as $v) {
				$aa = $this->db->GetOne("select title from `@#_shoplist` where `id`='$v[shopid]' ");			
				$v['title'] = strip_tags($aa['title']);
				$v['img'] = "gangmaduobao.com/".$v['img'];
				$photo = explode(";",$v['photolist']);
				foreach ($photo as $key => $value) {
					if($value) {
						$photo[$key] = "gangmaduobao.com/".$value;
					}else {
						unset($photo[$key]);
					}
				}
				$v['photolist'] = $photo;
				$data['data'][] = $v;
				$comnum += $v['comments'];
			}
			if($data['data']) {
				$data['cnum'] = $comnum;
				$data['ptotal'] = $yeshu;
				$unum = $this->db->GetList("select count(sd_userid) id from `@#_shaidan` where sd_shopid in($ids)");
				if($unum) {
					foreach($unum as $v) {
						$data['usernum'] = $v['id'];
					}				
				}
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
		}else {
			$code = 100;
			$msg = "操作失败";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);
		}
		
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
			// $arr = $this->db->GetList("select id from `@#_shoplist` where sid = '$item[sid]'");
			// $ids = '';
			// foreach ($arr as $key => $value) {
			// 	if(empty($ids)) {
			// 		$ids .= $value['id'];
			// 	}else {
			// 		$ids .= ",".$value['id'];
			// 	}
			// }
			// $total = $this->db->GetCount("select * from `@#_member_go_record` where  shopid in($ids)");
			$total = $this->db->GetCount("select * from `@#_member_go_record` where  shopid = '$id')");
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
			$Idata = $this->db->GetPage("SELECT id,username,uphoto,time,ip,gonumber FROM `@#_member_go_record`  where  shopid = '$id' order by time desc",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
			foreach($Idata as $v) {
				$v['uphoto'] = "gangmaduobao.com/".$v['uphoto'];
				$data['data'][] = $v;
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
		}else {
			$code = 100;
			$msg = "操作失败";
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
			$Sdata = $this->db->GetPage("select id,`qishu` periods,`title`,`thumb`,`money`,`zongrenshu` total,`canyurenshu` part,`shenyurenshu` remain from `@#_shoplist` where title like '%$keywords%' ",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
			foreach($Sdata as $v) {
				if($v['thumb']) {
					$v['thumb'] = "gangmaduobao.com/statics/uploads/".$v['thumb'];
				}
				$data['data'][] = $v;
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
		}else {
			$code = 100;
			$msg = "操作失败";
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);
		}
	}
	/*猜你喜欢*/
	public function love() {
		$token = isset($_POST['token']) ? $_POST['token'] : null;
		$info = System::token_uid($token);
		if ($info['code']==200) {
			$db = System::load_sys_class('model');
			$data = $db->GetList("select id,thumb,title,money,qishu,canyurenshu,shenyurenshu from `@#_shoplist` where `q_uid` is null and `renqi` = '1' order by id DESC LIMIT 6 ");
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
			$json = array('code' => $code, 'msg' => $msg, 'data' => $data);
			echo json_encode($json);
		}else{
			$json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
			echo json_encode($json);
		}
		
	}

}

?>