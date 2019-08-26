<?php

// 打印函数
function p($data){
    echo '<pre>';
    print_r($data);
}

function pe($data){
    p($data);
    exit;
}


$page_title = "订单中心";
$page_sed_title = '订单详情';

$user_id = isset($_SESSION['user_id'])&&!empty($_SESSION['user_id'])?(int)$_SESSION['user_id']:0;
$order_id = isset($_GET['order_id'])?(int)$_GET['order_id']:0;

if(empty($user_id) || $user_id==''){
    $Error->show('您还没有登录，或者登录超时，请登录后操作。','请登录');
    exit;
}

// 自动关闭超时订单
// $Common->auto_close_notpay_order($_SESSION["user_id"]);
// // 自动关闭超时未寄回订单
// $Common->auto_close_exchange_order($_SESSION["user_id"]);

// 加载o2o配置文件
$o2oConfig = require('./o2o_config.php');

$_table_orders = 'o2o_order';
$_table_order_join = 'o2o_order_join';
$_table_order_items = 'o2o_order_item';
$_table_users = 'users';

// 测试是使用o2o_bag，上线时使用bag
$_table_bag = $o2oConfig['bag_table'];

$_table_order_discounts = 'order_discounts';
$_table_order_history = 'o2o_order_history';
$_table_shipping = 'shipping';
$_table_order_items_return = 'o2o_order_items_return';
$_table_products = 'products';
$_table_delivery = 'delivery';


//获取订单
$sql = "SELECT o.*,oj.status,oj.location,oj.order_type,oj.pay_method,oj.order_total,oj.pay_total,oj.is_returned,d.delivery_name,sp.ship_sn,sp.company,sp.create_date as ship_time 
        FROM $_table_orders as o 
        JOIN $_table_order_join as oj ON o.order_id = oj.order_id
        LEFT JOIN $_table_shipping as sp ON o.ship_id=sp.ship_id 
        LEFT JOIN $_table_delivery as d ON o.delivery_id = d.delivery_id
        WHERE o.order_id = '".$order_id."' AND o.user_id='".(int)$user_id."'";
$order_query = $DB->query($sql);
$order = $DB->fetch_assoc($order_query);

if(count($order) == 0 || empty($order)){
    $Error->show('没有查询到相关信息，请检查输入数据是否正确。','找不到订单');
    exit;
}

// 订单商品
$sql = "SELECT oi.*,p.product_name,p.sku_sn,p.presale_date,s.color_prop,s.size_prop FROM $_table_order_items as oi 
    LEFT JOIN $_table_products as p ON oi.product_id=p.product_id 
    LEFT JOIN stock s ON oi.sku_sn = s.sku_sn AND oi.sku_prop = s.sku_prop
    WHERE oi.order_id = '".$order_id."' GROUP BY oi.item_id";
$order_row = $DB->query($sql);
$order_items = array();
while($result = $DB->fetch_assoc($order_row)){
    $presale_date = '';
    if($result['presale'] == 1){
        $presale_date = strtotime($result['presale_date'])-time()>0?'<p class="model"><font color="red">[预售,'.date('Y-m-d',strtotime($result['presale_date'])).'发货]</font></p>':'';
    }
    $result['presale_date'] = $presale_date;
    $order_items[] = $result; 
}

// 是否有退换货标记
$hasReorders = FALSE;
// 待寄回退货单
$sentBackReorders = [];

// 查找订单项是否有有效的退换货
foreach ($order_items as $k => $v) {
    $ori_condition = "JOIN o2o_reorder b ON o2o_reorder_item.reorder_id = b.reorder_id WHERE o2o_reorder_item.item_id = {$v['item_id']} AND b.status in (2,3,4)";
    $reorder = $DB->GetRs('o2o_reorder_item','*',$ori_condition);

    $order_items[$k]['reorder'] = $reorder;
    // 改变标记
    if (!$hasReorders && $reorder) {
        $hasReorders = TRUE;
    }

    // 如果退货单子状态是审核通过，待寄回，添加到
    if($reorder['substatus'] == 2){
        $sentBackReorders[] = $reorder;
    }
}

