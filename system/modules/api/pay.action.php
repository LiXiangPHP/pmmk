<?php

class pay extends SystemAction {

	public function paysubmit()
	{
		$token = isset($_POST['token']) ? $_POST['token'] : null;
		$info = System::token_uid($token);
		$uid = $info['uid'];
		if(!$uid)
		{
			$json = array('code' => 100, 'msg' => '请登录', 'data' => $data);
			echo json_encode($json);
		}
		$type = $_POST['type'];
		$money = $_POST['money'];
		
		$time = time();
		$scookies = 0;
		$score = 0;
		System::load_app_fun("pay","pay");
		if($uid)
		{
			if($type == 1)
			{
				$pay_type = "微信支付";
				$db = System::load_sys_class('model');
				$dingdancode = pay_get_dingdan_code('C');
				$query = $db->Query("INSERT INTO `@#_member_addmoney_record` (`uid`, `code`, `money`, `pay_type`, `status`,`time`,`score`,`scookies`) VALUES ('$uid', '$dingdancode', '$money', '$pay_type','未付款', '$time','$score','$scookies')");
				include 'WechatAppPay.class.php';
				//填写配置参数	
				$options = array(
					'appid' 	=> 	'wxf7f45a312b8c036f',		//填写微信分配的公众开放账号ID
					'mch_id'	=>	'1408736602',				//填写微信支付分配的商户号
					'notify_url'=>	'http://www.gangmaduobao.com/',	//填写微信支付结果回调地址
					'key'		=>	'6518f10706e342758b58a4d8bc8314bc'				//填写  商户支付密钥Key。审核通过后，在微信发送的邮件中查看
				);
				//统一下单方法
				$wechatAppPay = new wechatAppPay($options);
				$params['body'] = '用户充值';						//商品描述
				$params['out_trade_no'] = $dingdancode;	//自定义的订单号
				$params['total_fee'] = $money*100;					//订单金额 只能为整数 单位为分
				$params['trade_type'] = 'APP';					//交易类型 JSAPI | NATIVE | APP | WAP 
				$result = $wechatAppPay->unifiedOrder( $params );
				//创建APP端预支付参数
				/** @var TYPE_NAME $result */
				$data = @$wechatAppPay->getAppPayParams( $result['prepay_id'] );
				$return['prepayid'] = $data['prepayid'];
				$return['noncestr'] = $data['noncestr'];
				$return['timestamp'] = $data['timestamp'];
				$return['out_trade_no'] = $dingdancode;
				$return['sign'] = $data['sign'];
				echo json_encode($return);die;

			}
			if($type == 2)
			{
				$pay_type = "支付宝";
				$db = System::load_sys_class('model');
				$dingdancode = pay_get_dingdan_code('C');
				$query = $db->Query("INSERT INTO `@#_member_addmoney_record` (`uid`, `code`, `money`, `pay_type`, `status`,`time`,`score`,`scookies`) VALUES ('$uid', '$dingdancode', '$money', '$pay_type','未付款', '$time','$score','$scookies')");
				$data['out_trade_no']  = $dingdancode;

				echo json_encode($data);die;
			}
		}
		else
		{
			$json = array('code' => 100, 'msg' => '请登录', 'data' => $data);
			echo json_encode($json);
		}
	}
	public function payCallback()
	{
		$db = System::load_sys_class('model');
		$token = isset($_POST['token']) ? $_POST['token'] : null;
		$info = System::token_uid($token);
		$uid = $info['uid'];
		if(!$uid)
		{
			$json = array('code' => 100, 'msg' => '请登录');
			echo json_encode($json);die;
		}
		$out_trade_no = $_POST['out_trade_no'];
		$dingdaninfo = $db->GetOne("select * from `@#_member_addmoney_record` where `code` = '$out_trade_no'");
		if(!$dingdaninfo)
		{ 
			$json = array('code' => 100, 'msg' => '没有该订单');
			echo json_encode($json);die;
		}	//没有该订单,失败
		if($dingdaninfo['status'] == '已付款'){
			$json = array('code' => 200, 'msg' => '已付款');
			echo json_encode($json);die;
		}
		$c_money = intval($dingdaninfo['money']);
		$uid = $dingdaninfo['uid'];
		$time = time();
		
		$db->Autocommit_start();
		$up_q1 = $db->Query("UPDATE `@#_member_addmoney_record` SET `status` = '已付款' where `id` = '$dingdaninfo[id]' and `code` = '$dingdaninfo[code]'");
		$up_q2 = $db->Query("UPDATE `@#_member` SET `money` = `money` + $c_money where (`uid` = '$uid')");			
		$up_q3 = $db->Query("INSERT INTO `@#_member_account` (`uid`, `type`, `pay`, `content`, `money`, `time`) VALUES ('$uid', '1', '账户', '充值', '$c_money', '$time')");
		
		if($up_q1 && $up_q2 && $up_q3){			
			$db->Autocommit_commit();
		}else{
			$db->Autocommit_rollback();
			$json = array('code' => 100, 'msg' => '充值失败');
			echo json_encode($json);die;
		}			
		if(empty($dingdaninfo['scookies'])){					
			$json = array('code' => 200, 'msg' => '充值成功');
			echo json_encode($json);die;
		}
	}
}
?>
