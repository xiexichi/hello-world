<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$order_id = isset($_POST["order_id"]) ? (int)$_POST["order_id"] : 0;
if(!isset($_SESSION["user_id"])||empty($_SESSION["user_id"])) {
    echo json_encode(
        array("status"=>"nologin")
    );
    exit;
}

$returnjson = array(
    "status"=>"error",
    'total'=>'0.00'
);

if($order_id==0){
    echo json_encode($returnjson);
    exit;
}

$prices = 0;
$result = $DB->GetRs("o2o_order","*","where order_id=".$order_id);
if(!empty($result)) {
    $prices = $result["pay_total"];
}else{
    echo json_encode($returnjson);
    exit;
}

if($prices>=0){
    $result = $DB->GetRs("users","*","where user_id=".$_SESSION["user_id"]);
    if(!empty($result) && ($result['bag_total']>0 || $result['bag_total']>=$prices)) {
        $bag_total = $result["bag_total"];
        $returnjson["total"]=$bag_total;

        if($bag_total>=$prices){
            $returnjson["status"]="success";
        }else{
            $returnjson["status"]="merge";
        }

    }else{
        echo json_encode($returnjson);
        exit;
    }
}

echo json_encode($returnjson);
exit;