<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$order_id = isset($_POST["order_id"]) ? (int)$_POST["order_id"] : 0;

if(!isset($_SESSION["user_id"])||empty($_SESSION["user_id"])) {
    echo json_encode(
        array("status"=>"no_login")
    );
    exit;
}
$order = array();

$returnjson = array(
    "status"=>"error",
);

if($order_id==0){
    echo json_encode($returnjson);
    exit;
}


$result = $DB->GetRs("orders","*","where order_id=".$order_id);
if(!empty($result)) {
    $prices = (float)$result["pay_total"];
    $order = $result;
}else{
    echo json_encode($returnjson);
    exit;
}
if(isset($result['status']) && $result['status']!=0){
    echo json_encode(
        array("status"=>"is_payed")
    );
    exit;
}


if($prices>=0){
    $result = $DB->GetRs("users","*","where user_id=".$_SESSION["user_id"]);
    if(!empty($result)) {

        $bag_total = (float)$result["bag_total"];

        if($bag_total>=$prices){

            $new_balance = $bag_total - $prices;
            $result = $DB->Set("users",array("bag_total"=>$new_balance),"where user_id=".$_SESSION["user_id"]);

            if($result){
                update_order($prices,$order_id,$DB,$order,$new_balance);
                echo json_encode(
                    array("status"=>"success",'order_sn'=>$order["order_sn"],'order_id'=>$order["order_id"])
                );
                exit;
            }else{
                echo json_encode(
                    array("status"=>"error",'order_sn'=>$order["order_sn"],'order_id'=>$order["order_id"])
                );
                exit;
            }

        }

    }else{
        echo json_encode(
            array(
                "status"=>"no_balance",
                "balance"=>$bag_total,
                "price"=>$prices,
                "order_sn"=>$order["order_sn"],
                "order_id"=>$order["order_id"]
            )
        );
        exit;
    }
}


function update_order($prices,$order_id,$DB,$order,$bag_total=0){
    //添加历史
    $DB->Add("order_history",array(
        "order_id"=>$order_id,
        "content"=>"余额支付成功（手机）",
        "status"=>1,
        "create_date"=>date('Y-m-d H:i:s')
    ));
    //更新订单状态
    $DB->Set("orders","status=1,pay_status=1,pay_date=NOW(),pay_method=2","WHERE order_id=".$order_id);
    //更新钱包付款状态
    $bag_type = (substr($order["order_sn"],0,1)=='S'?'ship_fee':'goods');
    //备注
    $note = "手机支付";
    $business_code = '';
    //线下
    if(!empty($order['business_code'])) {
        $business_code = $order['business_code'];
        $business = $DB->GetRs("business","business_name","WHERE business_code = '{$business_code}'");
        $note = $business['business_name']."用户消费(手机支付)";
    }
    $bag = array(
        "pay_status"=>'paid',
        "pay_sn"=>$order["order_sn"],
        "method"=>'bag',
        "user_id"=>$_SESSION["user_id"],
        "create_date"=>date('Y-m-d H:i:s'),
        "money"=>-$prices,
        "type" => $bag_type,
        "note"=>$note,
        "balance"=>$bag_total,
        "pay_date"=>date('Y-m-d H:i:s'),
        "business_code"=>$business_code
    );
    $DB->Add("bag", $bag);
    $bag_id = $DB->insert_id();

    if($bag_id)
    {
        // 添加期末余额记录
        $bag['bag_id'] = $bag_id;
        include_once($_SERVER['DOCUMENT_ROOT']."/class/Balance.php");
        $balanceclass = new Balance();
        $balanceclass->save_user_bag($bag);

        // 流水
        include_once($_SERVER['DOCUMENT_ROOT']."/class/Flow.php");
        $flow = new Flow();
        // 消费流水
        $bagToFlow = $bag;
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
        // p($flowData);
        $flow->addFlow($flowData);

        //更新用户消费总额
        $DB->Set("users","consume_total = consume_total + ".$prices,"where user_id=".$_SESSION["user_id"]);

        //如果不是线下订单，则同步ERP
        if(empty($order['business_code'])) {
            // 添加 调用gyerp类，添加订单
            include_once($_SERVER['DOCUMENT_ROOT']."/class/grerp.php");
            $gyerp = new gyerp();
            $erp = $gyerp->getOrderForErp($order_id);
            $gyerp->add_order($erp);
        }
    }
    
}


echo json_encode($returnjson);
exit;