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
                $Sdata = $db->GetList("SELECT l.id,l.title,l.money,l.yunjiage,l.canyurenshu,l.shenyurenshu,l.thumb,c.num FROM `@#_shoplist` l,`@#_shopcart` c,`@#_member` m WHERE m.uid = '$info[uid]' AND l.id = c.good_id AND c.user_id =  m.uid");
            foreach($Sdata as $v) {
                if($v['thumb']) {
                    $v['thumb'] = "gangmaduobao.com/statics/uploads/".$v['thumb'];
                }
                $data['data'][] = $v;
            }
            if (!empty($data)) {
                $code = 200;
                $msg = "查询成功";
                $json = array('code' => $code, 'msg' => $msg, 'data' => $data);
                echo json_encode($json);
            } else {
                $code = 400;
                $msg = "查询失败";
                $json = array('code' => $code, 'msg' => $msg);
                echo json_encode($json);
            }
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
}


