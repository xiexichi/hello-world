<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
// 不允许删除订单
echo json_encode(array(
    "status"=>"error"
));
exit;

if(!isset($_SESSION["user_id"])||$_SESSION["user_id"]=="") {
    echo json_encode(array(
        "status"=>"nologin"
    ));
    exit;
}
$order_id = isset($_POST["order_id"]) ? (int)$_POST["order_id"] : 0;

if($order_id==0){
    echo json_encode(array(
        "status"=>"no_order_id"
    ));
    exit;
}

$DB->Del("order_items","","","order_id=".(int)$order_id);
$DB->Del("orders","","","order_id=".(int)$order_id);



echo json_encode(array(
    "status"=>"success"
));