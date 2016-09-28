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
			$lastissue=$db->GetOne("select issue from `@#_bet` WHERE `issue` order by issue DESC");
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
		$LastResult=$db->GetOne("select result from `@#_bet_result` WHERE `issue` = '$lastissue'");
		$LastResult = explode(',',$LastResult['result']);
		$sum = $LastResult[0]+$LastResult[1];
		$option=$db->GetList("select * from `@#_option`");
		foreach ($option as $k => $v) {
			// print_r($v);die;
			$detail[$v['name']]=$db->GetList("select number,odds from `@#_option_detail` where `oid` = '$v[id]'");
			
		}
		$NowResult = '';
	// print_r($detail);die;
		$code = 200;
		



		$data = array('issue'=>$issue,'lastissue'=>$lastissue,'qihao'=>$qihao,'status'=>$status,'WaitTime'=>$WaitTime,'GameTime'=>$GameTime,'detail'=>$detail,'LastResult'=>$LastResult,'sum'=>$sum,'NowResult'=>$NowResult);
		$json = array('code' => $code,'data'=>$data);
		echo json_encode($json);


		
	}
}
?>