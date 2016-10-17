<?php 
defined('G_IN_SYSTEM')or exit('no');
System::load_app_class('admin',G_ADMIN_DIR,'no');
System::load_app_fun('global',G_ADMIN_DIR);
System::load_sys_fun('user');
class app extends admin {
	private $db;
	public function __construct(){		
		parent::__construct();		
		$this->db=System::load_sys_class('model');
		$this->ment=array();
		$this->categorys=$this->db->GetList("SELECT * FROM `@#_category` WHERE 1 order by `parentid` ASC,`cateid` ASC",array('key'=>'cateid'));
		$this->models=$this->db->GetList("SELECT * FROM `@#_model` WHERE 1",array('key'=>'modelid'));

	}


	public function update() {
		// $info=$this->AdminInfo;
		// print_r($info);die;
		// if(!$info['neirong'])
		// {
		// 	echo '没有权限！';die;
		// }
		$Alist = $this->db->GetList("select * from `@#_edition`");
		if(isset($_POST['submit'])){
			
			unset($_POST['submit']);
			$con = $_POST;
			$res = $this->db->Query("UPDATE `@#_edition` SET `banben` = '$con[banben]', `gengxinurl` = '$con[aurl]' WHERE `id` = '$con[id]' ");
			
			if(!$res){
				_message("更新失败");
			}
			_message("更新成功");
		}
		include $this->tpl(ROUTE_M,'app.update');
	}
}
?>