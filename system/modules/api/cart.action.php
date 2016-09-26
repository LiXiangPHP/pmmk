<?php

defined('G_IN_SYSTEM')or exit("no");

class cart extends SystemAction {

    /*加入购物车接口*/
    public function json_cartadd() {
        $sid = $_POST['sid'];
        $num = $_POST['num'];
        $uid = $_POST['uid'];
        if (!empty($sid)) {
            $db = System::load_sys_class('model');
            $code = 200;
            $msg = "添加成功";
            $dada = $db->Query("INSERT INTO `@#_shopcart` (`user_id`, `good_id`,`num`) VALUES ('$uid','$sid','$num')";
        } else {
            $code = 100;
            $msg = "添加失败";
        }
        $json = array('code' => $code, 'msg' => $msg);

        echo json_encode($json);
    }


     /*购物车接口*/
    public function json_cartshow() {
        $uid = $_POST['uid'];
        if (!empty($uid)) {
            $db = System::load_sys_class('model');
            $data = $db->GetOne("SELECT l.title,l.money,l.yunjiage,l.canyurenshu,l.shenyurenshu,l.thumb FROM `@#_shoplist` l,`@#_shopcart` c WHERE l.sid = c.good_id AND c.user_id = $uid");
        }
//        echo "<pre>";
//        print_r($data);die;
        if (!empty($data)) {
            $code = 200;
            $msg = "查询成功";
        } else {
            $code = 100;
            $msg = "查询失败";
        }
        $json = array('code' => $code, 'msg' => $msg, 'data' => $data);

        echo json_encode($json);
    }

}
