<?php
/**
 * 通用通知接口demo
 * ====================================================
 * 支付完成后，微信会把相关支付和用户信息发送到商户设定的通知URL，
 * 商户接收回调信息后，根据需要设定相应的处理流程。
 *
 * 这里举例使用log文件形式记录回调信息。
 */
// require_once "class/mysql_for_pay.php";
require_once "config.php";
include_once("log_pay.php");
include_once("pay/wx/WxPayPubHelper.php");
include_once($_SERVER['DOCUMENT_ROOT']."/class/Flow.php");

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

// 测试，开启一个事务
// mysql_query("BEGIN");


//使用通用通知接口
$notify = new Notify_pub();

//存储微信的回调
$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
if(empty($xml)) return false;
$notify->saveData($xml);


//验证签名，并回应微信。
//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
//尽可能提高通知的成功率，但微信不保证通知最终能成功。
if($notify->checkSign() == FALSE){
    $notify->setReturnParameter("return_code","FAIL");//返回状态码
    $notify->setReturnParameter("return_msg","签名失败");//返回信息
}else{
    $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
}

$returnXml = $notify->returnXml();
echo $returnXml;



//==商户根据实际情况设置相应的处理流程，此处仅作举例=======

//以log文件形式记录回调信息
$log_ = new Log_();

$log_name="logs/".date('Y-m-d')."-notify.log";//log文件路径
$log_->log_result($log_name,"【接收到的notify通知】:\n".$xml."\n");



