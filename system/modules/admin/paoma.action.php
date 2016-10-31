<?php 
defined('G_IN_SYSTEM')or exit('no');
System::load_app_class('admin',G_ADMIN_DIR,'no');
System::load_app_fun('global',G_ADMIN_DIR);
System::load_sys_fun('user');
class paoma extends admin {
	private $db;
	public function __construct(){

		
		parent::__construct();		
		$this->db=System::load_sys_class('model');
		$this->ment=array();
		$this->categorys=$this->db->GetList("SELECT * FROM `@#_category` WHERE 1 order by `parentid` ASC,`cateid` ASC",array('key'=>'cateid'));
		$this->models=$this->db->GetList("SELECT * FROM `@#_model` WHERE 1",array('key'=>'modelid'));

	}
	public function show()
	{
		// $info=$this->AdminInfo;
		// // print_r($info);die;
		// if(!$info['shop'])
		// {
		// 	echo '没有权限！';die;
		// }
		$option=$this->db->GetList("SELECT * FROM `@#_option` ");
		$this->ment=array();
		foreach ($option as $k => $v) {
			array_push($this->ment,array("",$v['name'],ROUTE_M.'/'.ROUTE_C."/show/".$v['id']));
			# code...
		}
		// print_r($this->ment);die;
		// $this->ment=array(
		// 				array("lists","冠亚军和",ROUTE_M.'/'.ROUTE_C."/show/1"),
		// 				array("add","冠亚军和",ROUTE_M.'/'.ROUTE_C."/goods_add"),
		// 				array("renqi","人气商品",ROUTE_M.'/'.ROUTE_C."/goods_list/renqi"),
		// 				array("xsjx","限时揭晓商品",ROUTE_M.'/'.ROUTE_C."/goods_list/xianshi"),
		// 				array("qishu","期数倒序",ROUTE_M.'/'.ROUTE_C."/goods_list/qishu"),
		// 				array("danjia","单价倒序",ROUTE_M.'/'.ROUTE_C."/goods_list/danjia"),
		// 				array("money","商品价格倒序",ROUTE_M.'/'.ROUTE_C."/goods_list/money"),
		// 				array("money","已揭晓",ROUTE_M.'/'.ROUTE_C."/goods_list/jiexiaook"),
		// 				array("money","<font color='#f00'>期数已满商品</font>",ROUTE_M.'/'.ROUTE_C."/goods_list/maxqishu"),
		// );		
	    $id=$this->segment(4);
		if(!$id)
		{
			$id = 1;
		}
		$option_list=$this->db->GetList("SELECT * FROM `@#_option_detail` WHERE `oid`='$id' order by `number` ASC");
		 // print_R($option_list);die;
		include $this->tpl(ROUTE_M,'paoma.lists');
	}

