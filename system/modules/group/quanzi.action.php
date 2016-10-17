<?php 

defined('G_IN_SYSTEM')or exit('');
System::load_app_class('admin',G_ADMIN_DIR,'no');
System::load_sys_fun("user");
class quanzi extends admin {
	
	public function __construct(){
		parent::__construct();
		$this->ment=array(
			array("lists","贴子管理",ROUTE_M.'/'.ROUTE_C.""),
			// array("addcate","添加圈子",ROUTE_M.'/'.ROUTE_C."/insert"),	
			array("addcard","添加帖子",ROUTE_M.'/'.ROUTE_C."/insert"),	
			array("addcate","待审核",ROUTE_M.'/'.ROUTE_C."/shenhe_list/tiezi"),	
			//array("addcate","帖子回复查看",ROUTE_M.'/'.ROUTE_C."/liuyan"),
			array("design","裳积分",ROUTE_M.'/'.ROUTE_C."/design"),	
		);
		$this->db=System::load_sys_class("model");
	} 
	
	/*设置裳积分*/
	public function design() {

		$reward = $this->db->GetOne("select score from `@#_reward`");

		if(isset($_POST["submit"]))
		{
			if($_POST['score']==null)_message("赏消耗积分不能为空",null,3);
			$score= abs(intval($_POST['score']));
			$rul = $this->db->Query("update `@#_reward` set `score` = '$score'");
			
			if($rul) {
				_message("设置成功");
			}else {
				_message("设置失败");
			}
			
		}
		include $this->tpl(ROUTE_M,'quanzi.reward');
	}
	