// pe($order_items);

//用户信息
$Fileds = "user_id,email,phone";
$Condition = "where user_id=".(int)$user_id;
$user = $DB->GetRs($_table_users,$Fileds,$Condition);

// 支付方式
$order['pay_sn'] = '';
switch ($order['pay_method']) {
    case '1':
        $order['pay_type'] = '支付宝';
        $pay_method = 1;
        $order['pay_sn'] = '支付宝交易号:'.$order['alipay_sn'];
        break;
    case '2':
        $order['pay_type'] = '钱包支付';
        $pay_method = 3;
        break;
    case '5':
        $order['pay_type'] = '微信支付';
         $pay_method = 5;
         $order['pay_sn'] = '微信交易号:'.$order['transaction_id'];
        break;
    default:
        $order['pay_type'] = '其它渠道';
        $pay_method = 0;
        break;
}

// 付款时间
$order['is_pay'] = null;
if($order['pay_status'] == 1){
    $order['is_pay'] = 1;
    $order['pay_status'] = '已付款';

    $Fileds = "*";
    $Condition = "where pay_status='paid' AND method=".$pay_method." AND pay_sn='".$order['order_sn']."' AND user_id='".$user_id."'  ORDER BY create_date ASC";
    $bag = $DB->GetRs($_table_bag,$Fileds,$Condition);

    if(empty($order['pay_date'])){
        $order['pay_time'] = isset($bag['create_date'])?$bag['create_date']:'-';
    }else{
        $order['pay_time'] = $order['pay_date'];
    }
}else{
    $order['pay_status'] = '未支付';
    $order['pay_time'] = '-';
}


// 使用优惠情况
$Fileds = "*";
$Condition = "where order_id=".$order['order_id']."  ORDER BY type ASC";
$Row = $DB->Get($_table_order_discounts,$Fileds,$Condition);
$Row = $DB->result;
$order_discounts = array();
while($result = $DB->fetch_assoc($Row)){
    $order_discounts[] = $result; 
}


// 订单历史
$Fileds = "*";
$Condition = "where order_id=".$order['order_id']."  ORDER BY create_date ASC";
$Row = $DB->Get($_table_order_history,$Fileds,$Condition);
$Row = $DB->result;
$order_history = array();
while($result = $DB->fetch_assoc($Row)){
    $order_history[] = $result; 
}

// 关联订单
// $sql = "SELECT o.*,sp.ship_sn,sp.company,sp.create_date as ship_time FROM $_table_orders as o LEFT JOIN $_table_shipping as sp ON o.ship_id=sp.ship_id WHERE o.relation_order = '".$order['order_sn']."'";
// $relation_query = $DB->query($sql);
// $relation_order = $DB->fetch_assoc($relation_query);

// if(!empty($order['relation_order'])){
//     $Fileds = "order_id,reout,status,return_num";
//     $Condition = "where order_sn='".$order['relation_order']."'";
//     $reorder = $DB->GetRs($_table_orders,$Fileds,$Condition);
// }
// $order['relation_order_id'] = isset($reorder['order_id'])?$reorder['order_id']:'';
// $order['relation_reout'] = isset($reorder['reout'])?$reorder['reout']:'';
// $order['relation_status'] = isset($reorder['status'])?$reorder['status']:'';
// $order['relation_return_num'] = isset($reorder['return_num'])?$reorder['return_num']:'';


// 物流信息
include_once($_SERVER['DOCUMENT_ROOT']."/class/kuaidi.php");
$kuaidi = new kuaidi();

$Fileds = "ship_sn,company,create_date";
$Condition = "where ship_id='".$order['ship_id']."'";
$shipping = $DB->GetRs($_table_shipping,$Fileds,$Condition);
$order['company'] = isset($shipping['company'])?$shipping['company']:'';
$order['ship_sn'] = isset($shipping['ship_sn'])?$shipping['ship_sn']:'';
$order['ship_com'] = $kuaidi->company_name($order['company']);
if(isset($relation_order['company'])){
    $relation_order['ship_com'] = $kuaidi->company_name($relation_order['company']);
}
$kuaidiinfo = $kuaidi->get_kuaidi($order['ship_sn']);
if(!empty($kuaidiinfo) && count($kuaidiinfo) > 0){
    $kuaidiinfo['data'] = unserialize($kuaidiinfo['data']);
}


