<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$money = isset($_POST['money']) ? (float)$_POST['money'] : 0;
$store_name = isset($_COOKIE['store_name']) ? htmlspecialchars($_COOKIE['store_name']) : NULL;
$store_code = isset($_COOKIE['store_code']) ? htmlspecialchars($_COOKIE['store_code']) : NULL;

if(empty($_SESSION["user_id"])) {
    echo json_encode(array(
        "status"=>"nologin",
        'msg' => '您还没登录，请登录后操作！'
    ));
    exit;
}

if(empty($money)) {
    echo json_encode(array(
        "status"=>"error",
        'msg' => '数据出错，请重试。'
    ));
    exit;
}

// 查询余额
$row = $DB->GetRs("users","phone,nickname,bag_total","where user_id=" . $_SESSION["user_id"]);
$bag_total = !empty($row) ? $row["bag_total"] : 0;

//余额不足以支付
if($bag_total<$money){
    echo json_encode(array(
        "status"=>"no_balance",
        'msg' => '余额（¥'.$bag_total.'）不足以本次支付！<br/>请充值，或者使用其它支付方式。'
    ));
    exit;
}


// 添加钱包流水
$new_balance = $bag_total - $money;
$pay_sn = $Base->build_order_no('B');
$DB->Add("bag",array(
    "pay_status"=>'paid',
    "pay_sn"=>$pay_sn,
    "method"=>'bag',
    "user_id"=>$_SESSION["user_id"],
    "create_date"=>date('Y-m-d H:i:s'),
    "money"=>-$money,
    "type" => 'paycode',
    "note"=>$store_name.'扫码支付',
    "balance"=>$new_balance,
    "pay_date"=>date('Y-m-d H:i:s'),
    'store_code'=>$store_code
));
//更新用户消费总额
$DB->Set("users","bag_total=".$new_balance.", consume_total=consume_total+".$money,"where user_id=".$_SESSION["user_id"]);

echo json_encode(
    array(
        "status"=>"success",
        'msg'=>'付款成功，请出示交易号给商家登记！<p style="font-size:1.4em;color:#ff6400;margin-top:5px;">'.$pay_sn.'</p>',
        "pay_sn"=>$pay_sn,
        "money"=>$money,
        "bag_total"=>$bag_total,
        "phone"=>$phone,
        "nickname"=>$nickname,
    )
);
exit;