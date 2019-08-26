<?php
/*
* 付款处理
* 第三方付款跳转
* 钱包付款直接完成支付
*/
require_once "config.php";

if(empty($_SESSION["user_id"])) {
    $Error->show('未登录，请登录后操作。');
    exit;
}

// 打印函数
function p($data){
    echo "<pre>";
    print_r($data);
}
function pe($data){
    p($data);
    exit;
}


$method = isset($_GET['method']) ? htmlspecialchars($_GET['method']) : NULL;
$order_sn = isset($_GET['sn']) ? htmlspecialchars($_GET['sn']) : NULL;
$salt = isset($_SESSION['salt']) ? htmlspecialchars($_SESSION['salt']) : NULL;
$get_salt = isset($_GET['salt']) ? htmlspecialchars($_GET['salt']) : NULL;

// C开头的是通过js.cookie记录的
if(substr($get_salt,0,1)=='C'){
    $salt = $_COOKIE['salt'];
}

if(empty($method) || empty($order_sn) || empty($salt) || $salt!=$get_salt){
    header("location:/?m=account&a=o2o_order");
    exit;
}

// 查找订单数据
// 写原生sql
$sql = "SELECT a.user_id,b.status,a.order_id,b.order_type,b.pay_status,b.pay_total FROM o2o_order a JOIN o2o_order_join b ON a.order_id = b.order_id WHERE a.order_sn = '{$order_sn}' AND a.user_id = {$_SESSION["user_id"]} LIMIT 1";
// 获取订单数据
$order = $DB->fetch_assoc($DB->query($sql));

if(empty($order['order_id'])){
    $Error->show('找不到订单：'.$order_sn);
    exit;
}else{
    $order_id = isset($order['order_id'])?$order['order_id']:0;
    $pay_status = isset($order['pay_status'])?$order['pay_status']:0;
    $pay_total = isset($order['pay_total'])?(float)$order['pay_total']:0;
    $user = $DB->GetRs("users","bag_total","where user_id=" . (int)$_SESSION["user_id"]);
    $bag_total = isset($user['bag_total'])?(float)$user['bag_total']:0;
}

// 重复付款提示
if($pay_status==1){
    $Error->show('订单已经支付，请勿重复付款！');
    exit;
}

// 金额=0时自动付款
if($pay_total == 0){
    $Common->add_order_history($order_id, array(
        'pay_method' => 2,
        'status' => 1,
        'content' => '钱包支付成功',
    ));
    header("location:/?m=account&a=o2o_order_detail&order_id=".$order_id);
    exit;
}

$subject = '25BOY购物-'.$order_sn;


/*
* 选择付款方式
* method : bag/alipay/weixin
* @ bag : 钱包付款，直接完成并更新订单状态
* @ alipay : 跳转到支付宝付款，支付宝返回通知 miao:alipay/notify_url.mobile.php 处理订单
* @ weixin : 调用JSAPI付款，在页面弹出并确认付款，微信返回通知 wxpay_notify.php 处理订单
*/
// 钱包付款
if($method == 'bag'){
    // 钱包付款不能在这里操作，因为需要输入密码认证的
    $Error->show('请选择正确的支付方式！');
    exit;
    if($bag_total >= $pay_total){
        //余额足已支付
        $new_balance= $bag_total - $pay_total;
        //更新余额
        $DB->Set("users","bag_total=".(float)$new_balance,"WHERE user_id=".$_SESSION["user_id"]);
        //更新订单历史
        $DB->Add("order_history",array(
            "order_id"=>$order_id,
            "content"=>"钱包支付成功（手机）",
            "status"=>1,
            "create_date"=>date('Y-m-d H:i:s')
        ));
        //更新订单状态
        $DB->Set("orders","status=1,pay_status=1,pay_date=NOW(),pay_method=2","WHERE order_id=".$order_id);
        //更新钱包付款状态
        $DB->Add("bag",array(
            "pay_status"=>'paid',
            "pay_sn"=>$order_sn,
            "method"=>'bag',
            "user_id"=>$_SESSION["user_id"],
            "create_date"=>date('Y-m-d H:i:s'),
            "money"=>$pay_total,
            "type" => 'goods',
            "note"=>'手机支付'
        ));

        header("location:/?m=account&a=o2o_order_detail&order_id=".$order_id);
        exit();
    }else{
        $Error->show("<p>支付失败，余额 $bag_total 不足 $pay_total</p><p>请充值后付款，或者使用第三方（微信/支付宝）在线支付。</p>");
        exit();
    }
}   // end method = bag

