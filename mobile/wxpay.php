<?php
require_once "config.php";
// require_once "class/mysql_for_pay.php";
// include_once("error.php");

require_once "pay/wx/WxPay.Api.php";
require_once "pay/wx/WxPay.JsApiPay.php";
require_once "log.php";


//初始化日志
$logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);


if(empty($_SESSION["user_id"])) {
    $Error->show('未登录，请登录后操作。');
    exit;
}
if(!isset($_GET["order_id"])){
    $order_id = 0;
    header("location:/?m=account&a=order");
    exit;
}else{
    $order_id = $_GET["order_id"];
}

//echo "<br/>";
//echo $prices;
//echo "<br/>";
//echo $body;
//echo $_SERVER['QUERY_STRING'];


//①、获取用户openid
$tools = new JsApiPay();
$openId = $tools->GetOpenid("http://".$base_url."/wxpay.php?order_id=".$order_id);

$result = $DB->GetRs("orders","*","where user_id=".(int)$_SESSION["user_id"]." and order_id=".$order_id);
if(!empty($result)) {

    // 金额=0时自动付款
    if($result["pay_total"] == 0){
        $Common->add_order_history($order_id,array(
            'pay_method' => 2,
            'status' => 1,
            'content' => '钱包支付成功',
        ));
        header("location:/?m=account&a=order_detail&order_id=".$order_id);
        exit;
    }
    $prices = strval($result["pay_total"]*100);
    $prices_show = $result["pay_total"];
    $body="25BOY购物-".$result["order_sn"];
    $detail = '';

    $Table="v_order_items";
    $Fileds = "*";
    $Row = $DB->Get($Table,$Fileds,"where order_id=".$order_id,0);
    $Row = $DB->result;
    $RowCount = $DB->num_rows($Row);
    if($RowCount!=0){
        while($result = $DB->fetch_assoc($Row)) {
            //array_push($db_array,$result["id"]);
            $detail .= $result["product_name"].",".$result["size_prop"].",".$result["color_prop"].",".$result["num"]."件；";
        }
    }
    $detail = $Base->cn_substr_utf8($detail,120);

    $attach = $order_id;
    $productdescription = "25BOY男装";
}else{
    $Error->show('无效的订单信息！');
    exit;
}

//$prices_show = number_format($prices,2);
//exit;


//$body = "body";
//$attach = "attach";
//$prices = 1;
//$productdescription ="productdescription";

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody($body);
// $input->SetDetail($detail);
$input->SetAttach($attach);
$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
$input->SetTotal_fee((int)$prices);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag($productdescription);
$input->SetNotify_url("http://".$base_url."/wxpay_notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input,30);
//echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
//printf_info($order);

if(!array_key_exists("appid", $order)
    || !array_key_exists("prepay_id", $order)
    || $order['prepay_id'] == "")
{
    header("location:/?m=account&a=order");
}

$jsApiParameters = $tools->GetJsApiParameters($order);
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>微信支付 - 25BOY</title>
    <script type="text/javascript">
        //调用微信JS api 支付
        function jsApiCall()
        {
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                <?php echo $jsApiParameters; ?>,
                function(res){
                    WeixinJSBridge.log(res.err_msg);
                    //alert(res.err_code+res.err_desc+res.err_msg);

                    if(res.err_msg=="get_brand_wcpay_request:ok"){
                        //alert("支付成功")
                        showrequest("<?php echo $order_id; ?>",1)
                    }else{
                        showrequest("<?php echo $order_id; ?>",0)
                        //alert("支付失败")
                    }
                    //alert("")
                }
            );
        }

        function callpay() {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                jsApiCall();
            }
        }

        function latetopay(){
            window.location.href = "http://<?php echo $base_url;?>/index.php?m=account&a=order"
        }

        function showrequest(orderid,status){
            window.location.href = "http://<?php echo $base_url;?>/index.php?m=account&a=order"
        }
    </script>

    <style>
        body,html{
            background: #da3335; margin:0; padding:0;
            display: box;
            box-align: center;
            display: -webkit-box;
            -webkit-box-align: center;
            overflow: hidden;
            height: 100%;
        }
        .mainbox {
            width: 94%; margin-left:auto; margin-right:auto; margin-top:-60px; height: auto; overflow: hidden; background: #fff;
            -webkit-box-shadow:0 0 10px rgba(0,0,0,0.1);
            -moz-box-shadow:0 0 10px rgba(0,0,0,0.1);
            box-shadow:0 0 10px rgba(0,0,0,0.1);  border:1px solid #2c7f21; border-radius: 8px;
        }
        .mainbox .inbox {
            padding:20px;
        }
        .mainbox .inbox .title { padding-bottom:15px;}
        .mainbox .inbox .pricebox {border:1px solid #ccc; background: url("/pay/wx/img/pricebg.gif")}
        .btnpay,.btnlate {
            width:40%; height:50px; border-radius: 5px; background-color:#da3335;
            border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px; margin: 0 5px;
        }
        .btnlate {
            width:40%; background-color:#ccc;
        }
    </style>
</head>
<body>
<div class="mainbox">
    <div class="inbox">
        <div class="title"><img src="/pay/wx/img/pay-title.jpg" width="100%" /></div>
        <div class="pricebox">
            <center style="color:#9ACD32; padding:15px 0;">你将要支付的总金额为</center>
            <center style="color:#000; font-size: 32px; border-bottom: 1px solid #ccc; padding-bottom: 15px;">￥<?php echo $prices_show;?>&nbsp;</center>
        </div>
        <div style="line-height: 24px; border-bottom: 1px dotted #ccc; padding: 10px 0; color:#666">
            订单详情如下：<?php echo $body;?>
        </div>

        <center style="padding:20px;">
            <button class="btnlate" type="button" onclick="latetopay()" >稍后支付</button>
            <button class="btnpay" type="button" onclick="callpay()" >立即支付</button>
        </center>
        <center style="color:#999; font-size: 12px;">
            选择稍后付款，你可以在我的订单里选择该笔订单再付款。
        </center>

    </div>
</div>
</body>
</html>