	/*审核帖子*/	
	public function shenhe_list(){
	
		$types = $this->segment(4);
		
		if($types == 'tiezi'){
			$sql1 = "SELECT COUNT(id) FROM `@#_quanzi_tiezi` WHERE `tiezi` = '0' and `shenhe` = 'N'";
			$sql2 = "SELECT * FROM `@#_quanzi_tiezi` WHERE `tiezi` = '0' and `shenhe` = 'N'";
		}else{
			$sql1 = "SELECT COUNT(id) FROM `@#_quanzi_tiezi` WHERE `tiezi` != '0' and `shenhe` = 'N'";
			$sql2 = "SELECT * FROM `@#_quanzi_tiezi` WHERE `tiezi` != '0' and `shenhe` = 'N'";
		}
		
	
		$num=20;
		$total=$this->db->GetCount($sql1);
		$page=System::load_sys_class('page');
		if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}	
		$page->config($total,$num,$pagenum,"0");
		$glist=$this->db->GetPage($sql2,array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));

		include $this->tpl(ROUTE_M,'quanzi.shenhe');
	}
	
	/*显示全部圈子*/
	public function init(){	
		// $quanzi=$this->db->GetList("select * from `@#_quanzi` where 1");
		// include $this->tpl(ROUTE_M,'quanzi.list');
		$num = 20;
		$total=$this->db->GetCount("select * from `@#_quanzi_tiezi` where `tiezi` = '0' and `pid` = '0' and `shenhe` = 'Y'"); 
		$page=System::load_sys_class('page');
		if(isset($_GET['p'])){
			$pagenum=$_GET['p'];
		}else{$pagenum=1;}		
		$page->config($total,$num,$pagenum,"0"); 
		if($pagenum>$page->page){
			$pagenum=$page->page;
		}
		$rews = $this->db->GetOne("select score from `@#_reward` ");	
		$tiezi=$this->db->GetPage("select * from `@#_quanzi_tiezi` where `tiezi` = '0' and `pid` = '0' and `shenhe` = 'Y'",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));		
		foreach ($tiezi as $k => $val) {
			$user = $this->db->GetOne("select mobile from `@#_member` where uid = '$val[hueiyuan]'");
			$tiezi[$k]['mobile'] = $user['mobile'];
			if(strlen($val['neirong']) > 15) {
				for ($i = 0; $i < 15;$i++) {
					if(ord($val['neirong'][$i]) > 128) $i++;
					 $tiezi[$k]['neirong'] = substr($val['neirong'],0,$i)."...";
				}
			}
			$rew = explode(',',$val['reward']);
			$num = count($rew);
			$tiezi[$k]['score'] = $num * $rews['score'];
		}
		include $this->tpl(ROUTE_M,'quanzi.tiezi');
	}
	
	/*显示添加圈子*/
	public function insert(){
		if(isset($_POST["submit"]))
		{
			if($_POST['title']==null)_message("帖子标题不能为空",null,3);
			$title= htmlspecialchars($_POST['title']);
			
			// $guanli= htmlspecialchars($_POST['guanli']);
			// $glfatie= htmlspecialchars($_POST['glfatie']);
			// $huifu= htmlspecialchars($_POST['huifu']);
			// $shenhe= htmlspecialchars($_POST['shenhe']);
			
			// $checkemail=_checkemail($guanli);
			// $checkemobile=_checkmobile($guanli);
			// if($checkemail===false && $checkemobile===false){
			// 	_message("圈子管理员信息填写错误");
			// }
			// $res=$this->db->GetOne("SELECT uid FROM `@#_member` WHERE `email`='$guanli' or `mobile`='$guanli'");
			// if(empty($res)){
			// 	_message("圈子管理员不存在");
			// }else{
			// 	$guanli=$res['uid'];
			// }
			
			// $jiaru= $_POST['jiaru'];
			$jianjie=htmlspecialchars($_POST['jianjie']);
			// $gongao=htmlspecialchars($_POST['gongao']);
			$time= time();			
			$img = htmlspecialchars($_POST['img']);
			$rul = $this->db->Query("INSERT INTO `@#_quanzi_tiezi`(`hueiyuan`,`title`,`img`,`neirong`,`time`,`type`,`shenhe`) VALUES('管理员','$title','$img','$jianjie','$time','1','Y')");
			if($rul) {
				_message("添加成功");
			}else {
				_message("添加失败");
			}
			
		}
		include $this->tpl(ROUTE_M,'quanzi.insert');
	}
	
	/*圈子修改*/
	public function quanzi_update(){
		$id=intval($this->segment(4));
		$quanzi=$this->db->GetOne("select * from `@#_quanzi` where `id`='$id'");
		$member=$this->db->GetOne("select email,mobile from `@#_member` where `uid`='$quanzi[guanli]'");
		if(!$quanzi)_message("参数错误");
		
		if(isset($_POST["submit"])){
			if($_POST['title']==null)_message("圈子名不能为空");
			$title= htmlspecialchars($_POST['title']);
			$glfatie= htmlspecialchars($_POST['glfatie']);
			$huifu= htmlspecialchars($_POST['huifu']);
			$guanli= htmlspecialchars($_POST['guanli']);
			$shenhe= htmlspecialchars($_POST['shenhe']);
			
			$checkemail=_checkemail($guanli);
			$checkemobile=_checkmobile($guanli);
			if($checkemail===false && $checkemobile===false){
				_message("圈子管理员信息填写错误");
			}
			$res=$this->db->GetOne("SELECT uid FROM `@#_member` WHERE `email`='$guanli' or `mobile`='$guanli'");
			if(empty($res)){
				_message("圈子管理员不存在");
			}else{
				$guanli=$res['uid'];
			}
			
			$jiaru= $_POST['jiaru'];
			$jianjie=htmlspecialchars($_POST['jianjie']);
			$gongao=htmlspecialchars($_POST['gongao']);
			$time= time();
			$img = htmlspecialchars($_POST['img']);				
			$this->db->Query("UPDATE `@#_quanzi` SET title='$title',img='$img',glfatie='$glfatie',huifu='$huifu',shenhe='$shenhe',guanli='$guanli',jianjie='$jianjie',gongao='$gongao',jiaru='$jiaru',time='$time' where`id`='$id'");
			_message("修改成功");
		}		
				
		include $this->tpl(ROUTE_M,'quanzi.update');
	}
	
	/*显示圈子里面全部帖子*/
	public function tiezi(){
		// $qzid = intval($this->segment(4));	
		// if(!$qzid)_message("参数错误");
		$num = 20;
		$total=$this->db->GetCount("select * from `@#_quanzi_tiezi` where `tiezi` = '0' and `pid` = '0' and `shenhe` = 'Y'"); 
		$page=System::load_sys_class('page');
		if(isset($_GET['p'])){
			$pagenum=$_GET['p'];
		}else{$pagenum=1;}	
		$page->config($total,$num,$pagenum,"0"); 
		if($pagenum>$page->page){
			$pagenum=$page->page;
		}
		$rews = $this->db->GetOne("select score from `@#_reward` ");	
		$tiezi=$this->db->GetPage("select * from `@#_quanzi_tiezi` where `tiezi` = '0' and `pid` = '0' and `shenhe` = 'Y'",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));		
		foreach ($tiezi as $k => $val) {
			$user = $this->db->GetOne("select mobile from `@#_member` where uid = '$val[hueiyuan]'");
			$tiezi[$k]['mobile'] = $user['mobile'];
			if(strlen($val['neirong']) > 15) {
				for ($i = 0; $i < 15;$i++) {
					if(ord($val['neirong'][$i]) > 128) $i++;
					 $tiezi[$k]['neirong'] = substr($val['neirong'],0,$i)."...";
				}
			}
			$rew = explode(',',$val['reward']);
			$num = count($rew);
			$tiezi[$k]['score'] = $num * $rews['score'];
		}
		include $this->tpl(ROUTE_M,'quanzi.tiezi');
	}
	
	/*帖子查看*/
	public function tiezi_update(){
		$id=$this->segment(4);
		$tiezi=$this->db->GetOne("select * from `@#_quanzi_tiezi` where `id`='$id' and `shenhe` = 'Y'");
		$user = $this->db->GetOne("select username from `@#_member` where uid = '$tiezi[hueiyuan]' limit 1");
		$tiezi['user'] = $user['username'];
		$ids = explode(',', $tiezi['reward']);
		$rusers = '';
		if($ids && is_array($ids)) {
			foreach ($ids as $key => $v) {
				$users = $this->db->GetOne("select username from `@#_member` where uid = '$v'");
				if($rusers) {
					$rusers .= ','.$users['username'];
				}else {
					$rusers = $users['username'];
				}
			}
		}
		$tiezi['ruser'] = $rusers;
		include $this->tpl(ROUTE_M,'quanzi.tiezi.update');
	}
	
	//显示全部留言
	public function liuyan(){
		
		$id=$this->segment(4);//帖子id
		if(!$id) {
			_message("帖子id错误");
		}
		$num=20;
		$total=$this->db->GetCount("select * from `@#_quanzi_tiezi` where `tiezi` = '$id' "); 
		$page=System::load_sys_class('page');
		if(isset($_GET['p'])){
			$pagenum=$_GET['p'];
		}else{$pagenum=1;}		
		$page->config($total,$num,$pagenum,"0"); 
		if($pagenum>$page->page){
			$pagenum=$page->page;			
		}	
		$hueifu=$this->db->GetPage("select * from `@#_quanzi_tiezi` where `tiezi` = '$id'",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));		
		foreach($hueifu as $k => $val) {
			$user = $this->db->GetOne("select username from `@#_member` where uid = '$val[hueiyuan]' limit 1");
			$pid = $this->db->GetOne("select hueiyuan from `@#_quanzi_tiezi` where id = '$val[pid]' limit 1");
			$puser = $this->db->GetOne("select username from `@#_member` where uid = '$pid[hueiyuan]' limit 1");
			$hueifu[$k]['user'] = $user['username'];
			$hueifu[$k]['puser'] = $puser['username'];
		}
		include $this->tpl(ROUTE_M,'quanzi.liuyan');
	}
	
	
	
	/*删除圈子或者帖子或者回复*/
	public function del(){
		$deltype = $this->segment(4);	
		$id=intval($this->segment(5));
		if(!in_array($deltype,array("quanzi","quanzi_tiezi","quanzi_hueifu")) || !$id){
			_message("参数错误!");		
		}
		if($deltype == 'quanzi'){
			$q = $this->db->Query("DELETE FROM `@#_quanzi` where `id`='$id'");
			$q = $this->db->Query("DELETE FROM `@#_quanzi_tiezi` where `qzid`='$id'");	
			//$q = $this->db->Query("DELETE FROM `@#_quanzi_hueifu` where `qzid`='$id'");
		}
		if($deltype == 'quanzi_tiezi'){	
			$this->db->Query("DELETE FROM `@#_quanzi_tiezi` where `id`='$id'");	
			//$this->db->Query("DELETE FROM `@#_quanzi_hueifu` where `tzid`='$id'");
		}
		if($deltype == 'quanzi_hueifu'){		
			$this->db->Query("DELETE FROM `@#_quanzi_tiezi` where `id`='$id'");
		}
		_message("删除成功");
	}
	
	/*帖子或者回复审核*/
	public function shenhe(){
		$id=intval($this->segment(4));
		$q = $this->db->Query("UPDATE `@#_quanzi_tiezi` SET `shenhe` = 'Y' WHERE `id` = '$id'");
		_message("审核成功");
	
	}
}

?>