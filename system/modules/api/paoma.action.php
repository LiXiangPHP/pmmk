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
		$token = isset($_POST['uid']) ? $_POST['uid'] : null;
		$info = System::token_uid($token);
		if($info['uid'])
		{
			$code = 100;
			$msg = '请登录';
			echo json_encode(array("code"=>$code,"msg"=>$msg));die;
		}
		$bet = stripslashes($_POST['bet']);
		// $arr = array('冠军'=>array("2"=>"2","3"=>"11"),'亚军'=>array("2"=>"2"));
		// echo json_encode($arr);die;
		// $bet = '{"\u51a0\u519b":{"2":"2"},"\u4e9a\u519b":{"2":"2"}}';
		
		$bet = json_decode($bet);
		$number = 0;
		foreach ($bet as $key => $value) {
			$oid = $db->GetOne("select id from `@#_option` where `name` = '$key'");
			$oid = $oid['id'];
			foreach ($value as $k => $v) {
				
				$number = $value->$k;
				$n+=$number;
				$t = $db->GetOne("select id,odds from `@#_option_detail` where `oid` = '$oid' and `number` = '$number'");
				$name = $key.$number;
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
}
?>