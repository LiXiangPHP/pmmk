<?php
defined('G_IN_SYSTEM')or exit('no');
System::load_app_class('admin',G_ADMIN_DIR,'no');
System::load_app_fun('global',G_ADMIN_DIR);
System::load_sys_fun('user');
class help extends admin {
	private $db;
	public function __construct(){		
		parent::__construct();
		$this->ment=array(
			array("lists","帮助管理",ROUTE_M.'/'.ROUTE_C."/help_show"),
			array("listsd","待回复",ROUTE_M.'/'.ROUTE_C."/help_showsd"),
			array("add","添加热门问题",ROUTE_M.'/'.ROUTE_C."/help_add"),
		);		
		$this->db=System::load_sys_class('model');
		$this->categorys=$this->db->GetList("SELECT * FROM `@#_category` WHERE 1 order by `parentid` ASC,`cateid` ASC",array('key'=>'cateid'));
		$this->models=$this->db->GetList("SELECT * FROM `@#_model` WHERE 1",array('key'=>'modelid'));

	}
	

	
	/*帮助模块-会员问题查看*/	
	public function help_show() {
		$info=$this->AdminInfo;
		// print_r($info);die;
		if(!$info['neirong'])
		{
			echo '没有权限！';die;
		}
		
		$list_where = "`is_delete` = 0 and `is_check` = 1";
		$num=20;
		$total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_help` WHERE $list_where");
		$page=System::load_sys_class('page');
		if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}	
		$page->config($total,$num,$pagenum,"0");
		$helplist=$this->db->GetPage("SELECT id,uid,issue,reply,times,hot FROM `@#_help` WHERE $list_where order by id desc",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
	
		foreach($helplist as $k => $v) {
			if(strlen($v['issue']) > 15) {
				for ($i = 0; $i < 15;$i++) {
					if(ord($v['issue'][$i]) > 128) $i++;
					 $helplist[$k]['issue'] = substr($v['issue'],0,$i)."...";
				}
			}
			if(strlen($v['reply']) > 15) {
				for ($i = 0; $i < 15;$i++) {
					if(ord($v['reply'][$i]) > 128) $i++;
					 $helplist[$k]['reply'] = substr($v['reply'],0,$i)."...";
				}
			}
			$arr = $this->db->GetOne("SELECT mobile FROM `@#_member` where `uid`='$v[uid]' LIMIT 1");
			if($arr) {
				$helplist[$k]['username'] = $arr['mobile'];
			}else {
				$helplist[$k]['username'] = '管理员';
			}
		}
		include $this->tpl(ROUTE_M,'help.lists');
	}

	public function help_showsd() {
		$info=$this->AdminInfo;
		// print_r($info);die;
		if(!$info['neirong'])
		{
			echo '没有权限！';die;
		}
		
		$list_where = "`is_delete` = 0 and `is_check` = 0";
		$num=20;
		$total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_help` WHERE $list_where");
		$page=System::load_sys_class('page');
		if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}	
		$page->config($total,$num,$pagenum,"0");
		$helplist=$this->db->GetPage("SELECT id,uid,issue,times,hot FROM `@#_help` WHERE $list_where order by id desc",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
	
		foreach($helplist as $k => $v) {
			if(strlen($v['issue']) > 15) {
				for ($i = 0; $i < 15;$i++) {
					if(ord($v['issue'][$i]) > 128) $i++;
					 $helplist[$k]['issue'] = substr($v['issue'],0,$i)."...";
				}
			}
			$arr = $this->db->GetOne("SELECT mobile FROM `@#_member` where `uid`='$v[uid]' LIMIT 1");
			if($arr) {
				$helplist[$k]['username'] = $arr['mobile'];
			}else {
				$helplist[$k]['username'] = '管理员';
			}
		}
		include $this->tpl(ROUTE_M,'help.listsd');
	}

	/*帮助模块-热门问题添加*/
	public function help_add() {
		$info=$this->AdminInfo;
		// print_r($info);die;
		if(!$info['neirong'])
		{
			echo '没有权限！';die;
		}
		if(isset($_POST['dosubmit'])){
			$description = htmlspecialchars($_POST['description']);
			$detail = htmlspecialchars($_POST['detail']);
			$time = date('Y-m-d h:i:s');
			$sql="insert into `@#_help` (`issue`,`reply`,`times`,`is_check`,`hot`)  value('$description','$detail','$time',1 ,1)";
			$this->db->Query($sql);
			
			if($this->db->affected_rows()){
				_message("操作成功!");
			}else{
				_message("操作失败!");
			}
			header("Cache-control: private");
		}
		include $this->tpl(ROUTE_M,'help.add');
	}
	
