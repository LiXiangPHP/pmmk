<?php

class paoma extends SystemAction {

	//app首页
	public function homepage(){
		$db = System::load_sys_class('model');
		$time = time();
		$nianyue = date("Ymd",$time);
		$qihao = date("Y/m/d",$time);
		// for ($i=2; $i < 12; $i++) { 
		// 	$number1 = "大";
		// 	$number2 = "小";
		// 	$number3 = "单";
		// 	$number4 = "双";
		// 	$odds = '1.00000';
		// 	$sql1="INSERT INTO `@#_option_detail`(number,odds,oid)VALUES('$number1','$odds','$i')";
		// 	$sqlreg = $db->Query($sql1);
		// 	$sql2="INSERT INTO `@#_option_detail`(number,odds,oid)VALUES('$number2','$odds','$i')";
		// 	$sqlreg = $db->Query($sql2);
		// 	$sql3="INSERT INTO `@#_option_detail`(number,odds,oid)VALUES('$number3','$odds','$i')";
		// 	$sqlreg = $db->Query($sql3);
		// 	$sql4="INSERT INTO `@#_option_detail`(number,odds,oid)VALUES('$number4','$odds','$i')";
		// 	$sqlreg = $db->Query($sql4);
			

		// }
		// echo 111;die;
		$issue=$db->GetOne("select issue from `@#_bet_result` WHERE `issue` LIKE '%".$nianyue."%' order by id DESC");
		$issue = $issue['issue'];
		

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
		if($s > 45 )
		{
			$status = 'wait';
			$WaitTime = $s-45;
		}

		if($s >5 && $s<=45)
		{
			$status = 'game';
			$GameTime = $s;
		}
		if(!$GameTime)
		{
			$GameTime = 40;
		}
		if($s < 5 )
		{
			$status = 'prize';
			$PrizeTime = $s;
		}
		else
		{
			$PrizeTime = 5;
		}

		if($issue)
		{
			if($s<45)
			{

				$lastissue1 = substr($issue,8);
				$lastissue = $lastissue1-1;
				$lastissue = $nianyue.$lastissue;
			}
			else
			{
				$lastissue = $issue;
				$lastissue1 = substr($issue,8);
				$issue = $lastissue1+1;
				$issue = $nianyue.$issue;
			}
			
		}
		else
		{
			$lastissue=$db->GetOne("select issue from `@#_bet_result`  order by id DESC");
			$lastissue = $lastissue['issue'];
			$issue = $nianyue."1";
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
			$detail[$v['name']]=$db->GetList("select number,odds from `@#_option_detail` where `oid` = '$v[id]' order by id ASC"  );
			
		}
		$NowResult = '';
		$code = 200;
		

		// print_r($detail);die;

		$data = array('issue'=>$issue,'lastissue'=>$lastissue,'qihao'=>$qihao,'status'=>$status,'WaitTime'=>$WaitTime,'GameTime'=>$GameTime,'detail'=>$detail,'LastResult'=>$LastResult,'sum'=>$sum,'NowResult'=>$NowResult,'NumberDs'=>$NumberDs,'NumberSize'=>$NumberSize,'PrizeTime'=>$PrizeTime);
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
		$time = time();
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
		if($s <=40)
		{
			$code = 100;
			$msg = '不能下注';
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
		// $time =  date("Y/m/d H:i:s",time());
		// $t = substr($time,15,1);
		// $s = (int)substr($time,14,1);
		// $s = $s-1;
		// $d = substr($time,0,14);
		// $q = substr($time,0,15);
		// if($t<5)
		// {
		// 	$time = $d.$s.":00";
		// }
		// if($t>5)
		// {
		// 	$time = $q."5:00";
		// }
		// $time = mktime($time);
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
		$Sdata = $db->GetPage("SELECT result,issue,time  FROM `@#_bet_result` order by id desc ",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
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
			$v['result'] = strtr($v['result'],array('10'=>'0'));
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
	public function betopen($issue = "")
	{


		$db = System::load_sys_class('model');
		// echo 111;die;
		if(!$issue)
		{
			$issue = $_POST['issue'];
			$f = true;
		}
		$bet = $db->GetOne("SELECT * FROM `@#_bet_result` where `issue` = '$issue'");
		if($bet)
		{

			$betresult = explode(',',$bet['result']);
			$bet['result'] = strtr($bet['result'],array('10'=>'0'));
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
			if($f)
			{
				echo json_encode(array('code'=>$code,'sum'=>$sum,'NumberDs'=>$NumberDs,'NumberSize'=>$NumberSize,'result'=>$bet['result']));die;
			}
			else
			{
				return array('code'=>$code,'sum'=>$sum,'NumberDs'=>$NumberDs,'NumberSize'=>$NumberSize,'result'=>$bet['result']);
			}

		}
		elseif(file_exists("log/".$issue.".log"))
		{
			sleep(10);
			$bet = $db->GetOne("SELECT * FROM `@#_bet_result` where `issue` = '$issue'");
			if($bet)
			{

				$betresult = explode(',',$bet['result']);
				$bet['result'] = strtr($bet['result'],array('10'=>'0'));
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
				if($f)
				{
					echo json_encode(array('code'=>$code,'sum'=>$sum,'NumberDs'=>$NumberDs,'NumberSize'=>$NumberSize,'result'=>$bet['result']));die;
				}
				else
				{
					return array('code'=>$code,'sum'=>$sum,'NumberDs'=>$NumberDs,'NumberSize'=>$NumberSize,'result'=>$bet['result']);
				}

			}
		}
		else
		{
			file_put_contents("log/".$issue.".log", $issue);
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
		$bet_set=$db->GetOne("SELECT * FROM `@#_bet_set` ");
		$ying = $bet_set['minmoney']/100;
		$s = 1-$ying;
		// $s = 0.9;
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
			if($p)
			{
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
			elseif(!in_array($ming, $flag['ming']) && $ming!=$vming &&!in_array($ci2, $flag['ci']))
			{
				// print_r($ming);die;
				$aaa[] = $ci2;
				$flag['ming'][] = $ming;
				$flag['ci'][] = $ci2;
			}



		}

		// print_r($aaa);die;
		foreach ($aaa as $k => $v) {
			$result .= $v.",";
			# code...
		}
		$result = rtrim($result, ',');

		$sql="INSERT INTO `@#_bet_result`(result,issue,time)VALUES('$result','$issue','$time')";
		$query = $db->Query($sql);
		$result = strtr($result,array('10'=>'0'));
				// echo $result;die;
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
			if($f)
			{
				echo json_encode(array('code'=>$code,'sum'=>$sum,'NumberDs'=>$NumberDs,'NumberSize'=>$NumberSize,'result'=>$result));die;
			}
			else
			{
				return array('code'=>$code,'sum'=>$sum,'NumberDs'=>$NumberDs,'NumberSize'=>$NumberSize,'result'=>$result);
			}

		}
		else
		{
			$code = "100";
			$msg = '请求错误，请重试';
			if($f)
			{
				echo json_encode(array('code'=>$code,'msg'=>$msg));die;
			}
			else
			{
				return array('code'=>$code,'msg'=>$msg);
			}
			
		}

	}
	//定时任务
	function returns()
	{
		$db = System::load_sys_class('model');
		$bet = $db->GetOne("SELECT * FROM `@#_bet` where `returns` = 0 order by time desc");
		$issue = $bet['issue'];
		$bet_result = $db->GetOne("SELECT * FROM `@#_bet_result` where `issue` = $issue");
		if($bet_result)
		{
			$res = $bet_result;
		}
		else
		{
			$res = $this->betopen($issue);
		}
		$bet = $db->GetList("SELECT * FROM `@#_bet` where `issue` = $issue and `returns` = 0");
		$result = explode(',',$res['result']);
		$sum = $result[0]+$result[1];
		$option = array('冠军','亚军','第三名','第四名','第五名','第六名','第七名','第八名','第九名','第十名');
		$result1 = array();
		foreach ($result as $key => $value) {
			$result1[] = $option[$key].$value;
		}
		$result1[] = "冠亚军和".$sum;
		foreach ($bet as $k => $v) {
			if(in_array($v['name'],$result1))
			{
				$profit = $v['number']*$v['odds'];
				$members = $db->GetOne("SELECT * FROM `@#_member` where `uid` = '$v[uid]'");
				$db->Autocommit_start();
				$Money = $members['money'] + $profit;
				$query = $db->Query("UPDATE `@#_member` SET `money`='$Money' WHERE (`uid`='$v[uid]')");
				$query1 = $db->Query("UPDATE `@#_bet` SET `returns`='1',`profit`='$profit' WHERE (`id`='$v[id]')");
				if(!$query1)
				{
					$db->Autocommit_rollback();
				}
				$db->Autocommit_commit();
			}
			else
			{
				$query1 = $db->Query("UPDATE `@#_bet` SET `returns`='1',`profit`='0' WHERE (`id`='$v[id]')");
			}
		}

	}
	function profit()
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
		$uid = $info['uid'];
		// $uid = 694;
		$code = 200;
		$user_bet = $db->GetList("SELECT sum(profit) as sumprofit ,issue,sum(number) as sumnumber  FROM `@#_bet` where `uid` = $uid  group by `issue` order by id desc");

		echo json_encode(array('code'=>$code,'data'=>$user_bet));die;

	}
	function profit_issue()
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
		$uid = $info['uid'];
		$issue = $_POST['issue'];
		if($issue)
		{
			$user_bet = $db->GetList("SELECT * FROM `@#_bet` where `uid` = $uid  and `issue` = $issue order by id DESC");
		}
		else
		{
			$pagenum = $_POST['p'];
			$total = $db->GetCount("SELECT * FROM `@#_bet` where `uid` = $uid order by id DESC");
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
			$user_bet = $db->GetPage("SELECT * FROM `@#_bet` where `uid` = $uid order by id DESC",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
		
			// if($user_bet) {
			// 	$user_bet['ptotal'] = $yeshu;
			// }

			 // print_r($user_bet);die;
		}
			

			foreach ($user_bet as $k => $v) {
				$result[]  = array("issue"=>$v['issue'],"name"=>$v['name'],"number"=>$v['number'],"profit"=>$v['profit']);
		}
		if($yeshu)
		{
			$result['ptotal'] = $yeshu;
		}
		$code = 200;
		echo json_encode(array('code'=>$code,'data'=>$result));die;
	}
}
?>