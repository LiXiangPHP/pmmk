<?php
defined('G_IN_SYSTEM')or exit('No permission resources.');
System::load_app_fun("pay","pay");
System::load_sys_fun("user");
System::load_app_class("tocode","pay",'no');
System::load_app_class('base','member','no');
System::load_app_fun('user','go');
class cartbuy extends SystemAction {
/*购物车购买接口*/
public function json_cartbuy(){
    $data = $_POST['data'];
    $uid = $_POST['uid'];
    $addressid = $_POST['addressid'];
    $dingdanzhui='A';
    $pay_type = "账户";
    $info = System::token_uid($uid);
    $db = System::load_sys_class('model');
    $cart_xiaoji = '';
    $cart_shenyu = '';
    if ($info['code']==200) {
        $Sdata = json_decode(stripslashes($data), true);
//        print_r($Sdata);
        $moeny = $Sdata['money'];
//        print_r($moeny);
            $shopids = '';
            $shopqishu = '';
            if (is_array($Sdata)) {
                foreach ($Sdata['data'] as $k => $v) {
                    $shopids = $shopids . ',' . $v['id'];
                    $shopids = trim($shopids, ',');
                    $shopqishu = $shopqishu . ',' . $v['qishu'];
                    $shopqishu = trim($shopqishu, ',');
                    $canyu = $db->GetOne("SELECT shenyurenshu FROM `@#_shoplist` WHERE id = '$v[id]'");
                    if ($v['renshu'] > $canyu['shenyurenshu']){
                        $code = 100;
                        $msg = "购买人数不足，购买失败";
                        $json = array('code' => $code, 'msg' => $msg);
                        echo json_encode($json);die;
                    }
                }
            }
//            echo '---';
//            print_r($shopqishu);
//            print_r($shopids);
            $shoplist = $db->GetList("SELECT * FROM `@#_shoplist` WHERE id IN ($shopids)");
//            print_r($shoplist);
            $dizhi = $db->GetOne("SELECT * FROM `@#_member_dizhi` WHERE id = '$addressid'");
//            print_r($dizhi);
            $members = $db->GetOne("SELECT * FROM `@#_member` WHERE uid = '$info[uid]'");
//            print_r($members);
            $uphoto = $members['img'];
//            print_r($uphoto);
            $username = $members['username'];
//            print_r($username);
            $insert_html='';
            $this->dingdancode = $dingdancode= pay_get_dingdan_code($dingdanzhui);		//订单号
//            print_r($dingdancode);
            if(count($shoplist)>1){
                $dingdancode_tmp = 1;	//多个商品相同订单
            }else{
                $dingdancode_tmp = 0;	//单独商品订单
            }
//            echo $dingdancode_tmp;
            $ip = $members['user_ip'];
            /*订单时间*/
            $time=sprintf("%.3f",microtime(true));
            $this->MoenyCount=0;
            foreach($shoplist as $key=>$shop){
                foreach($Sdata['data'] as $ky => $vva ) {
                    if($shop['id'] == $vva['id']) {
                        $shop['cart_gorenci'] = $vva['renshu'];
                        $shop['qishu'] = $vva['qishu'];
                    }
                }
                $shop['cart_shenyu'] = $shop['shenyurenshu'];
                $shop['cart_xiaoji'] = $shop['yunjiage'] * $shop['cart_gorenci'];
                $ret_data = array();
                pay_get_shop_codes($shop['cart_gorenci'],$shop,$ret_data);
                $this->dingdan_query = $ret_data['query'];
//                print_r($ret_data);die;
                if(!$ret_data['query'])$this->dingdan_query = false;
                $codes = $ret_data['user_code'];									//得到的云购码
//                print_r($codes);die;
                $codes_len= intval($ret_data['user_code_len']);						//得到云购码个数
//                print_r($codes_len);
                $status='未付款,未发货,未完成';
                $shop['canyurenshu'] = intval($shop['canyurenshu']) + $codes_len;
//                print_r($shop['canyurenshu']);
                $shop['goods_count_num'] = $codes_len;
                $shop['title'] = addslashes($shop['title']);
//                print_r( $shop['title']);
                $this->shoplist[$key] = $shop;
                $uid = $info['uid'];
                if($codes_len){
                    $insert_html.="('$dingdancode','$dingdancode_tmp','$uid','$username','$uphoto','$shop[id]','$shop[title]','$shop[qishu]','$codes_len','$money','$codes','$pay_type','$ip','$status','$time'),";
                }
            }
            $sql="INSERT INTO `@#_member_go_record` (`code`,`code_tmp`,`uid`,`username`,`uphoto`,`shopid`,`shopname`,`shopqishu`,`gonumber`,`moneycount`,`goucode`,`pay_type`,`ip`,`status`,`time`) VALUES ";
            $sql.=trim($insert_html,',');
            $dingdanadd = $db->Query($sql);
            if(!empty($dingdanadd)){
                $moneycount = $members['money'] - $moeny;
                $memberdel = $db->Query("UPDATE `@#_member` SET `money`='$moneycount' WHERE uid='$info[uid]'");
                if(!empty($memberdel)){
                    $code = 100;
                    $msg = "户余额不足，购买失败";
                    $json = array('code' => $code, 'msg' => $msg);
                    echo json_encode($json);die;
                }
            }
            if ($db->Query("DELETE FROM `@#_shopcart` WHERE user_id='$info[uid]' and good_id in ($shopids)") !== false) {
                $status='已付款,未发货,未完成';
                if (is_array($Sdata)) {
                    foreach ($Sdata['data'] as $k => $v) {
                        $canyu = $db->GetOne("SELECT shenyurenshu,canjiarenshu FROM `@#_shoplist` WHERE id = '$v[id]'");
                        $shenyu = $canyu['shenyurenshu'] - $v['renshu'];
                        $canjia = $canyu['canjiarenshu'] + $v['renshu'];
                        $renshuup = $db->Query("UPDATE `@#_shoplist` SET `shenyurenshu`='$shenyu',`canyurenshu` = '$canjia' WHERE id = '$v[id]'");
                        if (empty($renshuup)){
                            $code = 100;
                            $msg = "购买失败";
                            $json = array('code' => $code, 'msg' => $msg);
                            echo json_encode($json);die;
                        }
                    }
                }
                $ctatusup = $db->Query("UPDATE `@#_member_go_record` SET `status`='$status' WHERE code In ($codes)");
                if(!empty($ctatusup)){
                    $code = 200;
                    $msg = "购买成功";
                } else {
                    $code = 100;
                    $msg = "购买失败";
                }
                $json = array('code' => $code, 'msg' => $msg);
                echo json_encode($json);
                }
        }else{
            $json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
            echo json_encode($json);
        }
    }
}