	/*帮助模块-会员问题删除（记录不存在）*/
	public function help_del() {
		$info=$this->AdminInfo;
		// print_r($info);die;
		if(!$info['neirong'])
		{
			echo '没有权限！';die;
		}
		$id=intval($this->segment(4));		
		$this->db->Query("DELETE FROM `@#_help` WHERE (`id`='$id') LIMIT 1");
		if($this->db->affected_rows()){	
			echo WEB_PATH.'/'.ROUTE_M.'/help/help_show';
		}else{
			echo "no";
		}
	}
	
	/*帮助模块-会员问题删除（记录不存在）*/
	public function help_dels() {
		$info=$this->AdminInfo;
		// print_r($info);die;
		if(!$info['neirong'])
		{
			echo '没有权限！';die;
		}
		$id=intval($this->segment(4));		
		$this->db->Query("DELETE FROM `@#_help` WHERE (`id`='$id') LIMIT 1");
		if($this->db->affected_rows()){	
			echo WEB_PATH.'/'.ROUTE_M.'/help/help_dus';
		}else{
			echo "no";
		}
	}
	
	/*帮助模块-会员问题删除（记录存在）*/
	public function help_delall() {
		$info=$this->AdminInfo;
		// print_r($info);die;
		if(!$info['neirong'])
		{
			echo '没有权限！';die;
		}
		if($_POST['hcheck']) {
			$ids=implode(',',$_POST['hcheck']);
		}else {
			_message("请选择记录");
		}
		$sql = "UPDATE `@#_help` SET `is_delete` = 1 WHERE (`id` in ('$ids'))";
		if($this->db->Query($sql)) {
			$this->db->Autocommit_commit();	
			_message("删除成功!");
		}else {
			$this->db->Autocommit_rollback();
			_message("删除失败!");
		}
	}
	
	/*帮助模块-会员已删除问题查看*/
	public function help_dus() {
		$info=$this->AdminInfo;
		// print_r($info);die;
		if(!$info['neirong'])
		{
			echo '没有权限！';die;
		}
		$this->ment=array(
			array("lists","帮助管理",ROUTE_M.'/'.ROUTE_C."/help_list"),
		);
		
		$list_where = "`is_delete` = 1";
		$num=20;
		$total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_help` WHERE $list_where");
		$page=System::load_sys_class('page');
		if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}	
		$page->config($total,$num,$pagenum,"0");
		$helplist=$this->db->GetPage("SELECT id,a.uid,issue,reply,is_check,times,username FROM `@#_help` a,`@#_member` b WHERE $list_where AND a.uid = b.uid",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
		include $this->tpl(ROUTE_M,'help.dus');
	}
	
	/*帮助模块-会员问题回复*/
	public function help_reply() {
		$info=$this->AdminInfo;
		// print_r($info);die;
		if(!$info['neirong'])
		{
			echo '没有权限！';die;
		}
		
		$id=intval($this->segment(4));	
		$info=$this->db->GetOne("SELECT * FROM `@#_help` where `id`='$id' LIMIT 1");
		if(!$info)_message("参数错误");
		if(isset($_POST['dosubmit'])){
			$description = htmlspecialchars($_POST['description']);			
			
			$sql="UPDATE `@#_help` SET  `reply` = '$description',
										`is_check` = 1 
										WHERE (`id`='$id')";
			$this->db->Query($sql);
			
			if($this->db->affected_rows()){
				_message("操作成功!");
			}else{
				_message("操作失败!");
			}
			header("Cache-control: private");
		}
		include $this->tpl(ROUTE_M,'help.reply');
		
	}

	/*帮助模块--热门问题查看*/
	public function help_shows() {
		$info=$this->AdminInfo;
		// print_r($info);die;
		if(!$info['neirong'])
		{
			echo '没有权限！';die;
		}
		$id=intval($this->segment(4));	
		$info=$this->db->GetOne("SELECT * FROM `@#_help` where `id`='$id' LIMIT 1");
		if(!$info)_message("参数错误");	

		include $this->tpl(ROUTE_M,'help.hlists');
	}





	
}












?>