// 支付宝付款 
else if($method == 'alipay') 
{

    $sHtml = '<html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <title>支付 - 25BOY</title>
    </head>
    <body>';
    $sHtml.= "<div style='display:none'>";
    $sHtml.= "<form id='paysubmit' name='alipaysubmit' action='http://www.25boy.cn/payment/alipay_mobile_o2o_order?pc' method='POST'>";
    $sHtml.= "<input type='hidden' name='sn' value='".$order_sn."'/>";
    // $sHtml.= "<input type='hidden' name='WIDout_trade_no' value='".$order_sn."'/>";
    // $sHtml.= "<input type='hidden' name='WIDsubject' value='25BOY充值-".$order_sn."'/>";
    // $sHtml.= "<input type='hidden' name='WIDtotal_fee' value='".$recharge."'/>";
    // $sHtml.= "<input type='hidden' name='WIDbody' value='".$data['note']."'/>";
    $sHtml = $sHtml."<input type='submit' value='submit'></form>";
    $sHtml = $sHtml."<script>document.forms['paysubmit'].submit();</script>";
    $sHtml = $sHtml."</div></body></html>";

    echo $sHtml;
    exit;
}   // end method = alipay

// 微信付款 
else if($method == 'weixin')
{
    if(!is_weixin()){
        $Error->show('<p>微信支付只适用于微信内使用，请关注微信公众号：25BOY</p><p>或者使用其它付款方式。</p>');
        exit;
    }

    require_once "pay/wx/WxPay.Api.php";
    require_once "pay/wx/WxPay.JsApiPay.php";
    require_once "log.php";
    //初始化日志
    $logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');
    $log = Log::Init($logHandler, 15);

    // ①、获取用户openid
    $openId = isset($_SESSION['openid'])?$_SESSION['openid']:NULL;
    $tools = new JsApiPay();
    if(empty($openId)){
        $openId = $tools->GetOpenid("http://".$base_url."/o2o_pay.php?method=weixin&sn=".$order_sn);
    }

    $prices = strval($pay_total*100);
    $prices_show = number_format($pay_total,2);
    $body = $subject;
    $detail = '';

    // 获取订单项数据
    $Table = "o2o_order_item";
    $Fileds = "b.size_prop,b.color_prop,c.product_name,o2o_order_item.quantity";
    $Where = "JOIN stock b ON o2o_order_item.sku_sn = b.sku_sn AND o2o_order_item.sku_prop = b.sku_prop JOIN products c ON c.product_id = o2o_order_item.product_id WHERE order_id = {$order_id} GROUP BY o2o_order_item.item_id";
    $orderItems = $DB->GetAll($Table, $Fileds, $Where);

    // 整合商品详情
    foreach ($orderItems as $k => $v) {
        $detail .= $v["product_name"].",".$v["size_prop"].",".$v["color_prop"].",".$v["quantity"]."件;";
    }

    $detail = $Base->cn_substr_utf8($detail,120);
    $attach = $order_id;
    $productdescription = "25BOY男装";

    //②、统一下单
    $input = new WxPayUnifiedOrder();
    $input->SetBody($body);
    $input->SetDetail($detail);
    $input->SetAttach($attach);
    $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
    $input->SetTotal_fee((int)$prices);
    $input->SetTime_start(date("YmdHis"));
    // $input->SetTime_expire(date("YmdHis", time() + 600));
    $input->SetGoods_tag($productdescription);
    // $input->SetNotify_url("http://".$base_url."/wxpay_notify.php");
    $input->SetNotify_url("https://api.25boy.cn/payment/weixinNotify/otoconsume/wap");
    $input->SetTrade_type("JSAPI");
    $input->SetOpenid($openId);
    $wxorder = WxPayApi::unifiedOrder($input,30);
    // print_r($wxorder);exit;
    if(!array_key_exists("appid", $wxorder)
        || !array_key_exists("prepay_id", $wxorder)
        || $wxorder['prepay_id'] == "")
    {
        $Error->show('微信接口返回错误，请重试！');
        exit;
    }
    $jsApiParameters = $tools->GetJsApiParameters($wxorder);
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>25BOY - 微信支付</title>
    <script type="text/javascript">
        /*调用微信JS api 支付*/
        function jsApiCall()
        {
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                <?php echo $jsApiParameters; ?>,
                function(res){
                    WeixinJSBridge.log(res.err_msg);
                    /*alert(res.err_code+res.err_desc+res.err_msg);*/
                    if(res.err_msg=="get_brand_wcpay_request:ok"){
                        /*alert("支付成功")*/
                        showrequest("<?php echo $order_id; ?>",1)
                    }else{
                        showrequest("<?php echo $order_id; ?>",0)
                        /*alert("支付失败")*/
                    }
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
            window.location.href = "http://<?php echo $base_url;?>/?m=account&a=o2o_order"
        }

        function showrequest(orderid,status){
            window.location.href = "http://<?php echo $base_url;?>/?m=account&a=o2o_order"
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

<?php
}   // end method=weixin

// 删除salt
unset($_SESSION['salt']);
setcookie("salt",NULL,time()-1);
?>