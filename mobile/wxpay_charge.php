<?php
require_once "config.php";
require_once "pay/wx/WxPay.Api.php";
require_once "pay/wx/WxPay.JsApiPay.php";
require_once "log.php";

include_once($_SERVER['DOCUMENT_ROOT']."/class/Flow.php");
// 新版流水
$flow = new Flow();

/**
* 设置初始值
* @param  val   [变量]
* @param  mixed [初始值]
* @param  bool  [是否返回]
* @param  bool  [表示零值是否需要初始化]
*/
if ( ! function_exists('set_init'))
{
    function set_init(&$data, $val = '', $ifZeroisEmpty = TRUE)
    {
        if (is_array($val)) {
            $data = empty($data) ? $val : $data;
        }elseif (is_int($val)) {
            if ($ifZeroisEmpty)
                $data = empty($data) ? $val : intval(trim($data));
            else 
                $data = empty($data) && $data != '0' ? $val : intval(trim($data));
        }else {
            if ($ifZeroisEmpty) 
                $data = empty($data) ? $val : trim($data);
            else 
                $data = empty($data) && $data != '0' ? $val : trim($data);
        }
        return $data;
    }
}


if(empty($_SESSION["user_id"])) {
    $Error->show('未登录，请登录后操作。');
    exit;
}

$recharge = isset($_GET['recharge'])?(float)$_GET['recharge']:0;
$from_order_sn = isset($_GET['sn'])?htmlspecialchars($_GET['sn']):NULL;
$salt = isset($_SESSION['salt']) ? htmlspecialchars($_SESSION['salt']) : NULL;
$get_salt = isset($_GET['salt']) ? htmlspecialchars($_GET['salt']) : NULL;

// C开头的是通过js.cookie记录的
if(substr($get_salt,0,1)=='C'){
    $salt = $_COOKIE['salt'];
}

if(empty($recharge) && empty($from_order_sn) || empty($salt) || $salt!=$get_salt){
    header("location:/?m=account");
    exit;
}


if(is_weixin()){
    if(empty($_SESSION['openid'])){
        $Error->show('<p>用户未绑定微信帐号</p><p>请在微信内打开并绑定帐号，或者选择支付宝付款</p>');
        exit;
    }
}else{
    $Error->show('请在微信内访问。','','/?m=account');
    exit;
}

//初始化日志
$logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

//①、获取用户openid
$openId = isset($_SESSION['openid'])?$_SESSION['openid']:NULL;
$tools = new JsApiPay();
if(empty($openId)){
    $return_url = "http://".$base_url."/wxpay_charge.php?".$_SERVER['QUERY_STRING'];
    $openId = $tools->GetOpenid($return_url);
    exit;
}


/*
* 合并付款
* 通过订单号查询得出需要充值金额
*/
$merge_payment = FALSE;
$order_type = substr($from_order_sn,0,1);
$bag_id = 0;
$user = $DB->GetRs("users","bag_total,pid","where user_id=" . (int)$_SESSION["user_id"]);
$business_qrcode = '';//记录扫码(fr=business&ch=xxxx)
$business_code = '';

switch ($order_type) {
    case 'R': case 'F': case 'S':case 'D':
        // 下单时发起付款
        if(!empty($from_order_sn)){
            $order = $DB->GetRs("orders","order_id,pay_total,order_total,business_code,business_qrcode","where pay_status=0 AND user_id=" . (int)$_SESSION["user_id"] . " AND order_sn = '".$from_order_sn."'");
            if(empty($order['order_id'])){
                $Error->show('找不到订单：'.$from_order_sn);
                exit;
            }
            $business_code = empty($order['business_code'])?$business_code:$order['business_code'];
            $business_qrcode = empty($order['business_qrcode'])?$business_qrcode:$order['business_qrcode'];

            $merge_payment = TRUE;
            $recharge = (float)$order['pay_total']-(float)$user['bag_total'];
            // 余额足以支付，使用钱包付款
            /*if($user['bag_total'] >= $recharge){
                header("location:/pay.php?method=bag&sn=".$from_order_sn."&salt=".$salt);
                exit;
            }*/

            $bag = $DB->GetRs('bag','bag_id,pay_sn,note',"WHERE note='合并付款-".$from_order_sn."'");
            // 金额变化时更新
            /*if(isset($bag['money']) && $recharge>0 && $recharge != $bag['money']){
                $DB->Set("bag","money = ".$recharge,"where bag_id=".$bag_id);
            }*/
        }
        break;

    case 'B':
        // 在钱包记录发起付款
        $bag = $DB->GetRs("bag","bag_id,money,pay_status,method,pay_sn,note","where user_id=" . (int)$_SESSION["user_id"] . " AND pay_sn = '".$from_order_sn."'");
        if($bag['pay_status'] == 1){
            $Error->show('订单已付款，请勿重复支付！','','/?m=account&a=balance&c=cz');
            exit;
        }
        // 如果是合并支付，
        if(isset($bag['note']) && strpos($bag['note'],'合并付款') !== FALSE){
        }
        $recharge   = $bag['money'];
        $bag_id     = $bag['bag_id'];
        $order_sn   = $bag['pay_sn'];
        break;

    default:
        # code...
        break;
}

