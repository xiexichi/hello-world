<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

if(!isset($_SESSION["user_id"])||(int)$_SESSION["user_id"]==0) {
    echo json_encode(array("status"=> "failed","msg"   => "请先登录"));exit;
}

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;
$pass     = isset($_GET['pass']) ? htmlspecialchars($_GET['pass']) : null;

//判断密码是否正确
$user = $DB->GetRs("users","*","where user_id=".$_SESSION["user_id"]);
if(!empty($user)) {
    //密码登录，判断密码是否正确
    if($user['password'] != md5($pass) && $user['password'] != $Base->pass_crypt($pass)) {
        echo json_encode(array("status"=> "failed", "msg"   => "密码不正确！"));exit;
    }
}else {
    echo json_encode(array("status"=> "failed", "msg"   => "没有用户信息！"));exit;
} 

if(empty($order_id)) {
    echo json_encode(array("status"=> "failed","msg"   => "没有订单信息！"));exit;
}

$order = $DB->GetRs("orders","*","where order_id=".$order_id);

if(!empty($order)) {
    $prices = (float)$order["pay_total"];
}else{
    echo json_encode(array("status"=> "failed","msg"=>"没有订单信息！"));exit;
}
if(isset($order['status']) && $order['status'] == -1){
    echo json_encode(array("status"=> "cancel","msg"   => "订单已取消！"));exit;
}
if(isset($order['status']) && $order['status'] != 0){
    echo json_encode(array("status"=> "failed","msg"   => "订单已支付！"));exit;
}

//用余额来支付
if($prices>0){

    $bag_total = (float)$user["bag_total"];

    if($bag_total>=$prices){

        $new_balance = $bag_total - $prices;
        //更新用户余额
        $result = $DB->Set("users",array("bag_total"=>$new_balance),"where user_id=".$_SESSION["user_id"]);

        if($result){
            update_order($prices,$order_id,$DB,$order,$new_balance);
            echo json_encode(array("status"=>"success",'order_sn'=>$order["order_sn"],'order_id'=>$order["order_id"]));exit;
        }else{
            echo json_encode(
                array("status"=>"error",'order_sn'=>$order["order_sn"],'order_id'=>$order["order_id"])
            );
            exit;
        }

    }else {
        echo json_encode(array("status"=> "failed", "msg"   => "余额不足！"));exit;
    }

}

function update_order($prices,$order_id,$DB,$order,$bag_total=0){
    //增加订单历史
    $DB->Add("order_history",array(
        "order_id"=>$order_id,
        "content"=>"余额支付成功（手机）",
        "status"=>1,
        "create_date"=>date('Y-m-d H:i:s')
    ));
    //更新订单状态
    $DB->Set("orders","status=1,pay_status=1,pay_date=NOW(),pay_method=2","WHERE order_id=".$order_id);
    //准备流水信息
    $business_code = $order['business_code'];
    $business = $DB->GetRs("business","business_name","WHERE business_code = '{$business_code}'");
    $note = $business['business_name']."用户消费(手机支付)";

    //付款成功，添加流水
    $DB->Add("bag",array(
        "pay_status"=>'paid',
        "pay_sn"=>$order["order_sn"],
        "method"=>'bag',
        "user_id"=>$_SESSION["user_id"],
        "create_date"=>date('Y-m-d H:i:s'),
        "money"=>-$prices,
        "type" => 'goods',
        "note"=>$note,
        "balance"=>$bag_total,
        "pay_date"=>date('Y-m-d H:i:s'),
        "business_code"=>$business_code,
        "client"=>'o2o'
    ));

    //更新用户消费总额
    $DB->Set("users","consume_total = consume_total + ".$prices,"where user_id=".$_SESSION["user_id"]);

}