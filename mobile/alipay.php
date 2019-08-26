<?php
require_once "config.php";
// require_once "class/mysql_for_pay.php";

if(empty($_SESSION["user_id"])) {
    $Error->show('未登录，请登录后操作。');
    exit;
}

if(!isset($_GET["order_id"])){
    $order_id = 0;
    header("location:/?m=account&a=order");
    exit;
}else{
    $order_id = (int)$_GET["order_id"];
}

$result = $DB->GetRs("orders","*","where user_id=".(int)$_SESSION["user_id"]." and order_id=".$order_id);
if(!empty($result)) {
    $total_fee = $result["pay_total"];
    $order_sn=$result["order_sn"];
}else{
    $Error->show('无效的订单信息！');
    exit;
}

// 金额=0时自动付款
if($total_fee == 0){
    $Common->add_order_history($order_id,array(
        'pay_method' => 2,
        'status' => 1,
        'content' => '钱包支付成功',
    ));
    header("location:/?m=account&a=order_detail&order_id=".$order_id);
    exit;
}


$subject = '25BOY购物-'.$order_sn;
/*$pay['pay_sn'] = $order_sn;
$pay['pay_status'] = 'payment';//未付款
$pay['method'] = 1;//支付宝消费
$pay['money'] = -$total_fee;
$pay['user_id'] = $_SESSION["user_id"];
$pay["create_date"]=date('Y-m-d H:i:s');
$DB->Add("bag",$pay);*/


$sHtml = "正在跳转到支付宝付款...";
$sHtml.= "<div style='display:none'>";
$sHtml.= "<form id='paysubmit' name='alipaysubmit' action='http://www.25boy.cn/payment/alipay_mobile?pc' method='POST'>";
$sHtml.= "<input type='hidden' name='order_sn' value='".$order_sn."'/>";
// $sHtml.= "<input type='hidden' name='WIDout_trade_no' value='".$order_sn."'/>";
// $sHtml.= "<input type='hidden' name='WIDsubject' value='".$subject."'/>";
// $sHtml.= "<input type='hidden' name='WIDtotal_fee' value='".$total_fee."'/>";
//submit按钮控件请不要含有name属性
$sHtml = $sHtml."<input type='submit' value='submit'></form>";
$sHtml = $sHtml."<script>document.forms['paysubmit'].submit();</script>";
$sHtml = $sHtml."</div>";
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>商品支付 - 25BOY</title>
</head>
<body>
<?php
echo $sHtml;
?>
</body>
</html>