if($notify->checkSign() == TRUE)
{
    $order_id = $notify->data["attach"];
    $order_id = (int)$order_id;
    $total_fee = $notify->data['total_fee']/100;

    //测试
    /*$order_id = 38490;
    $total_fee = 316;
    $notify->data["transaction_id"] = '5961360294006506060';*/

    //allen 2015-11-10新增----------------------------------------
    $transaction_id = $notify->data["transaction_id"]; //微信支付订单号
    //end----------------------------------------

    if ($notify->data["return_code"] == "FAIL") {
        //此处应该更新一下订单状态，商户自行增删操作
        $log_->log_result($log_name,"【通信出错】:\n".$xml."\n");
    }
    elseif($notify->data["result_code"] == "FAIL"){
        //此处应该更新一下订单状态，商户自行增删操作
        $log_->log_result($log_name,"【业务出错】:\n".$xml."\n");
    }
    else{
        //此处应该更新一下订单状态，商户自行增删操作
        if($order_id!=0){
            $user_id = 0;
            $Condition = "where order_id=".$order_id;
            $order = $DB->GetRs("orders", "`receiver_name`,`location`,`receiver_phone`,`pay_total`, `ship_price`, `discount`, `seller_note`, `buyer_note`, `status`,`order_sn`,`user_id`,`delivery_id`,`business_code`,`business_qrcode`", $Condition);
            // print_r($order);exit();
            if(!empty($order)){
                $status = $order["status"];
                $order_sn = $order["order_sn"];
                $user_id = $order["user_id"];
                $pay_total = $order["pay_total"];

            }else{
                $status = -1;
            }

            // 会员信息
            $user = $DB->GetRs("users", "bag_total", "where user_id=".$user_id);

            if($status==0 && $total_fee==$pay_total){

                /*//更新会员积分
                $DB->Add("integral",array(
                    "context"=>"微信消费记录",
                    "integral_value"=>ceil($pay_total),
                    "user_id"=>$user_id,
                    "create_date"=>date('Y-m-d H:i:s')
                ));
                $DB->Set("users","integral_total=integral_total+".ceil($pay_total),"where user_id=".$user_id);*/


                //更新订单历史
                $DB->Add("order_history",array(
                    "order_id"=>$order_id,
                    "content"=>"微信支付成功",
                    "status"=>1,
                    "create_date"=>date('Y-m-d H:i:s')
                ));
                //更新订单状态
                $DB->Set("orders","status=1,pay_status=1,pay_date=NOW(),pay_method=5,transaction_id='".$transaction_id."'","where order_id=".$order_id);
                //更新钱包付款状态
                $bag_type = (substr($order_sn,0,1)=='S'?'ship_fee':'goods');
                //备注
                $note = "手机支付";
                $business_code = '';
                $business_qrcode = '';
                //线下
                if(!empty($order['business_code'])) {
                    $business_code = $order['business_code'];
                    $business = $DB->GetRs("business","business_name","WHERE business_code = '{$business_code}'");
                    $note = $business['business_name']."用户消费(微信支付)";
                }
                // 扫码记录商户，隐性消费
                if ( ! empty($order['business_qrcode'])) $business_qrcode = $order['business_qrcode'];

                $_bag = array(
                    "pay_status"=>'paid',
                    "pay_sn"=>$order_sn,
                    "method"=>'weixin',
                    "user_id"=>$user_id,
                    "create_date"=>date('Y-m-d H:i:s'),
                    "money"=>-$pay_total,
                    "type" => $bag_type,
                    "transaction_id"=>$transaction_id,
                    "note"=>$note,
                    "pay_date"=>date('Y-m-d H:i:s'),
                    "balance"=>isset($user['bag_total']) ? $user['bag_total'] : 0,  //当前余额
                    "business_code"=>$business_code,
                    "business_qrcode"=>$business_qrcode  // 扫码记录商户，隐性消费
                );

                $DB->Add("bag", $_bag);
                $_bag['bag_id'] = $DB->insert_id();

                // 流水
                $flow = new Flow();
                // 消费流水
                $bagToFlow = $_bag;
                // print_r($bagToFlow);exit();
                $flowData = [
                    'user_id' => $bagToFlow['user_id'],
                    // 基本金额
                    'money' => set_init($bagToFlow['money'], 0.0),
                    // 线下代码，从而得到business_id,进而得到商户ID, 最后获取收款账户相关信息,25boy不需要的
                    'business_code' => set_init($bagToFlow['business_code']),
                    // 支付方式
                    'method' => set_init($bagToFlow['method']),
                    'note' => set_init($bagToFlow['note']),
                    // 旧版流水bag表的id
                    'bag_id' => $bagToFlow['bag_id'],
                    // 第三方单号
                    'third_sn' => set_init($bagToFlow['third_sn'], set_init($bagToFlow['transaction_id'])),
                    // 流水类型 1: 充值赠送 2: 仅充值 3: 仅赠送 4: 消费 5: 退款 6: 扣款
                    'flow_type_id' => 4,
                    // 流水赠送金额
                    'plus_price' => set_init($bagToFlow['plus_price'], 0.0),
                    // 业务流水表 flow_business,flow_o2o,flow_hotel,flow_prepaid
                    'flow_profession' => 'flow_business',
                    // 商户收款类型code, 消费或充值时并且非钱包时要传，退款时则通过消费流水获取出来 
                    'code' => set_init($bagToFlow['method']),
                    'client' => set_init($bagToFlow['method']),
                    // 业务
                    'profession' => 'shop',
                    // 充值业务要传递的bag表的pay_sn参数
                    'pay_sn' => '',
                    // 消费或者退款要传递的订单ID
                    'order_id' => $order_id
                ];
                // print_r($flowData);exit();
                $flow->addFlow($flowData);

                //更新用户消费总额
                $DB->Set("users","consume_total = consume_total + ".$pay_total,"where user_id=".$user_id);


                // 添加 调用gyerp类，添加订单
                include_once($_SERVER['DOCUMENT_ROOT']."/class/grerp.php");
                $gyerp = new gyerp();
                $erp = $gyerp->getOrderForErp($order_id);
                $gyerp->add_order($erp);

            }

        }


        $log_->log_result($log_name,"【支付成功】:\n".$xml."\n");

    }

    //商户自行增加处理流程,
    //例如：更新订单状态
    //例如：数据库操作
    //例如：推送支付完成信息
}


?>