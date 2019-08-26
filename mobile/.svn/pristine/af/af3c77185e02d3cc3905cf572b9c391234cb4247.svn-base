<?php
require_once "config.php";

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
// print_r($user);exit();
switch ($order_type) {
    case 'R': case 'F': case 'S':case 'D':
        // 下单时发起付款
        if(!empty($from_order_sn)){
            $order = $DB->GetRs("orders","order_id,pay_total,order_total,business_code,business_qrcode","where pay_status=0 AND user_id=" . (int)$_SESSION["user_id"] . " AND order_sn = '".$from_order_sn."'");
            if(empty($order['order_id'])){
                $Error->show('找不到订单：'.$from_order_sn);
                exit;
            }
            //订单所属
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
        $data['method']     = 'alipay';                                                 // 支付方式 alipay/bag/weixin
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
// exit();

////////////////////// 测试数据 //////////////////////
/*if(isset($_GET['test'])) {
        setcookie('pay_sn','',time()- 3600,'/','25boy.cn');
        setcookie('total_fee','',time()- 3600,'/','25boy.cn');

        setcookie('pay_sn',$data['pay_sn'],time()+ 3600,'/','25boy.cn');
        setcookie('total_fee',$data['money'],time()+ 3600,'/','25boy.cn');
    header("Location:http://test.25boy.cn/alipay/notify_url.mobile.php");
    exit();
}*/
/////////////////////////////////////////////////////////

$sHtml.= "<div style='display:none'>";
$sHtml.= "<form id='paysubmit' name='alipaysubmit' action='http://www.25boy.cn/payment/alipay_mobile?pc' method='POST'>";
$sHtml.= "<input type='hidden' name='pay_sn' value='".$order_sn."'/>";
$sHtml.= "<input type='hidden' name='recharge' value='".$recharge."'/>";
// $sHtml.= "<input type='hidden' name='WIDout_trade_no' value='".$order_sn."'/>";
// $sHtml.= "<input type='hidden' name='WIDsubject' value='25BOY充值-".$order_sn."'/>";
// $sHtml.= "<input type='hidden' name='WIDtotal_fee' value='".$recharge."'/>";
// $sHtml.= "<input type='hidden' name='WIDbody' value='".$data['note']."'/>";
$sHtml = $sHtml."<input type='submit' value='submit'></form>";
$sHtml = $sHtml."<script>document.forms['paysubmit'].submit();</script>";
$sHtml = $sHtml."</div>";
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>充值支付 - 25BOY</title>
</head>
<body>
<?php
echo $sHtml;
?>
</body>
</html>