if(empty($recharge)){
    header("location:/?m=account&a=balance");
    exit;
}


// 添加账户流水记录
if(empty($bag['bag_id'])){
    $order_sn = $Base->build_order_no('B');
    if($recharge>0){
        $data = array();
        $data['pay_status'] = 'payment';                                                // 状态 'payment','paid','close'
        $data['type']       = 'prepaid';                                                // 交易类型，预付prepaid/商品消费goods/退款refund/运费ship_fee
        $data['pay_sn']     = $order_sn;                                                // 交易号
        $data['user_id']    = $_SESSION["user_id"];                                     // 用户ID
        $data['money']      = $recharge;                                                // 金额
        $data['method']     = 'weixin';                                                 // 支付方式 alipay/bag/weixin
        $data['balance']    = isset($user['bag_total'])?$user['bag_total']:0;           // 当前余额
        $data['create_date'] = date('Y-m-d H:i:s',time());
        $data['business_code'] = $business_code;
        // 隐性消费用这个代替business_code,流水不再出现在线下
        $data['business_qrcode'] = $business_qrcode;
        if($merge_payment===TRUE){
            $data['note'] = '合并付款-'.$from_order_sn;
        }
        $result = $DB->Add('bag',$data);
        $bag_id = $DB->insert_id();

        // 新版流水
        $flow->addPrepaid($data);
    }
}else{
    $bag_id = $bag['bag_id'];
    $order_sn = $bag['pay_sn'];
}


/* *********************************************************************
 * 充值返佣记录,第一次发起充值时记录
 * *********************************************************************/
$user_id = (int)$_SESSION["user_id"];
$recharge_price = $recharge;
$pay_sn = $order_sn;
if(empty($bag['bag_id'])){
    //会员充值才有返佣
    $seller = $DB->GetRs("seller","id","WHERE user_id = $user_id");
    if(empty($seller) && !empty($user['pid'])) {
        /*if(!empty($user['pid'])) {
            $promote_id = $user['pid'];
        }else {
            $promote_id = base64_decode($_COOKIE['rememberMe']);
            //将推广者id更新到用户
            $DB->Set("users","pid=".$promote_id,"where user_id=".$user_id);
        }*/

        $promote_id = $user['pid'];
        $promote = $DB->GetRs("promote","promote_id,is_frozen","WHERE promote_id = $promote_id");
       
       //冻结账户不可收益
        if(!empty($promote) && !$promote['is_frozen']) {

            //首次充返
            $promote_first = $Common->get_beyond_first($promote_id,5);
            //后续充返
            $beyond_recharge = $Common->get_beyond_recharge($promote_id);
            //判断是否首次充值
            $row = $DB->GetRs("bag","*","WHERE user_id = ".$user_id." AND pay_status = 'paid' AND type = 'prepaid'");
            if(empty($row) && !empty($promote_first)) {
                //首次充值
                $commission_rate = $promote_first['commission_rate'];
                $commission      = round($recharge_price * $commission_rate / 100,2);
                $pplan_id        = $promote_first['pplan_id'];

                //返佣下单记录
                $DB->Add('promote_order',array(
                        'pplan_id'       => $pplan_id,
                        'pay_sn'         => $pay_sn,
                        'promote_id'     => $promote_id,
                        're_price'       => $recharge_price,
                        'commission_rate'=> $commission_rate,
                        'commission'     => $commission,
                        'create_time'    => date("Y-m-d H:i:s"),
                ));
            }else {
                if(!empty($beyond_recharge)) {
                    //后续充返
                    $commission_rate = $beyond_recharge['commission_rate'];
                    $commission      = round($recharge_price * $commission_rate / 100,2);
                    $pplan_id        = $beyond_recharge['pplan_id'];

                    //返佣下单记录
                    $DB->Add('promote_order',array(
                            'pplan_id'       => $pplan_id,
                            'pay_sn'         => $pay_sn,
                            'promote_id'     => $promote_id,
                            're_price'       => $recharge_price,
                            'commission_rate'=> $commission_rate,
                            'commission'     => $commission,
                            'create_time'    => date("Y-m-d H:i:s"),
                    ));

                }

            }

        }
    }
}