	public function update_odds()
	{
		$odds = $_POST['odds'];
		$id = $_POST['id'];
		$sql="UPDATE `@#_option_detail` SET `odds`='$odds' WHERE (`id`='$id')";
		if($this->db->Query($sql))
		{
			echo  1;die;
		}
		else
		{
			echo  0;die;
		}

	}
	public function betlist()
	{
		// $info=$this->AdminInfo;
		// // print_r($info);die;
		// if(!$info['shop'])
		// {
		// 	echo '没有权限！';die;
		// }
		$this->ment=array(
						array("lists","下注管理",ROUTE_M.'/'.ROUTE_C."/betlist"),
						array("qishu","期数倒序",ROUTE_M.'/'.ROUTE_C."/betlist/qishu"),
						array("money","期数正序",ROUTE_M.'/'.ROUTE_C."/betlist/qishuasc"),
						array("money","赔率倒序",ROUTE_M.'/'.ROUTE_C."/betlist/peilv"),
						array("money","赔率正序",ROUTE_M.'/'.ROUTE_C."/betlist/peilvasc"),
					
		);		
	    $cateid=$this->segment(4);
	    $issue=$this->db->GetOne("SELECT issue FROM `@#_bet` order by `issue` DESC limit 1");
		$issue = $issue['issue'];
		$list_where = "issue = ".$issue;
		if($cateid){

			if($cateid=='qishu'){
				$list_where .= " order by `issue` DESC";
			}
			if($cateid=='qishuasc'){
				$list_where .= " order by `issue` ASC";
			}
			if($cateid=='peilv'){
				$list_where .= " order by `odds` DESC";
			}
			if($cateid=='peilvasc'){
				$list_where .= " order by `odds` ASC";
			}
			if($cateid==''){
				$list_where .= "  order by `time` DESC";
			}
					
		}else{
			$list_where .= " order by `time` DESC";
		}		
		// echo $list_where;die;
		
		$num=20;
		$total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_bet` WHERE $list_where"); 
		$page=System::load_sys_class('page');
		if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}	
		$page->config($total,$num,$pagenum,"0");
		$betlist=$this->db->GetPage("SELECT * FROM `@#_bet` WHERE $list_where",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0,));
		include $this->tpl(ROUTE_M,'bet.lists');

	}
	public function lastshow()
	{
		$this->ment=array(
						array("lists","下注管理",ROUTE_M.'/'.ROUTE_C."/lastshow"),
						array("qishu","期数倒序",ROUTE_M.'/'.ROUTE_C."/lastshow/qishu"),
						array("money","期数正序",ROUTE_M.'/'.ROUTE_C."/lastshow/qishuasc"),
						array("money","赔率倒序",ROUTE_M.'/'.ROUTE_C."/lastshow/peilv"),
						array("money","赔率正序",ROUTE_M.'/'.ROUTE_C."/lastshow/peilvasc"),
					
		);		
	    $cateid=$this->segment(4);
	 //    $issue=$this->db->GetOne("SELECT issue FROM `@#_bet` order by `issue` DESC limit 1");
		// $issue = $issue['issue'];
		$list_where = "1 = 1";
		if($cateid){

			if($cateid=='qishu'){
				$list_where .= " order by `issue` DESC";
			}
			if($cateid=='qishuasc'){
				$list_where .= " order by `issue` ASC";
			}
			if($cateid=='peilv'){
				$list_where .= " order by `odds` DESC";
			}
			if($cateid=='peilvasc'){
				$list_where .= " order by `odds` ASC";
			}
			if($cateid==''){
				$list_where .= "  order by `time` DESC";
			}
					
		}else{
			$list_where .= " order by `time` DESC";
		}		
		// echo $list_where;die;
		
		$num=20;
		$total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_bet` WHERE $list_where"); 
		$page=System::load_sys_class('page');
		if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}	
		$page->config($total,$num,$pagenum,"0");
		$betlist=$this->db->GetPage("SELECT * FROM `@#_bet` WHERE $list_where",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0,));
		// print_r($total);die;
		include $this->tpl(ROUTE_M,'bet.lists');
	}
	public function bet_del()
	{
		$id=$this->segment(4);
		$this->db->Query("DELETE FROM `@#_bet` WHERE (`id`='$id') LIMIT 1");
			if($this->db->affected_rows()){			
				_message("删除成功!请刷新页面");
			}else{
				_message("删除失败!");
			}	
	}
	public function betset()
	{
		$bet_set=$this->db->GetOne("SELECT * FROM `@#_bet_set` ");
		 // print_R($bet_set);die;
		include $this->tpl(ROUTE_M,'bet.set');
	}
	public function update_set()
	{
		$minmoney = $_POST['minmoney'];
		$id = $_POST['id'];
		$sql="UPDATE `@#_bet_set` SET `minmoney`='$minmoney' WHERE (`id`='$id')";
		if($this->db->Query($sql))
		{
			echo  1;die;
		}
		else
		{
			echo  0;die;
		}

	}
	
}