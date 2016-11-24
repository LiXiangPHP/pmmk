<?php

class pay extends SystemAction {

	public function paysubmit()
	{
		$token = isset($_POST['token']) ? $_POST['token'] : null;
		$info = System::token_uid($token);
		$uid = $info['uid'];
		$type = $_POST['type'];
		$money = $_POST['money'];
		$pay_type = "微信支付";
		$time = time();
		$scookies = 0;
		$score = 0;
		System::load_app_fun("pay","pay");
		if($uid)
		{
			if($type == 1)
			{
				$db = System::load_sys_class('model');
				$dingdancode = pay_get_dingdan_code('C');
				$query = $db->Query("INSERT INTO `@#_member_addmoney_record` (`uid`, `code`, `money`, `pay_type`, `status`,`time`,`score`,`scookies`) VALUES ('$uid', '$dingdancode', '$money', '$pay_type','未付款', '$time','$score','$scookies')");
				include 'wechatAppPay.class.php';
				//填写配置参数
						//订单号	
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
				echo json_encode($return);

			}
		}
		else
		{
			$json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
			echo json_encode($json);
		}
	}
}
?>