////////////////////// 测试数据 //////////////////////
/*if(isset($_GET['test'])) {
        setcookie('bag_id','',time()- 3600,'/','25boy.cn');
        setcookie('total_fee','',time()- 3600,'/','25boy.cn');

        setcookie('bag_id',$bag_id,time()+ 3600,'/','25boy.cn');
        setcookie('total_fee',$data['money'],time()+ 3600,'/','25boy.cn');
    header("Location:http://mm.25boy.cn/wxpay_recharge_notify.php");
    exit();
}*/
/////////////////////////////////////////////////////////


// 查询是否已有记录
/*$bag_id = 0;
$bag = $DB->GetRs('bag','bag_id,pay_sn,note',"WHERE note='合并付款-".$from_order_sn."'");
if(empty($bag['bag_id']) && $order_type != "B"){
    // 添加账户流水记录
    $order_sn = $Base->build_order_no('B');
    if($recharge>0){
        $data = array();
        $data['pay_status'] = 'payment';                                                // 状态 'payment','paid','close'
        $data['type']       = 'prepaid';                                                // 交易类型，预付prepaid/商品消费goods/退款refund/运费ship_fee
        $data['pay_sn']     = $order_sn;                                                // 交易号
        $data['user_id']    = $_SESSION["user_id"];                                     // 用户ID
        $data['money']      = $recharge;                                                // 金额
        $data['method']     = 'weixin';                                                 // 支付方式 alipay/bag/weixin
        $data['balance']    = isset($user['bag_total'])?$user['bag_total']:0;           // 当前余额
        $data['create_date'] = date('Y-m-d H:i:s',time());
        if($merge_payment===TRUE){
            $data['note'] = '合并付款-'.$from_order_sn;
        }
        $result = $DB->Add('bag',$data);
        $bag_id = $DB->insert_id();
    }
}else{
    $bag_id = $bag['bag_id'];
    $order_sn = $bag['pay_sn'];
}*/

$prices = strval($recharge*100);
$prices_show = number_format($recharge,2);
$body="25BOY充值-".$order_sn;
$detail='';
$attach = $bag_id;
$productdescription = "25BOY男装";

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody($body);
$input->SetDetail($detail);
$input->SetAttach($attach);
$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
$input->SetTotal_fee((int)$prices);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag($productdescription);
$input->SetNotify_url("http://".$base_url."/wxpay_recharge_notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$wxorder = WxPayApi::unifiedOrder($input,30);
if(!array_key_exists("appid", $wxorder)
    || !array_key_exists("prepay_id", $wxorder)
    || $wxorder['prepay_id'] == "")
{
    $Error->show($wxorder['return_msg']);
    exit;
}
$jsApiParameters = $tools->GetJsApiParameters($wxorder);

// 删除salt
unset($_SESSION['salt']);
setcookie("salt",NULL,time()-1);
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
                        showrequest("<?php echo $recharge; ?>",1)
                    }else{
                        showrequest("<?php echo $recharge; ?>",0)
                        //alert("支付失败")
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
            window.location.href = "http://<?php echo $base_url;?>/?m=account";
        }
        function showrequest(orderid,status){
            window.location.href = "http://<?php echo $base_url;?>/?m=account";
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
        <!--        <font color="#9ACD32"><b>该笔订单支付金额为<span style="color:#f00;font-size:50px">1分</span>钱</b></font><br/><br/>-->
        <div style="line-height: 24px; border-bottom: 1px dotted #ccc; padding: 10px 0; color:#666">
            订单详情如下：<?php echo $body;?>
        </div>

        <center style="padding:20px;">
            <button class="btnlate" type="button" onclick="latetopay()" >取消支付</button>
            <button class="btnpay" type="button" onclick="callpay()" >立即支付</button>
        </center>


    </div>
</div>
</body>
</html>