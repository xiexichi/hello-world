<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

if(isset($_SESSION["user_id"])&&!empty($_SESSION["user_id"])) {

    $pw = isset($_POST["pw"]) ? htmlspecialchars_decode($_POST["pw"]) : "";
    $type = isset($_POST["type"]) ? htmlspecialchars_decode($_POST["type"]) : "";
    $coupon_id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;

    // $type=receive 自动发放有密码代金券
    if($type == 'receive' && !empty($coupon_id)){
        $row = $DB->GetRs('coupon_user','coupon_user_id','where coupon_id='.$coupon_id.' AND user_id='.$_SESSION['user_id']);
        // if(empty($row)){
            $coupon = $DB->GetRs('coupon','start_date,exp_date','where coupon_id='.$coupon_id);
            $today = strtotime(date('Y-m-d',time()));
            if(!empty($coupon) && strtotime($coupon['start_date'])<=$today && strtotime($coupon['exp_date'])>=$today){
                $row = $DB->GetRs('coupon_user','coupon_pws','where coupon_id='.$coupon_id.' AND coupon_active=0 AND user_id=-1 order by coupon_user_id asc');
                if(!empty($row['coupon_pws'])){
                    $pw = $row['coupon_pws'];
                }
            }else{
                echo json_encode(array(
                    "status"=>"empty"
                ));
                exit;
            }
        // }else{
        //     echo json_encode(array(
        //         "status"=>"geted"
        //     ));
        //     exit;
        // }
    }

    // 优惠码
    if($pw==""){
        echo json_encode(array(
            "status"=>"nopw"
        ));
        exit;
    }

    // GT开头是奖品优惠码
    if(strpos($pw,'GT') === 0){

        $Table="prize_users";
        $Fileds = "*";
        $Condition = "where verifycode='".trim($pw)."' AND complete=0";
        $row = $DB->GetRs($Table,$Fileds,$Condition);
        if(empty($row)){
            echo json_encode(array(
                "status"=>"empty"
            ));
            exit;
        }else{
            if($row["user_id"]==-1){
                $result = $DB->Set($Table,array("user_id"=>$_SESSION["user_id"],'get_date'=>date('Y-m-d H:i:s',time())),$Condition);
                echo json_encode(array(
                    "status"=>"success"
                ));
                exit;
            }else{
                echo json_encode(array(
                    "status"=>"geted"
                ));
                exit;
            }
        }

    }else{

        $Table="coupon_user";
        $Fileds = "*";
        $Condition = "where coupon_pws='".trim($pw)."' AND coupon_active=0";
        $row = $DB->GetRs($Table,$Fileds,$Condition);
        if(empty($row)){
            echo json_encode(array(
                "status"=>"empty"
            ));
            exit;
        }else{
            if($row["user_id"]==-1){
                $result = $DB->Set($Table,array("user_id"=>$_SESSION["user_id"],'get_date'=>date('Y-m-d H:i:s',time())),$Condition);
                echo json_encode(array(
                    "status"=>"success"
                ));
                exit;
            }else{
                echo json_encode(array(
                    "status"=>"geted"
                ));
                exit;
            }
        }

    }

}else{
    echo json_encode(array(
        "status"=>"nologin"
    ));
    exit;
}