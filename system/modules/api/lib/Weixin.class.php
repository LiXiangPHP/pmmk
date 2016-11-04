<?php 

/**
*	微信登陆类 2016年11月4日
*	author Lixiang
**/

class Weixin {


	public function token($code)
	{

		$appId = "wxf7f45a312b8c036f";
    	$appSecret = "000e1118ca0e114fe0298d0b95bdaac8";
		// echo "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".."&secret=."$this->appSecret."&code=."$code."&grant_type=authorization_code";die;
		return file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appId&secret=$appSecret&code=$code&grant_type=authorization_code");
	}
	public function info($access_token)
	{
		$appId = "wxf7f45a312b8c036f";
		return file_get_contents("https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$appId");
	}

	

	
}