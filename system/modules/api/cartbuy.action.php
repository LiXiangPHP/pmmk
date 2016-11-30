<?php
defined('G_IN_SYSTEM')or exit('No permission resources.');
System::load_app_fun("pay","pay");
System::load_sys_fun("user");
System::load_app_class("tocode","pay",'no');
System::load_app_class('base','member','no');
System::load_app_fun('user','go');
class cartbuy extends SystemAction {
    /*购物车购买接口*/
    private $db;
    private $members;       //会员信息
    private $MoenyCount;    //商品总金额
    private $shops;         //商品信息
    private $pay_type;      //支付类型
    private $fukuan_type;   //付款类型 买商品 充值
    private $dingdan_query = true;  //订单的   mysql_qurey 结果
    public $pay_type_bank = false;

    public $scookie = null;
    public $fufen = 0;
    public $fufen_to_money = 0;
    public  $Cartlist;
    public function cart_buy()
    {
        $data = $_POST['data'];
        $uid = $_POST['uid'];
        $info = System::token_uid($uid);
         if ($info['code']!==200) {
             $json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
             echo json_encode($json);die;
         }
        $uid = $info['uid'];
        if($_POST['addressid'])
        {
            $this->address_id = $_POST['addressid'];
        }
        else
        {
            $this->address_id = '';
        }
        // $uid = 694;
        if($_POST['type'] == 1)
        {
            $pay_checkbox= false;
            $pay_type_id=1;
        }
        elseif($_POST['type'] == 2)
        {
            $pay_checkbox= false;
            $pay_type_id = 2;
        }else
        {
            $pay_checkbox= true;
            $pay_type_id = false;
        }
        
        $fufen = 0;
        $db = System::load_sys_class('model');
        // $Cartlist = $db->GetList("SELECT * FROM `@#_shopcart` WHERE user_id = '690'");
        /*************
        start
         *************/
        //Array ( [216] => Array ( [shenyu] => 4262 [num] => 1 [money] => 1 ) [MoenyCount] => 1 )
        $data = json_decode(stripslashes($data));
        foreach ($data as $k => $v) {
            foreach ($v as $key => $value) {
                $shengyu = $db->GetOne("SELECT shenyurenshu FROM `@#_shoplist` WHERE id = '$value->id' ");
                $Cartlist[$value->id] = array("shenyu"=>$shengyu['shenyurenshu'],"num"=>$value->renshu,"money"=>$value->renshu);
            }
        }
        $Cartlist['MoenyCount'] = $data->money;
        $this->Cartlist = $Cartlist;
        if(is_array($Cartlist)){
            foreach($Cartlist as $key => $val){
                $shopids.=intval($key).',';
            }
            $shopids=str_replace(',0','',$shopids);
            $shopids=trim($shopids,',');

        }

        // $pay=System::load_app_class('pay','pay');
        // //$pay->scookie = json_decode(base64_decode($_POST['cookies']));

        // $pay->fufen = $fufen;
        // $pay->pay_type_bank = $pay_type_bank;
        $ok = $this->init($uid,$pay_type_id,'go_record');    //云购商品
        if($ok !== 'ok'){
            $code=100;
            $msg = $ok;
            echo json_encode(array("code"=>$code,"msg"=>$msg));die;
        }
        $check = $this->go_pay($pay_checkbox);
        // echo $check;die;
        
        if($check === 'not_pay'){
            $code=100;
            $msg = "未选择支付平台";
            echo json_encode(array("code"=>$code,"msg"=>$msg));die;
        }
        if(!$check){
            $code=100;
            $msg = "商品支付失败";
            $cartdel = $db->Query("DELETE FROM `@#_shopcart` WHERE user_id='$uid' and good_id in ($shopids)");
            echo json_encode(array("code"=>$code,"msg"=>$msg));die;
        }
        if($check){
            $cartdel = $db->Query("DELETE FROM `@#_shopcart` WHERE user_id='$uid' and good_id in ($shopids)");
            if($cartdel!== false){
                $code=200;
                $msg ="购买成功";
                echo json_encode(array("code"=>$code,"msg"=>$msg));die;
            }

        }else{
            $code=100;
            $msg ="购买失败";
            $cartdel = $db->Query("DELETE FROM `@#_shopcart` WHERE user_id='$uid' and good_id in($shopids)");
            echo json_encode(array("code"=>$code,"msg"=>$msg));die;
            //失败
        }
        exit;

    }
    public function init($uid=null,$pay_type=null,$fukuan_type='',$addmoney=''){

        $this->db=System::load_sys_class('model');
        $this->db->Autocommit_start();
        $this->members = $this->db->GetOne("SELECT * FROM `@#_member` where `uid` = '$uid' for update");

        if($this->pay_type_bank){
            $pay_class = $this->pay_type_bank;
            $this->pay_type =$this->db->GetOne("SELECT * from `@#_pay` where `pay_class` = '$pay_class' and `pay_start` = '1'");
            $this->pay_type['pay_bank'] = $pay_type;

        }
        if(is_numeric($pay_type)){
            if($pay_type == 1)
            {
                 $this->pay_type['pay_name'] ='微信支付';
                 $this->pay_type['id'] ='1';

                $this->pay_type['pay_bank'] = 'DEFAULT';
            }
            elseif($pay_type == 2)
            {
                 $this->pay_type['id'] ='2';
                $this->pay_type['pay_name'] ='支付宝支付';
                $this->pay_type['pay_bank'] = 'DEFAULT';
            }
            else
            {
                $this->pay_type =$this->db->GetOne("SELECT * from `@#_pay` where `pay_id` = '$pay_type' and `pay_start` = '1'");
                $this->pay_type['pay_bank'] = 'DEFAULT';
            }
            
        }

        $this->fukuan_type=$fukuan_type;
        if($fukuan_type=='go_record'){
            return $this->go_record();
        }

        if($fukuan_type=='addmoney_record'){
            return addmoney_record($addmoney);
        }
        return false;
    }
    public  function addmoney_record($money=null,$data=null){    
        $uid=$this->members['uid'];
        $dingdancode = pay_get_dingdan_code('C');       //订单号   
        if(!is_array($this->pay_type)){
            return 'not_pay';
        }
        $pay_type = $this->pay_type['pay_name'];
        $time = time();
        if(!empty($data)){
            $scookies = $data;
        }else{
            $scookies = '0';
        }
        $score = $this->fufen;      
        $query = $this->db->Query("INSERT INTO `@#_member_addmoney_record` (`uid`, `code`, `money`, `pay_type`, `status`,`time`,`score`,`scookies`) VALUES ('$uid', '$dingdancode', '$money', '$pay_type','未付款', '$time','$score','$scookies')");               
        if($query){
            $this->db->Autocommit_commit();
        }else{
            $this->db->Autocommit_rollback();
            return false;
        }
        if($this->pay_type['id'] == 1)
        {
            include 'WechatAppPay.class.php';
                //填写配置参数    
                $options = array(
                    'appid'     =>  'wxf7f45a312b8c036f',       //填写微信分配的公众开放账号ID
                    'mch_id'    =>  '1408736602',               //填写微信支付分配的商户号
                    'notify_url'=>  'http://www.gangmaduobao.com/', //填写微信支付结果回调地址
                    'key'       =>  '6518f10706e342758b58a4d8bc8314bc'              //填写  商户支付密钥Key。审核通过后，在微信发送的邮件中查看
                );
                //统一下单方法
                $wechatAppPay = new wechatAppPay($options);
                $params['body'] = '商品支付';                       //商品描述
                $params['out_trade_no'] = $dingdancode; //自定义的订单号
                $params['total_fee'] = $money*100;                  //订单金额 只能为整数 单位为分
                $params['trade_type'] = 'APP';                  //交易类型 JSAPI | NATIVE | APP | WAP 
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
        if($this->pay_type['id'] == 2)
        {
            $pay_type = "支付宝";
                $db = System::load_sys_class('model');
                $dingdancode = pay_get_dingdan_code('C');
                $query = $db->Query("INSERT INTO `@#_member_addmoney_record` (`uid`, `code`, `money`, `pay_type`, `status`,`time`,`score`,`scookies`) VALUES ('$uid', '$dingdancode', '$money', '$pay_type','未付款', '$time','$score','$scookies')");
                $return['out_trade_no']  = $dingdancode;

                echo json_encode($return);die;
        }
        else
        {
            $return['msg'] = '未选择支付平台'; 
            echo json_encode($return);die;
        }
       
    }
    //买商品
    public  function go_record(){

        $Cartlist = $this->Cartlist;
        $shopids='';            //商品ID
        if(is_array($Cartlist)){
            foreach($Cartlist as $key => $val){
                $shopids.=intval($key).',';
            }
            $shopids=str_replace(',0','',$shopids);
            $shopids=trim($shopids,',');

        }
        $shoplist=array();      //商品信息
        if($shopids!=NULL){
            $shoplist=$this->db->GetList("SELECT * FROM `@#_shoplist` where `id` in($shopids) and `q_uid` is null for update",array("key"=>"id"));
        }else{
            $this->db->Autocommit_rollback();
            return '购物车内没有商品!';
        }
        $MoenyCount= 0;
        $shopguoqi = 0;
        if(count($shopids)>=1){
            $scookies_arr = array();
            $scookies_arr['MoenyCount'] = 0;
            foreach($Cartlist as $key => $val){
                $key=intval($key);
                if(isset($shoplist[$key]) && $shoplist[$key]['shenyurenshu'] != 0){
                    if(($shoplist[$key]['xsjx_time'] != '0') && $shoplist[$key]['xsjx_time'] < time()){
                        unset($shoplist[$key]);
                        $shopguoqi = 1;
                        continue;
                    }
                    $shoplist[$key]['cart_gorenci']=$val['num'] ? $val['num'] : 1;
                    if($shoplist[$key]['cart_gorenci'] >= $shoplist[$key]['shenyurenshu']){
                        $shoplist[$key]['cart_gorenci'] = $shoplist[$key]['shenyurenshu'];
                    }
                    $MoenyCount+=$shoplist[$key]['yunjiage']*$shoplist[$key]['cart_gorenci'];
                    $shoplist[$key]['cart_xiaoji']=substr(sprintf("%.3f",$shoplist[$key]['yunjiage'] * $shoplist[$key]['cart_gorenci']),0,-1);
                    $shoplist[$key]['cart_shenyu']=$shoplist[$key]['zongrenshu']-$shoplist[$key]['canyurenshu'];
                    $scookies_arr[$key]['shenyu'] = $shoplist[$key]['cart_shenyu'];
                    $scookies_arr[$key]['num'] = $shoplist[$key]['cart_gorenci'];
                    $scookies_arr[$key]['money'] = intval($shoplist[$key]['yunjiage']);
                    $scookies_arr['MoenyCount'] += intval($shoplist[$key]['cart_xiaoji']);
                }else{
                    unset($shoplist[$key]);
                }
            }
           if(count($shoplist) < 1){
            // if(count($shopids) < 1){
                $scookies_arr = '0';
                $this->db->Autocommit_rollback();
                if($shopguoqi){
                    return '限时揭晓过期商品不能购买!';
                }else{
                    return '购物车里没有商品!';
                }
            }
        }else{
            $scookies_arr = '0';
            $this->db->Autocommit_rollback();
            return '购物车里商品已经卖完或已下架!';
        }


        $this->MoenyCount=substr(sprintf("%.3f",$MoenyCount),0,-1);
        /**
         *   最多能抵扣多少钱
         **/

        if($this->fufen){
            if($this->fufen >= $this->members['score']){
                $this->fufen = $this->members['score'];
            }



            $fufen = System::load_app_config("user_fufen",'','member');
            if($fufen['fufen_yuan']){
                $this->fufen_to_money  = intval($this->fufen / $fufen['fufen_yuan']);
                if($this->fufen_to_money >= $this->MoenyCount){
                    $this->fufen_to_money = $this->MoenyCount;
                    $this->fufen = $this->fufen_to_money * $fufen['fufen_yuan'];
                }
            }else{
                $this->fufen_to_money = 0;
                $this->fufen = 0;
            }
        }else{
            $this->fufen_to_money = 0;
            $this->fufen = 0;
        }

        /*总支付价格*/
        $this->MoenyCount = $this->MoenyCount - $this->fufen_to_money;
        $this->shoplist=$shoplist;
        $this->scookies_arr = $scookies_arr;
        return 'ok';
    }
    public function go_pay($pay_checkbox){
        if($this->members['money'] >= $this->MoenyCount){
            $uid=$this->members['uid'];
            $pay_1 =  $this->pay_bag();
            if(!$pay_1){return $pay_1;}
            $dingdancode=$this->dingdancode;
            $pay_2 = pay_go_fund($this->goods_count_num);
            $pay_3 = pay_go_yongjin($uid,$dingdancode);

            return $pay_1;
        }
        if(!is_array($this->pay_type)){
            return 'not_pay';
        }
        if(is_array($this->scookies_arr)){
            $scookie = serialize($this->scookies_arr);
        }else{
            $scookie= '0';
        }
        if($pay_checkbox){
            $money = $this->MoenyCount - $this->members['money'];
            return $this->addmoney_record($money,$scookie);
        }else{
            //全额支付
            $this->MoenyCount;
            return $this->addmoney_record($this->MoenyCount,$scookie);
        }
        exit;
    }


    //账户里支付
    private function pay_bag(){
        $time=time();
        $uid=$this->members['uid'];
        $fufen = System::load_app_config("user_fufen",'','member');

        $query_1 = $this->set_dingdan('账户','A');
        /*会员购买过账户剩余金额*/
        $Money = $this->members['money'] - $this->MoenyCount + $this->fufen_to_money;
        $query_fufen = true;
        $pay_zhifu_name = '账户';
        if($this->fufen_to_money){
            $myfufen = $this->members['score'] - $this->fufen;
            $query_fufen = $this->db->Query("UPDATE `@#_member` SET `score`='$myfufen' WHERE (`uid`='$uid')");
            $pay_zhifu_name = '福分';
            $this->MoenyCount = $this->fufen;
        }
        // echo $this->MoenyCount;die;
        //更新用户账户金额
        if($query_1)
        {
            $query_2 = $this->db->Query("UPDATE `@#_member` SET `money`='$Money' WHERE (`uid`='$uid')");            //金额
            $query_3 = $info = $this->db->GetOne("SELECT * FROM  `@#_member` WHERE (`uid`='$uid') LIMIT 1");
            $query_4 = $this->db->Query("INSERT INTO `@#_member_account` (`uid`, `type`, `pay`, `content`, `money`, `time`) VALUES ('$uid', '-1', '$pay_zhifu_name', '云购了商品', '{$this->MoenyCount}', '$time')");
            $query_5 = true;
            $query_insert = true;
        }
        else
        {
            return false;
        }
        


        $goods_count_num = 0;
        foreach($this->shoplist as $shop):
            if($shop['canyurenshu'] >= $shop['zongrenshu'] && $shop['maxqishu'] >= $shop['qishu']){
                $this->db->Query("UPDATE `@#_shoplist` SET `canyurenshu`=`zongrenshu`,`shenyurenshu` = '0' where `id` = '$shop[id]'");
            }else{
                $shenyurenshu = $shop['zongrenshu'] - $shop['canyurenshu'];
                $query = $this->db->Query("UPDATE `@#_shoplist` SET `canyurenshu` = '$shop[canyurenshu]',`shenyurenshu` = '$shenyurenshu' WHERE `id`='$shop[id]'");
                if(!$query)$query_5=false;
            }
            $goods_count_num += $shop['goods_count_num'];
        endforeach;


        //添加用户经验
        $jingyan = $this->members['jingyan'] + ($fufen['z_shoppay'] * $goods_count_num);
        $query_jingyan = $this->db->Query("UPDATE `@#_member` SET `jingyan`='$jingyan' WHERE (`uid`='$uid')");  //经验值


        //添加福分
        // if(!$this->fufen_to_money){
        //     $mygoscore = $fufen['f_shoppay']*$goods_count_num;
        //     $mygoscore_text =  "云购了{$goods_count_num}人次商品";
        //     $myscore = $this->members['score'] + $mygoscore;
        //     $query_add_fufen_1 = $this->db->Query("UPDATE `@#_member` SET `score`= '$myscore' WHERE (`uid`='$uid')");
        //     $query_add_fufen_2 = $this->db->Query("INSERT INTO `@#_member_account` (`uid`, `type`, `pay`, `content`, `money`, `time`) VALUES ('$uid', '1', '福分', '$mygoscore_text', '$mygoscore', '$time')");
        //     $query_fufen = ($query_add_fufen_1 && $query_add_fufen_2);
        // }

        $dingdancode=$this->dingdancode;
        $query_6 = $this->db->Query("UPDATE `@#_member_go_record` SET `status`='已付款,未发货,未完成' WHERE `code`='$dingdancode' and `uid` = '$uid'");
        $query_7 = $this->dingdan_query;
        $query_8 = $this->db->Query("UPDATE `@#_caches` SET `value`=`value` + $goods_count_num WHERE `key`='goods_count_num'");
        $this->goods_count_num = $goods_count_num;

        if($query_fufen && $query_jingyan && $query_1 && $query_2 && $query_3 && $query_4 && $query_5 && $query_6 && $query_7 && $query_insert && $query_8){
            if($info['money'] == $Money){
                $this->db->Autocommit_commit();
                foreach($this->shoplist as $shop):
                    if($shop['canyurenshu'] >= $shop['zongrenshu'] && $shop['maxqishu'] >= $shop['qishu']){
                        $this->db->Autocommit_start();
                        $query_insert = pay_insert_shop($shop,'add');
                        if(!$query_insert){
                            $this->db->Autocommit_rollback();
                        }else{
                            $this->db->Autocommit_commit();
                        }
                        $this->db->Query("UPDATE `@#_shoplist` SET `canyurenshu`=`zongrenshu`,`shenyurenshu` = '0' where `id` = '$shop[id]'");
                    }
                endforeach;

                return true;
            }else{
                $this->db->Autocommit_rollback();
                return false;
            }
        }else{
            $this->db->Autocommit_rollback();
            return false;
        }

    }
    private function set_dingdan($pay_type='',$dingdanzhui=''){
        $uid=$this->members['uid'];
        $uphoto = $this->members['img'];
        $username = addslashes(get_user_name($this->members));
        $insert_html='';
        $this->dingdancode = $dingdancode= pay_get_dingdan_code($dingdanzhui);      //订单号

        if(count($this->shoplist)>1){
            $dingdancode_tmp = 1;   //多个商品相同订单
        }else{
            $dingdancode_tmp = 0;   //单独商品订单
        }

        $ip =  $this->members['user_ip'];


        /*订单时间*/
        $time=sprintf("%.3f",microtime(true));
        $this->MoenyCount=0;
        foreach($this->shoplist as $key=>$shop){
            $ret_data = array();
            pay_get_shop_codes($shop['cart_gorenci'],$shop,$ret_data);
            $this->dingdan_query = $ret_data['query'];
            if(!$ret_data['query'])$this->dingdan_query = false;
            $codes = $ret_data['user_code'];                                    //得到的云购码
            $codes_len= intval($ret_data['user_code_len']);                     //得到云购码个数
            $money=$codes_len * $shop['yunjiage'];                              //单条商品的总价格
            $this->MoenyCount += $money;                                        //总价格
            $status='未付款,未发货,未完成';
            $shop['canyurenshu'] = intval($shop['canyurenshu']) + $codes_len;
            $shop['goods_count_num'] = $codes_len;
            $shop['title'] = addslashes($shop['title']);
            $this->shoplist[$key] = $shop;
            if($codes_len){
                $insert_html.="('$dingdancode','$dingdancode_tmp','$uid','$username','$uphoto','$shop[id]','$shop[title]','$shop[qishu]','$codes_len','$money','$codes','$pay_type','$ip','$status','$time','$this->address_id'),";
            }
        }
        $sql="INSERT INTO `@#_member_go_record` (`code`,`code_tmp`,`uid`,`username`,`uphoto`,`shopid`,`shopname`,`shopqishu`,`gonumber`,`moneycount`,`goucode`,`pay_type`,`ip`,`status`,`time`,`address_id`) VALUES ";
        // echo $this->address_id;die;
        $sql.=trim($insert_html,',');
        //$this->db->Query("set global max_allowed_packet = 2*1024*1024*10");
        // echo $sql;die;
        return $this->db->Query($sql);
    }

}
