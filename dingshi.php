<?php
define('G_APP_PATH',dirname(__FILE__).DIRECTORY_SEPARATOR);
$system_path = 'system';
$statics_path = 'statics';
include  G_APP_PATH.$system_path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'global.php';
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
			$res = betopen($issue);
		}
		$bet = $db->GetList("SELECT * FROM `@#_bet` where `issue` = $issue and `returns` = 0");
		$result = explode(',',$res['result']);
		$sum = $result[0]+$result[1];
		$option = array('冠军','亚军','第三名','第四名','第五名','第六名','第七名','第八名','第九名','第十名');
		$result1 = array();
		foreach ($result as $key => $value) {
			$result1[] = $option[$key].$value;
			if($value%2==0)
			{
				$result1[] = $option[$key].双;
			}
			else{
				$result1[] = $option[$key].单;
			}
			if($value >= 5 )
			{
				$result1[] = $option[$key].大;
			}
			else{
				$result1[] = $option[$key].小;
			}

		}
		$result1[] = "冠亚军和".$sum;
		if($sum%2==0)
		{
			$result1[] = "冠亚军和双";
		}
		else
		{
			$result1[] = "冠亚军和单";	
		}
		if($sum >=11)
		{
			$result1[] = "冠亚军和大";
		}
		else
		{
			$result1[] = "冠亚军和小";
		}

		// print_R($result1);die;
		foreach ($bet as $k => $v) {
			// echo $v['name'];die;
			if(in_array($v['name'],$result1))
			{
				// echo 111;die;
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
				echo date("Y-m-d H:i:s").$v['id']."收益".$profit;
			}
			else
			{
				$query1 = $db->Query("UPDATE `@#_bet` SET `returns`='1',`profit`='0' WHERE (`id`='$v[id]')");
				echo date("Y-m-d H:i:s").$v['id']."收益0";
			}
		}
	function betopen($issue = "")
	{

		$db = System::load_sys_class('model');
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
?>