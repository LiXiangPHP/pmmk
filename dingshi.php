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
			if($issue)
			{
				$res = betopen($issue);
			}
			else
			{
				/***************************/
				$time = time();
				$nianyue = date("Ymd",$time);
				$star =  strtotime(date('Y-m-d',strtotime('+0 day')));
				$now =  time();
				$str = $now - $star;
				$qishu = $nianyue.floor($str/300)-1;
				$data=$db->GetOne("select issue from `@#_bet_result` WHERE `issue` = $qishu");
				if(!$data){
					$time = time();
					$nianyue = date("Ymd",$time);
					// echo $nianyue;die;
					$issue=$db->GetOne("select issue from `@#_bet_result` WHERE `issue` LIKE '%".$nianyue."%' order by id DESC");
					$issue = $issue['issue'];
					$lastissue1 = substr($issue,8);

					$issue = $lastissue1+1;
					$issue = $nianyue.$issue;
					$res = betopen($issue);
				}
				/**************************/	
			}
			
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
			if($value > 5 )
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
		if($sum > 11)
		{
			$result1[] = "冠亚军和大";
		}
		else
		{
			$result1[] = "冠亚军和小";
		}
		if($sum == 11)
		{
			$result1[] = "冠亚军和双";
			$result1[] = "冠亚军和大";
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
			if((int)$sum == 11)
			{
				$NumberDs = "和";
				$NumberSize = '和';
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
				if((int)$sum == 11)
			{
				$NumberDs = "和";
				$NumberSize = '和';
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
		
		
		$bet = $db->GetList("SELECT * FROM `@#_bet` where `issue` = '$issue'");
		$i=0;
		foreach ($bet as $k => $v) {
			$i++;
			$n+=$v['number'];
			$pei = $db->GetOne("SELECT * FROM `@#_option_detail` where `id` = '$v[did]'");
			$p[$v['id']]= array("did"=>$v['did'],"p"=>$pei['odds']*$v['number'],"name"=>$v['name']);
			
		}
		$bet_set=$db->GetOne("SELECT * FROM `@#_bet_set` ");
		$ying = $bet_set['minmoney']/100;
		$s = 1-$ying;
		// $s = 0.9;
		$sheng = intval($n*$s);
		$m = intval($sheng/$i);
		// print_r($detail);die;
		// print_r($o);die;
		// print_r($p);die;
		

		foreach ($p as $key => $value) {
			if(strlen($value['name'])>8)
			{
				$ming  = mb_substr($value['name'] ,  0,3);
			}
			else
			{
				$ming = mb_substr($value['name'],0,2);
			}
			$ci = mb_substr($value['name'] ,  3);
			if(!$ci)
			{
				$ci = mb_substr($value['name'],2);
			}
			// echo $m;die;
			if($value['p'] > $m)
			{
				$arr[$ming][] = $ci;
			}

		}

		foreach ($arr as $key => $value) {
				for ($iz=0; $iz < 10 ; $iz++) {
				if($number[$iz])
				{
					if($number[$iz] != $value[$iz])
					{
						$ac[$key] =  $number[$iz];
						unset($number[$iz]);
						break;
					}

				} 
					
				}
			
		}
		
				// print_r($a);die;
		foreach ($ac as $key => $value) {
			if($key == '冠军')
			{
				$jg[0] = $value[0];
			}
			if($key == '亚军')
			{
				$jg[1] = $value[0];
			}
			if($key == '第三名')
			{
				$jg[2] = $value[0];
			}
			if($key == '第四名')
			{
				$jg[3] = $value[0];
			}
			if($key == '第五名')
			{
				$jg[4] = $value[0];
			}
			if($key == '第六名')
			{
				$jg[5] = $value[0];
			}
			if($key == '第七名')
			{
				$jg[6] = $value[0];
			}
			if($key == '第八名')
			{
				$jg[7] = $value[0];
			}
			if($key == '第九名')
			{
				$jg[8] = $value[0];
			}
			if($key == '第十名')
			{
				$jg[9] = $value[0];
			}

		}
// $a = array_rand($ips);//返回结果是一个键值
 //print_r($ips[$a]);exit;
		
		 //print_r($jg);die;
/***************************************************************/
//echo 123;die;
 for ($y = 1; ; $y++) {
 	$comm = 0;
 	$profit_1 = 0;
for ($i=0; $i <10 ; $i++) { 
			if(!$jg[$i])
			{
				$a = array_rand($number);
				$jg[$i] = $number[$a];
				unset($number[$a]);
			}
		}
		//print_r($jg);die;
$bet_1 = $db->GetList("SELECT * FROM `@#_bet` where `issue` = '$issue' and `returns` = 0");


		$result_1 = $jg;
		$sum_1 = $result_1[0]+$result_1[1];
		$option_1 = array('冠军','亚军','第三名','第四名','第五名','第六名','第七名','第八名','第九名','第十名');
		$result1_1 = array();
		foreach ($result_1 as $key => $value) {
			$result1_1[] = $option_1[$key].$value;
			if($value%2==0)
			{
				$result1_1[] = $option_1[$key].双;
			}
			else{
				$result1_1[] = $option_1[$key].单;
			}
			if($value > 5 )
			{
				$result1_1[] = $option_1[$key].大;
			}
			else{
				$result1_1[] = $option_1[$key].小;
			}

		}
		$result1_1[] = "冠亚军和".$sum_1;
		if($sum_1%2==0)
		{
			$result1_1[] = "冠亚军和双";
		}
		else
		{
			$result1_1[] = "冠亚军和单";	
		}
		if($sum > 11)
		{
			$result1_1[] = "冠亚军和大";
		}
		else
		{
			$result1_1[] = "冠亚军和小";
		}
		if($sum == 11)
		{
			$result1_1[] = "冠亚军和双";
			$result1_1[] = "冠亚军和大";
			$result1_1[] = "冠亚军和小";
		}
		 //print_R($bet_1);die;
		foreach ($bet_1 as $k => $v) {
			// echo $v['name'];die;
			if(in_array($v['name'],$result1_1))
			{
				// echo 111;die;
				$profit_1 += $v['number']*$v['odds'];

			}
			
		}

		$bet_2 = $db->GetList("SELECT * FROM `@#_bet` where `returns` = 0 order by time desc");

		foreach ($bet_2 as $value) {
							$comm += $value['number'];
							
		}
		//echo $profit_1.'/'.$comm;die;

		if($comm >= $profit_1){
			
		     break;
		}

           
        }
// echo $comm .'/'. $profit_1;
// print_r($jg);die;

/***************************************************************/
		// print_r($jg);die;
		
		foreach ($jg as $k => $v) {
			$result .= $v.",";
			# code...
		}
		$result = rtrim($result, ',');
		// echo $result;die;
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
			if((int)$sum == 11)
			{
				$NumberDs = "和";
				$NumberSize = '和';
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