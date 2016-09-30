<?php

class paoma extends SystemAction {

	//app首页
	public function homepage(){
		$db = System::load_sys_class('model');
		$time = time();
		$nianyue = date("Ymd",$time);
		$qihao = date("Y/m/d",$time);
		$issue=$db->GetOne("select issue from `@#_bet` WHERE `issue` LIKE '%".$nianyue."%' order by issue DESC");
		$issue = $issue['issue'];
		if($issue)
		{
			$lastissue = $issue;
			$issue = $lastissue+1;
		}
		else
		{
			$lastissue=$db->GetOne("select issue from `@#_bet`  order by issue DESC");
			$lastissue = $lastissue['issue'];
			$issue = $nianyue."1";
		}
		$aa = date("h:i:s",$time);
		// echo $time.'|';
		//初始时间
		$chushi = date("Y-m-d 00:00:00",$time);
		$Endtime=$chushi;
		$Endtime=strtotime($Endtime);
		//现在时间过去多少秒
		$sytime = $time-$Endtime;
		// echo $Endtime;die;
		// 有多少个5分钟
		$ge = intval(floor($sytime/300));
		//剩余多少秒
		$s = 300-($sytime - $ge*300);
		$WaitTime = 0;
		
		if($s > 140 )
		{
			$status = 'wait';
			$WaitTime = $s-125;
		}
		if( $s >125 && $s<=140)
		{
			$status = 'no';
			$WaitTime = $s-125;
		}
		if($s >5 && $s<=125)
		{
			$status = 'game';
			$GameTime = $s;
		}
		if(!$GameTime)
		{
			$GameTime = 120;
		}
		if($s < 5 )
		{
			$status = 'prize';
		}
		$LastResult1=$db->GetOne("select result from `@#_bet_result` WHERE `issue` = '$lastissue'");
		$LastResult = explode(',',$LastResult1['result']);
		$sum = $LastResult[0]+$LastResult[1];
		if((int)$sum%2 == 0){
			$NumberDs = "双";
		}
		else
		{
			$NumberDs = "单";
		}
		if((int)$sum>=11)
		{
			$NumberSize = '大';
		}
		if((int)$sum<=10)
		{
			$NumberSize = '小';

		}
		// echo $LastResult;die;
		$LastResult = $LastResult1['result'];
		$option=$db->GetList("select * from `@#_option`");
		foreach ($option as $k => $v) {
			// print_r($v);die;
			$detail[$v['name']]=$db->GetList("select number,odds from `@#_option_detail` where `oid` = '$v[id]' order by number ASC"  );
			
		}
		$NowResult = '';
		$code = 200;
		



		$data = array('issue'=>$issue,'lastissue'=>$lastissue,'qihao'=>$qihao,'status'=>$status,'WaitTime'=>$WaitTime,'GameTime'=>$GameTime,'detail'=>$detail,'LastResult'=>$LastResult,'sum'=>$sum,'NowResult'=>$NowResult,'NumberDs'=>$NumberDs,'NumberSize'=>$NumberSize);
		$json = array('code' => $code,'data'=>$data);
		echo json_encode($json);


		
	}
	public function bet()
	{
		if(!$_POST)
		{
			$code = 100;
			$msg = '错误';
			echo json_encode(array("code"=>$code,"msg"=>$msg));die;
		}
		$db = System::load_sys_class('model');
		$token = isset($_POST['token']) ? $_POST['token'] : "kong";
		$info = System::token_uid($token);
		if(!$info['uid'])
		{
			$code = 100;
			$msg = '请登录';
			echo json_encode(array("code"=>$code,"msg"=>$msg));die;
		}
		$bet = stripslashes($_POST['bet']);
		// $arr = array('冠军'=>array("2"=>"2","3"=>"11"),'亚军'=>array("2"=>"2"));
		// echo json_encode($arr);die;
		// $bet = '{"\u51a0\u519b":{"1":"2","2":"11","3":"11","4":"11","5":"11","6":"11","7":"11","8":"11","9":"11","10":"11"},"\u4e9a\u519b":{"1":"2","2":"11","3":"11","4":"11","5":"11","6":"11","7":"11","8":"11","9":"11","10":"11"},"\u7b2c\u4e09\u540d":{"1":"2","2":"11","3":"11","4":"11","5":"11","6":"11","7":"11","8":"11","9":"11","10":"11"},"\u7b2c\u56db\u540d":{"1":"2","2":"11","3":"11","4":"11","5":"11","6":"11","7":"11","8":"11","9":"11","10":"11"},"\u7b2c\u4e94\u540d":{"1":"2","2":"11","3":"11","4":"11","5":"11","6":"11","7":"11","8":"11","9":"11","10":"11"},"\u7b2c\u516d\u540d":{"1":"2","2":"11","3":"11","4":"11","5":"11","6":"11","7":"11","8":"11","9":"11","10":"11"},"\u7b2c\u4e03\u540d":{"1":"2","2":"11","3":"11","4":"11","5":"11","6":"11","7":"11","8":"11","9":"11","10":"11"},"\u7b2c\u516b\u540d":{"1":"2","2":"11","3":"11","4":"11","5":"11","6":"11","7":"11","8":"11","9":"11","10":"11"},"\u7b2c\u4e5d\u540d":{"1":"2","2":"11","3":"11","4":"11","5":"11","6":"11","7":"11","8":"11","9":"11","10":"11"},"\u7b2c\u5341\u540d":{"1":"2","2":"11","3":"11","4":"11","5":"11","6":"11","7":"11","8":"11","9":"11","10":"11"},"\u51a0\u4e9a\u519b\u548c":{"3":"11","4":"11","5":"11","6":"11","7":"11","8":"11","9":"11","10":"11","11":"11","12":"11","13":"11","14":"11","15":"11","16":"11","17":"11","18":"11","19":"11"}}';
		
		$bet = json_decode($bet);
		$number = 0;
		// print_r($bet);die;
		foreach ($bet as $key => $value) {
			$oid = $db->GetOne("select id from `@#_option` where `name` = '$key'");
			$oid = $oid['id'];
			foreach ($value as $k => $v) {
				$number = $value->$k;
				$n+=$number;
				$t = $db->GetOne("select id,odds from `@#_option_detail` where `oid` = '$oid' and `number` = '$k'");
				$name = $key.$k;
				$did = $t['id'];
				$odds = $t['odds'];
				$issue = $_POST['issue'];
				$uid = $info['uid'];
				$number = $v;
				$time = $qihao = date("Y-m-d H:i:s",time());
				$sql[]="INSERT INTO `@#_bet`(name,did,odds,issue,uid,number,time)VALUES('$name','$did','$odds','$issue','$uid','$number','$time')";


			}
		}
		$members = $db->GetOne("SELECT * FROM `@#_member` where `uid` = '$uid' for update");
		if($members['money'] >= $n)
		{
			$db->Autocommit_start();
			$Money = $members['money'] - $n;
			$query = $db->Query("UPDATE `@#_member` SET `money`='$Money' WHERE (`uid`='$uid')");
			foreach ($sql as $k => $v) {
				$sqlreg = $db->Query($v);
				if(!$sqlreg)
				{
					$db->Autocommit_rollback();
					$code = 100;
					$msg = '购买失败';
					echo json_encode(array("code"=>$code,"msg"=>$msg));die;
					
				}
			}
			$db->Autocommit_commit();
			$code = 200;
			$msg = '购买成功';
			echo json_encode(array("code"=>$code,"msg"=>$msg));die;

		}
		else{
			$code = 100;
			$msg = '账户余额不足，请充值';
			echo json_encode(array("code"=>$code,"msg"=>$msg));die;
		}
				
		

	}
	public function betlog()
	{
		$db = System::load_sys_class('model');
		$pagenum = $_POST['p'];
		$total = $db->GetCount("SELECT * FROM `@#_bet_result` ");
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
		$Sdata = $db->GetPage("SELECT result,issue,time  FROM `@#_bet_result` ",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
		foreach($Sdata as $v) {
			
			$result=  $v['result'];
			$result = explode(',',$result);
			$sum = $result[0]+$result[1];
			if((int)$sum%2 == 0){
				$NumberDs = "双";
			}
			else
			{
				$NumberDs = "单";
			}
			if((int)$sum>=11)
			{
				$NumberSize = '大';
			}
			if((int)$sum<=10)
			{
				$NumberSize = '小';

			}
			$v['sum'] = $sum;
			$v['NumberDs'] = $NumberDs;
			$v['NumberSize'] = $NumberSize;
			$data['data'][] = $v;
		}
		if($data['data']) {
			$data['ptotal'] = $yeshu;
		}
		if($data)
		{
			$code = 200;
			$json = array('code' => $code, 'data' => $data);
			echo json_encode($json);
		}
		else
		{
			$code = 100;
			$msg = "数据为空";
			$json = array('code' => $code, 'msg'=>$msg, 'data' => $data);
			echo json_encode($json);
		}
	}
	public function betopen()
	{
		$db = System::load_sys_class('model');

		$issue = $_POST['issue'];

		$bet = $db->GetOne("SELECT * FROM `@#_bet_result` where `issue` = '$issue'");
		if($bet)
		{
			$betresult = explode(',',$bet['result']);
			$code = "200";
			$sum = $betresult[0]+$betresult[1];
			if((int)$sum%2 == 0){
				$NumberDs = "双";
			}
			else
			{
				$NumberDs = "单";
			}
			if((int)$sum>=11)
			{
				$NumberSize = '大';
			}
			if((int)$sum<=10)
			{
				$NumberSize = '小';

			}
			echo json_encode(array('code'=>$code,'sum'=>$sum,'NumberDs'=>$NumberDs,'NumberSize'=>$NumberSize,'result'=>$bet['result']));die;

		}
		$option = array(array("id"=>2,"name"=>'冠军'),array("id"=>3,"name"=>'亚军'),array("id"=>4,"name"=>'第三名'),array("id"=>5,"name"=>'第四名'),array("id"=>6,"name"=>'第五名'),array("id"=>7,"name"=>'第六名'),array("id"=>8,"name"=>'第七名'),array("id"=>9,"name"=>'第八名'),array("id"=>10,"name"=>'第九名'),array("id"=>11,"name"=>'第十名'));
		$number = array('1','2','3','4','5','6','7','8','9','10');
		$time = date("Y/m/d",time());
		$hao = rand(0,9);
		$guan1= $option[0]['name'].$number[$hao];
		if($hao <=5)
		{
			$hao1 = rand($hao+1,9);
		}
		else
		{
			$hao1 = rand(0,$hao-1);
		}
		

		$ya1 = $option[1]['name'].$number[$hao1];
		$guanci = $number[$hao];
		$yaci = $number[$hao1];
		unset($option[0]);
		unset($option[1]);
		unset($number[$hao1]);unset($number[$hao]);
		// print_r($number);die;
		foreach ($option as $key => $value) {
			
				foreach ($number as $k => $v) {
					$o[] = $value['name'].$v;
				}
		}
		// print_r($o);die;
		
		$bet = $db->GetList("SELECT * FROM `@#_bet` where `issue` = '$issue'");
		$guan = $db->GetOne("SELECT * FROM `@#_bet` where `issue` = '$issue' and name = '$guan1'");
		$ya = $db->GetOne("SELECT * FROM `@#_bet` where `issue` = '$issue' and name = '$ya1'");
		$guan = $guan['odds']*$guan['number'];
		$ya = $ya['odds']*$ya['number'];
		$detail = $db->GetList("SELECT * FROM `@#_option_detail` ");
		foreach ($bet as $k => $v) {
			$n+=$v['number'];
			$pei = $db->GetOne("SELECT * FROM `@#_option_detail` where `id` = '$v[did]'");
			$p[$v['id']]= array("did"=>$v['did'],"p"=>$pei['odds']*$v['number'],"name"=>$v['name']);
			
		}
		
		$s = 0.9;
		$sheng = intval($n*$s);
		$sheng = $sheng-$guan-$ya;
		// echo $sheng;die;
		$m = intval($sheng/80);
		// print_r($detail);die;
		// echo $m;die;
		// print_r($o);die;
		// print_r($p);die;
		$aaa = array(mb_substr($guan1,2),mb_substr($ya1,2));
		$flag['ci'] = array($guanci,$yaci);
		
		foreach ($o as $key => $value) {
			$ming = mb_substr($value , 0 , 3);
			$ci = mb_substr($value , 3);
			$ci1 = rand(1,10);
			if(!in_array($ci1, $flag['ci']))
			{
				$ci2 = $ci1;
			}
			else{
				$ci2 = $ci;
			}
			foreach ($p as $k => $v) {
				$vming = mb_substr($v['name'] , 0 , 3);
				if($v['name'] == $value)
				{
					// print_r($flag);die;
					// print_r($flag['ming']);
					// print_r( $flag['ci']);
					// echo $value;
					// print_r($flag['ci']);die;
					if($v['p'] < $m && !in_array($ming, $flag['ming']) && !in_array($ci, $flag['ci']))
					{
						// print_r($o[$key]);die;
						
						// echo mb_substr($v['name'] , 0 , 3);die;
						$aaa[] = $ci;
						$flag['ming'][] = $ming;
						$flag['ci'][] = $ci;

					}
					else
					{
						// print_r($value);die;
						 // echo $v['name']; 
						 // echo $ci."|";
						break;
					}
					
				}
				elseif(!in_array($ming, $flag['ming']) && $ming!=$vming &&!in_array($ci2, $flag['ci']))
				{
					// print_r($ming);die;
					$aaa[] = $ci2;
					$flag['ming'][] = $ming;
					$flag['ci'][] = $ci2;
				}
				

			}
		}
		foreach ($aaa as $k => $v) {
			$result .= $v.",";
			# code...
		}
		$result = rtrim($result, ',');
		$sql="INSERT INTO `@#_bet_result`(result,issue,time)VALUES('$result','$issue','$time')";
		$query = $db->Query($sql);
		if($query){

			$code = "200";
			$sum = $aaa[0]+$aaa[1];
			if((int)$sum%2 == 0){
				$NumberDs = "双";
			}
			else
			{
				$NumberDs = "单";
			}
			if((int)$sum>=11)
			{
				$NumberSize = '大';
			}
			if((int)$sum<=10)
			{
				$NumberSize = '小';

			}
			echo json_encode(array('code'=>$code,'sum'=>$sum,'NumberDs'=>$NumberDs,'NumberSize'=>$NumberSize,'result'=>$result));die;

		}
		else
		{
			$code = "100";
			$msg = '请求错误，请重试';
			echo json_encode(array('code'=>$code,'msg'=>$msg));die;
		}

	}
}
?>