// product_ids, item_ids
$product_ids = $item_ids = array();
foreach ($order_items as $pro) {
    $product_ids[] = $pro['product_id'];
    $item_ids[] = $pro['items_id'];
}

// 获取退货商品
// $sql = "SELECT re.re_id,re.num as re_num,oi.items_id,oi.product_id,oi.size_prop,oi.color_prop,oi.re_price,oi.product_name,p.sku_sn,oi.color_photo  
//     FROM $_table_order_items_return as re 
//     LEFT JOIN $_table_order_items as oi ON re.items_id=oi.items_id 
//     LEFT JOIN $_table_products as p ON p.product_id=oi.product_id 
//     WHERE re.order_id = '".$order_id."'";
// $refund_query = $DB->query($sql);
// $refund_items = array();
// while($result = $DB->fetch_assoc($refund_query)){
//     $refund_items[] = $result; 
// }

// pe($refund_items);

$refund_items = [];


// 重新整理
$order['ship_price'] = $order['ship_price']==0?'免运费':'<sup>￥</sup>'.$order['ship_price'];
$timearr = $Base->timeLeft(strtotime($order['ship_time'])+86400*$SITECONFIGER['order']['auto_confirm_order_time'],time());
$auto_confirm_order_time = '还剩';
if($timearr['day']>0){
    $auto_confirm_order_time .= $timearr['day'].'天';
}
if($timearr['hour']>0){
    $auto_confirm_order_time .= $timearr['hour'].'小时';
}
$auto_confirm_order_time .= '自动确认';

$closearr = $Base->timeLeft(strtotime($order['exp_date']),time());
$auto_close_time = '还剩';
if($closearr['day']>0){
    $auto_close_time .= $closearr['day'].'天';
}
if($closearr['hour']>0){
    $auto_close_time .= $closearr['hour'].'小时';
}
if($closearr['minute']>0){
    $auto_close_time .= $closearr['minute'].'分';
}
$auto_close_time .= '关闭订单';


foreach ($order_discounts as $key => $val) { 
    $val['title'] = preg_replace('/\(.*\)/', '', $val['title']);
    switch ($val['type']) {
        case '3':
            $order_discounts[$key]['name'] = '【奖品】'.$val['title'];
            break;
        case '2':
            $order_discounts[$key]['name'] = '【代 金 券】'.$val['title'];
            break;
        case '4':
            $order_discounts[$key]['name'] = '【会员折扣】'.$val['title'];
            break;
        case '5':
            $order_discounts[$key]['name'] = '【分销折扣】'.$val['title'];
            break;
        case '6':
            $order_discounts[$key]['name'] = '【线下优惠】'.$val['title'];
            break;
        default:
            $order_discounts[$key]['name'] = '【店铺活动】'.$val['title'];
            break;
    }
}


// 退换货超时时间
$re_timeout = '';
if($order['status']==5 || $order['status']==7){
    $exchange_timeout = $Common->get_order_history($order_id,$order['status']);
    $closearr = $Base->timeLeft(strtotime($exchange_timeout['create_date'])+86400*$SITECONFIGER['order']['exchange_timeout'],time());
    if($closearr['day']>0){
        $re_timeout .= $closearr['day'].'天';
    }
    if($closearr['hour']>0){
        $re_timeout .= $closearr['hour'].'小时';
    }
    if($closearr['minute']>0){
        $re_timeout .= $closearr['minute'].'分';
    }
    $re_timeout .= '后自动完成交易';
}else if($order['relation_status']==5 || $order['relation_status']==7){
    $exchange_timeout = $Common->get_order_history($order['relation_order_id'],$order['relation_status']);
    $closearr = $Base->timeLeft(strtotime($exchange_timeout['create_date'])+86400*$SITECONFIGER['order']['exchange_timeout'],time());
    if($closearr['day']>0){
        $re_timeout .= $closearr['day'].'天';
    }
    if($closearr['hour']>0){
        $re_timeout .= $closearr['hour'].'小时';
    }
    if($closearr['minute']>0){
        $re_timeout .= $closearr['minute'].'分';
    }
    $re_timeout .= '后自动完成交易';
}


