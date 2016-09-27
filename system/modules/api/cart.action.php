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
            if (!empty($id)&!empty($num)&!empty($info['uid'])) {
                $db = System::load_sys_class('model');
                $code = 200;
                $msg = "添加成功";
                $dada = $db->Query("INSERT INTO `@#_shopcart` (`user_id`, `good_id`,`num`) VALUES ('$info[uid]','$id','$num')");
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
                $data = $db->GetList("SELECT l.id,l.title,l.money,l.yunjiage,l.canyurenshu,l.shenyurenshu,l.thumb FROM `@#_shoplist` l,`@#_shopcart` c,`@#_member` m WHERE l.id = c.good_id AND m.uid = '$info[uid]'");
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
}


