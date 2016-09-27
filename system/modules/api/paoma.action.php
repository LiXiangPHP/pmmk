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
		$Endtime='2016-09-27 00:00:00';
		$Endtime=strtotime($Endtime);
		$sytime = $time-$Endtime;
		// echo $Endtime;die;
		$ge = intval(floor($sytime/300));
		$s = 300-($sytime - $ge*300);

		if($s < 5 )
		{
			$status = 'prize';
		}
		if($s > 40 )
		{
			$status = 'wait';
			$WaitTime = $s-25;
		}
		if( $s >25 && $s<40)
		{
			$status = 'no';
			$WaitTime = $s-25;
		}
		if($s >5 && $s<25)
		{
			$status = 'game';
			$GameTime = $s;
		}
		if(!$GameTime)
		{
			$GameTime = 20;
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
		$data = array('issue'=>$issue,'lastissue'=>$lastissue,'qihao'=>$qihao,'status'=>$status,'WaitTime'=>$WaitTime,'GameTime'=>$GameTime,'detail'=>$detail,'LastResult'=>$LastResult,'sum'=>$sum,'NowResult'=>$NowResult);
		echo json_encode($data);

		
	}
}
?>