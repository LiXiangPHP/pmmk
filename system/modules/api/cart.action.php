<?php
defined('G_IN_SYSTEM')or exit("no");

class cart extends SystemAction {

    /*加入购物车接口*/
    public function json_cartadd() {
        $id = $_POST['id'];
        $num = $_POST['num'];
        $uid = $_POST['uid'];
        $info = System::token_uid($uid);
        if ($info['code']==200) {
            $db = System::load_sys_class('model');
            $uidm = $db->GetOne("SELECT * FROM `@#_shopcart` WHERE good_id = '$id' AND user_id = '$info[uid]'");
            if(!empty($uidm)){
                $numm =$num + $uidm['num'];
                $numadd = $db->Query("UPDATE `@#_shopcart` SET num ='$numm' WHERE good_id = '$id' AND user_id = '$info[uid]'");
                if(!empty($numadd)){
                $code = 200;
                $msg = "添加成功";
                }
            }elseif (!empty($id)&!empty($num)&!empty($info['uid'])&($db->Query("INSERT INTO `@#_shopcart` (`user_id`, `good_id`,`num`) VALUES ('$info[uid]','$id','$num')")!=false)) {
                $code = 200;
                $msg = "添加成功";
            } else {
                $code = 100;
                $msg = "添加失败";
            }
            $json = array('code' => $code, 'msg' => $msg);
            echo json_encode($json);
        }else {
            $json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
            echo json_encode($json);
        }
    }

    /*查看购物车接口*/
    public function json_cartshow() {
        $uid = $_POST['uid'];
        $info = System::token_uid($uid);
        if ($info['code']==200) {
                $db = System::load_sys_class('model');
                $data = $db->GetList("SELECT l.id,l.title,l.money,l.yunjiage,l.canyurenshu,l.shenyurenshu,l.thumb,c.num FROM `@#_shoplist` l,`@#_shopcart` c,`@#_member` m WHERE m.uid = '$info[uid]' AND l.id = c.good_id AND c.user_id =  m.uid");
            if (!empty($data)) {
                $code = 200;
                $msg = "查询成功";
            } else {
                $code = 100;
                $msg = "查询失败";
            }
            $json = array('code' => $code, 'msg' => $msg, 'data' => $data);
            echo json_encode($json);
        }else {
            $json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
            echo json_encode($json);
        }
    }


    /*购物车删除接口*/
    public function json_cartdel(){
        $id = $_POST['id'];
        $uid = $_POST['uid'];
        $info = System::token_uid($uid);
        if ($info['code']==200) {
            $db = System::load_sys_class('model');
            if ($db->Query("DELETE FROM `@#_shopcart` WHERE user_id='$info[uid]' and good_id in ($id)") !== false) {
                $code = 200;
                $msg = "删除成功";
            } else {
                $code = 100;
                $msg = "删除失败";
            }
            $json = array('code' => $code, 'msg' => $msg);
            echo json_encode($json);
        }else {
            $json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
            echo json_encode($json);
        }
    }


    /*购物车购买接口*/
    public function json_cartbuy(){
        $data = $_POST['data'];
        $type = $_POST['type'];
        $uid = $_POST['uid'];
        $info = System::token_uid($uid);
        if ($info['code']==200) {
            $Sdata = json_decode(stripslashes($data), true);
//            print_r($Sdata);
            $shopids = '';
            if (is_array($Sdata)) {
                foreach ($Sdata as $k => $v) {
                    foreach ($v as $i => $j) {
                        $newdata [] = $j;
                    }
                }
            }
//            print_r($newdata);
            $MoenyCount ='';
            foreach ($newdata as $v) {
                $MoenyCount = $MoenyCount + $v['personcount'];
            }
//            print_r($MoenyCount);
            $cartdata['moenycount'] = $MoenyCount;
//            print_r($cartdata);
            foreach ($newdata as $v) {
                $shopids = $shopids . ',' . $v['id'];
                $shopids = trim($shopids, ',');
            }
            $time = time();
            $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
            $dingdancode = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
//            print_r($dingdancode);
            if(iconv_strlen($shopids)>1){
               $dingdancode_tmp = 1;	//多个商品相同订单
            }else{
               $dingdancode_tmp = 0;	//单独商品订单
            }
            echo "\n";
//            print_r($dingdancode_tmp);
            echo "\n";
//            print_r($shopids);
//            print_r($info['uid']);
            $db = System::load_sys_class('model');
            $member = $db->GetOne("SELECT * FROM `@#_member` WHERE `uid` = '$info[uid]'");
//            print_r($member);
            $username = $member['username'];
            $uphoto = $member['img'];
            $ip = $member['user_ip'];
            $shoplist = $db->GetList("SELECT * FROM `@#_shoplist` WHERE `id` IN ('$shopids')");
//            print_r($shoplist);
            foreach ($shoplist as $v) {
                $shoptitle = $shoptitle . ',' . $v['title'];
                $shoptitle = trim($shoptitle, ',');
            }
//            print_r($shoptitle);
            foreach ($newdata as $v) {
                $shopqishu = $shopqishu . ',' . $v['date'];
                $shopqishu = trim($shopqishu, ',');
            }
//            print_r($shopqishu);
            if($type = 1){
                $pay_type = 'zhanghu';
            }elseif($type = 2){
                $pay_type = 'weixin';
            }elseif($type = 3){
                $pay_type = 'zhifubao';
            }else{
                echo '支付方式不正确';
            }
//            echo $pay_type;
            $status='未付款,未发货,未完成';
            $cartdata['pay_type'] = $pay_type;
            $db = System::load_sys_class('model');
            $order = $db->Query("INSERT INTO `@#_member_go_record` (`code`,`code_tmp`,`username`,`uphoto`,`uid`,`shopid`,`shopname`,`shopqishu`,`gonumber`,`goucode`,`moneycount`,`pay_type`,`ip`,`status`,`time`) VALUES ('$dingdancode','$dingdancode_tmp','$username','$uphoto','$info[uid]','$shopids','$shoptitle','$shopqishu','$MoenyCount','$dingdancode','$MoenyCount','$pay_type','$ip','$status','$time')");
            $data = $db->GetOne("SELECT `moneycount`,`pay_type`,`code` FROM `@#_member_go_record` WHERE `uid` = '$info[uid]' AND `time`= '$time'");
            if(!empty($shopids)&!empty($info['uid'])&!empty($data)){
                $code = 200;
                $msg = "订单提交成功";
//                print_r($data);
            } else {
                $code = 100;
                $msg = "订单提交失败";
            }
                $json = array('code' => $code, 'msg' => $msg,'data'=> $data);
                echo json_encode($json);

        }else{
            $json = array('code' => 300, 'msg' => '请登录', 'data' => $data);
            echo json_encode($json);
        }
    }
}


