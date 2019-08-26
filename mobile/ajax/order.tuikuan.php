<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
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


$Table="orders";
$Fileds = "*";
$Condition = "where order_id=".$order_id;
$row = $DB->GetRs($Table,$Fileds,$Condition);
if(empty($row)){
    echo json_encode(array(
        "status"=>"no_order_id"
    ));
    exit;
}else{
    $order = $row;
}

//判断订单状态，如果为1，则可以申请退款
if($order['status'] != 1) {
    echo json_encode(array(
        "status"=>"refuse_refund"
    ));
    exit;
}

$Table="users";
$Fileds = "*";
$Condition = "where user_id=".$_SESSION["user_id"];
$row = $DB->GetRs($Table,$Fileds,$Condition);
if(empty($row)){
    echo json_encode(array(
        "status"=>"nologin"
    ));
    exit;
}else{
    $user = $row;
}


// ----------------生成退款单--------------------------------------------------------------

// 添加 调用gyerp类，生成退款单
include_once($_SERVER['DOCUMENT_ROOT']."/class/grerp.php");
$gyerp = new gyerp();

// 获取erp订单状态
// return 订单打包中，不能申请退款。
// if($order['reout']=='shenhe'){
//     echo json_encode(array("status"=>"packing"));
//     exit();
// }else{
    $tradeState = $gyerp->get_order_status($order['order_sn']);
    if($tradeState['shenhe']==1){
        $DB->Set("orders",array("reout"=>'shenhe'),"where order_id=".(int)$order_id);
        echo json_encode(array("status"=>"packing"));
        exit();
    }
// }

// erp退款单
$erp['outer_tid'] = $order['order_sn']; //平台单号
$erp['outer_refundid'] = 'T'.substr($order['order_sn'],-6,6);   //子订单号
$erp['refund_state'] = 1;                   // 0、取消退款 1、标识退款
$gyerp->trade_refund_update($erp);

// ----------------生成退款单--------------------------------------------------------------


$data['status'] = 3;
$data['content'] = '退款审核';
$data['order_id'] = $order_id;
$data['create_date'] = date('Y-m-d H:i:s');

$DB->Add("order_history",$data);
$DB->Set("orders",array("status"=>$data['status']),"where order_id=".$order_id);

echo json_encode(array(
    "status"=>"success"
));