// pe($order);

// 获取收货地址
$default_address = array();
$address_list = array();
$query = $DB->query("SELECT a.*,
        (select area_name from area where a.state=area.area_id and area.area_type=1) as state_name,
        (select area_name from area where a.city=area.area_id and area.area_type=2) as city_name,
        (select area_name from area where a.district=area.area_id and area.area_type=3) as district_name 
        FROM address a WHERE user_id={$_SESSION['user_id']} ORDER BY modify_date DESC");
while($result = $DB->fetch_assoc($query)){
    if($default_ID==$result["address_id"]){
        $default_address = $result;
    }else{
        if($i==0){
            $default_address = $result;
            $default_ID = $result["address_id"];
        }
    }
    array_push($address_list, array(
        "address_id"=>$result["address_id"],
        "user_id"=>$result["user_id"],
        "address"=>$result["address"],
        "zip"=>ceil($result["zip"]),
        "receiver_name"=>$result["receiver_name"],
        "receiver_phone"=>$result["receiver_phone"],
        "state_name"=>$result["state_name"],
        "city_name"=>$result["city_name"],
        "district_name"=>$result["district_name"],
        "state_id"=>$result["state"],
        "city_id"=>$result["city"],
        "district_id"=>$result["district"],
    ));
    $i++;
}

// 如有收货地址信息
if ($order['location']) {
    // 如果有收货地址，解析json数据
    $location = json_decode($order['location'], TRUE);
    if (is_array($location)) {
        $location['receiver_name'] = $order['receiver_name'];
        $location['receiver_phone'] = $order['receiver_phone'];
        // 替换默认地址
        $default_address = [
            "receiver_name"=>$location["receiver_name"],
            "receiver_phone"=>$location["receiver_phone"],
            "state_name"=>$location["state"],
            "city_name"=>$location["city"],
            "district_name"=>$location["district"],
            "address"=>$location['address']
        ];
    }
}

// pe($order);
// 退货单子状态
$reorderSubstatus = [
    '1' => '申请退换中',
    '2' => '同意申请，待寄回',
    '3' => '已寄出，待确认', 
    '4' => '确认退换',
];

$sm->assign("reorder_substatus", $reorderSubstatus);

$sm->assign("order", $order, true);
$sm->assign("user", $user, true);
$sm->assign("order_history", $order_history, true);
$sm->assign("history", end($order_history), true);
$sm->assign("order_discounts", $order_discounts, true);
$sm->assign("order_items", $order_items, true);
$sm->assign("relation_order", $relation_order, true);
$sm->assign("refund_items", $refund_items, true);
$sm->assign("product_ids", $product_ids, true);
$sm->assign("item_ids", $product_ids, true);
$sm->assign("serviceTel", $SITECONFIGER['info']['serviceTel'], true);
$sm->assign("kuaidiinfo", $kuaidiinfo, true);
$sm->assign("re_timeout", $re_timeout, true);

// 分配收货地址
$sm->assign("default_address", $default_address, true);
$sm->assign("address_list", $address_list, true);

$kuaidi_json = $kuaidiinfo;
if($kuaidi_json){
    $result_param = json_decode($kuaidiinfo['result_param'],true);
    $kuaidi_json['state'] = $result_param['lastResult']['state'];
}
$sm->assign("kuaidi_json", json_encode($kuaidi_json), true);
$sm->assign("auto_confirm_order_time", $auto_confirm_order_time, true);
$sm->assign("auto_close_time", $auto_close_time, true);

// 是否有退货单
$sm->assign("hasReorders", $hasReorders, true);
// 待寄回退货单
$sm->assign("sentBackReorders", $sentBackReorders, true);

// 是否可以申请退换
$refundable = in_array($order['status'],[2,3]);
$sm->assign('refundable',$refundable, true);

// pe($refundable);

// 隐藏底部导航栏
$site_nav_display = 'hide';