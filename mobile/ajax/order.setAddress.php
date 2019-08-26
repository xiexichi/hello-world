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
$Fileds = "order_id,location,is_issuing,business_code";
$Condition = "where `client`='o2o' AND `order_id`=".$order_id;
$row = $DB->GetRs($Table,$Fileds,$Condition);
if(empty($row)){
    echo json_encode(array(
        "status"=>"no_order_id"
    ));
    exit;
}else{
    $order = $row;
}

// 接收数据
$data = array();
$data['receiver_name'] = isset($_POST["receiver_name"]) ? trim(htmlspecialchars_decode($_POST["receiver_name"])) : '';
$data['receiver_phone'] = isset($_POST["receiver_phone"]) ? trim(htmlspecialchars_decode($_POST["receiver_phone"])) : '';
$data['location'] = isset($_POST["location"]) ? trim(htmlspecialchars_decode($_POST["location"])) : '';

if(empty($data['receiver_name']) || empty($data['receiver_phone']) || empty($data['location'])){
    echo json_encode(array(
        "status"=>"empty_params"
    ));
    exit;
}


$res = $DB->Set("orders",$data,"where user_id={$_SESSION['user_id']} AND order_id={$order_id}");
if($res){
    // 线下代发订单，同步erp
    if($order['is_issuing'] == 1){
        include_once($_SERVER['DOCUMENT_ROOT']."/class/grerp.php");
        $gyerp = new gyerp();
        $erp = $gyerp->getOrderForErp($order_id);
        $result = $gyerp->add_order($erp);
    }
            
    //同步成功后，新增订单历史并记录行为
    $_history = array();
    $_history['order_id'] = $order_id;
    $_history['status']   = 1;
    $_history['content']  = "线下代发订单，用户补充地址";
    $_history['condition']= 'sync'; //同步
    $_history['create_date']= date("Y-m-d H:i:s", time());
    if(isset($result['success']) && $result['success'] == 1){
        $_history['content'] .= "，同步成功";
    }
    //增加状态为退货的订单历史
    $history_id = $DB->Add("order_history",$_history);

    //记录行为操作
    $_action = array();
    $_action['model']        = 'order';
    $_action['action']       = 'sync';
    $_action['account_type'] = 'user';
    $_action['relation_id']  = $order_id;
    $_action['history_id']   = $history_id;
    $_action['account_id']   = $_SESSION["user_id"]; //###power
    $_action['content']      = "用户填写地址，自动同步订单";
    $_action['ip']           = $Base->clientIp();
    $_action['business_code']= $order['business_code'];
    $_action['create_date']= date("Y-m-d H:i:s", time());
    $action_id = $DB->Add("business_action",$_action);

    echo json_encode(array(
        "status"=>"success"
    ));
    exit;
}

echo json_encode(array(
    "status"=>"error"
));