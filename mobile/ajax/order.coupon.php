<?php
// 计算代金券优惠
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
//$_SESSION["user_id"] = 2;
if(isset($_SESSION["user_id"])&&!empty($_SESSION["user_id"])) {

    $coupon_id = isset($_POST["coupon_id"]) ? intval($_POST["coupon_id"]) : 0;
    $cart_ids = isset($_POST["cart_ids"]) ? trim($_POST["cart_ids"],',') : '';
    $sub_total = isset($_POST["sub_total"]) ? (float)$_POST["sub_total"] : 0;

    $Table="coupon";
    $Fileds = "*";
    $Condition = "where coupon_id='".$coupon_id."'";

    $row = $DB->GetRs($Table,$Fileds,$Condition);
    if(empty($row)){
        echo json_encode(array(
            "status"=>"empty"
        ));
        exit;
    }else{

        $result = $Common->get_coupon_price($coupon_id, $cart_ids, $sub_total);
        die(json_encode($result));
    }

}else{
    echo json_encode(array(
        "status"=>"nologin"
    ));
    exit;
}