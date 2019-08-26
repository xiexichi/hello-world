<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
include_once($_SERVER['DOCUMENT_ROOT']."/class/Activity.php");
include_once($_SERVER['DOCUMENT_ROOT']."/class/Voucher.php");
include_once($_SERVER['DOCUMENT_ROOT']."/class/Curl.php");
$activityModel = new Activity();
$voucherModel = new Voucher();
$curlClass = new Curl();

$user_id = isset($_SESSION["user_id"]) ? intval($_SESSION["user_id"]) : 0;

// 打印函数
function p($data){
    echo '<pre>';
    print_r($data);
    exit;
}

// 是否套餐商品
function isCombomeal(){
    if (isset($_POST['isCombomeal']) && $_POST['isCombomeal']) {
        return true;
    }
    return false;
}

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

if(!isset($user_id)||empty($user_id)) {
    echo json_encode(
        array("status"=>"no_login")
    );
    exit;
}

// 微信判断
if(is_weixin()){
    if(!isset($_SESSION["openid"])||empty($_SESSION["openid"])) {
        echo json_encode(
            array("status"=>"no_login")
        );
        exit;
    }
    // 微信支付
    $pay_method = 5;
    $history_content = '微信创建订单成功';

}else{
    // 支付宝付款
    $pay_method = 1;
    $history_content = '手机创建订单成功';
}



if (isCombomeal()) {
    // 套餐提交

} else {
    // 购车提交
    $cart_ids = isset($_POST["cart_id"])?trim($_POST["cart_id"],','):"";
    if($cart_ids==""){
        echo json_encode(
            array("status"=>"no_cart_id")
        );
        exit;
    }    
} 

// 测试，开启一个事务
// mysql_query("BEGIN");

//配送方式
$delivery_id = empty($_POST['delivery_id']) ? 0 : trim($_POST['delivery_id']);
$address_id = isset($_POST["address_id"]) ? (int)$_POST["address_id"] : 0;
$address_id = is_numeric($address_id ) ? $address_id : 0;
if($delivery_id == 'self')
{
    $business_code = $Common->getBusinessCodeFrom();
    $business = $DB->GetRs("business","state,district,city,business_address,business_tel","WHERE business_code = '".$business_code."'");
    $address_data = array(
        'receiver_name' => '自提',
        'receiver_phone' => $business['business_tel'],
        'state' => $business['state'],
        'district' => $business['district'],
        'city' => $business['city'],
        'address' => $business['business_address'],
    );
}else{
    if($address_id==0){
        echo json_encode(
            array("status"=>"no_address_id")
        );
        exit;
    }else{
        $condition .= "where user_id=".$user_id." and address_id=".$address_id;
        $address_data = $DB->GetRs("address","*",$condition);
        if(!$address_data){
            echo json_encode(
                array("status"=>"no_address_id")
            );
            exit;
        }
        // 有些城市没有第三级 district
        if(empty($address_data['city'])){
            echo json_encode(
                array("status"=>"no_address_area")
            );
            exit;
        }
    }
}

// 附近店铺信息
$business_id = '';
$nearstores = $curlClass->get('o2o/getNearStores');
if($nearstores && $nearstores['code'] === 0){
  $business_id = $nearstores['rs']['business_id'];
}

// 会员信息
$user = [];
if(!empty($user_id)){
    $query = $DB->query("select u.user_id,u.`level`,u.pid,s.seller_level_id from users u left join seller s on u.user_id=s.user_id where u.user_id={$user_id}");
    $user = $DB->fetch_array($query);
}

// 新活动 - 按活动分组的购物车
$activitys = $activityModel->getUserActivity($business_id, $user);


//获取分销
$is_seller = 0;
$result = $DB->query("SELECT * FROM seller s LEFT JOIN seller_level sl ON s.seller_level_id = sl.id WHERE user_id = ".$user_id);
$seller = $DB->fetch_array($result);
if($seller) $is_seller = 1;


// 查询余额
$balance = isset($_POST["balance"])?(int)$_POST["balance"]:0;

if($balance){
    $row = $DB->GetRs("users","bag_total","where user_id=" . $user_id);
    $user_balance = !empty($row) ? $row["bag_total"] : 0;
}else{
    $user_balance = 0;
}
$coupon_id = isset($_POST["coupon"]) ? intval($_POST["coupon"]) : 0;
$voucher_id = isset($_POST["voucher_id"]) ? intval($_POST["voucher_id"]) : 0;

// 获取用户地址信息
$sub_total  = 0; // 购物车总价
$order_items = array(); // 订单产品数组
$auto_exp_time  = $SITECONFIGER["order"]["order_auto_close_time"]; // 付款超时时间
$goods_total = 0;
$pay_total = 0;
$total_quantity = 0;

if (isCombomeal()) {
    // 套餐不使用购物车
} else {
    // 购物车
    $carts = $Common->carts($cart_ids, $seller);
    // 下架商品不允许下单
    $unShelve = '';
    foreach ($carts as $key => $value) {
        // 下架不允许下单
        if (empty($value['stock']))
            $unShelve .= "{$value['product_name']}，<br />";
    }
    if ( ! empty($unShelve)) {
        echo json_encode(
            array(
                "status"=> 'error',
                'msg' => "存在已下架商品，不允许下单！<br />已下架的商品有：<br />{$unShelve}"
            )
        );
        exit();
    }
}

// 查询当前用户是否符合可领取产品奖品
$prize_data = $Common->get_product_prizes($carts, $user_id);
$prizes = $prize_data['prizes'];
$prize_cut_total = $prize_data['prize_cut_total'];
$carts = $prize_data['carts'];
if(count($prizes)>0){
    $prizes_limit = false;
    foreach ($prizes as $prize){
        if($prize['count_num']>$prize['quantity']){
            $prizes_limit = true;
        }
    }
    if($prizes_limit){
        echo json_encode(
            array(
                "status"=>"prizes_limit",
            ));
        exit;  
    }
}


/******************************************************************
    分销结算入口
*******************************************************************/

// 如果是分销，并且是购买套餐，则不允许购买
if($is_seller && isCombomeal()) {
    echo json_encode(
        array(
            "status"=>"seller_not_buy",
        ));
    exit;
}


if($is_seller) {

    /******************************************************************
        优惠入口
    *******************************************************************/
    $total_discounts_price = 0;
    //总计
    $amount = 0;
    $product_ids = array();
    //计算优惠
    foreach ($carts as $key => $cart) {
        /*if(empty($cart['prize'])){  // 排除奖品商品
            $row = $DB->GetRs('seller_item', '*',"where product_id=" . $cart['product_id']);
            $seller_discounts = (empty($row) || empty($row['discounts'])) ? null : unserialize($row['discounts']);
            $carts[$key]['seller_discounts'] = isset($seller_discounts[$seller['seller_level_id']]) ? $seller_discounts[$seller['seller_level_id']] : 10;
            $carts[$key]['discounts_price'] = $cart['product_price'] * $cart['quantity'] * (1 - $carts[$key]['seller_discounts'] / 10 );
            $total_discounts_price += $carts[$key]['discounts_price'];
        }*/
        $amount += $cart['product_price'] * $cart['quantity'];
        $product_ids[] = $cart['product_id'].'x'.$cart['quantity'];
    }

    //总优惠价
    $total_event_price = $total_discounts_price;

    $product_ids = $prizes_list = array();
    foreach ($carts as $ck=>$cart){
        if ($cart['miao_price'] > 0){
           $price   = $cart['miao_price'];
           $amount  = $cart['miao_price']*$cart['quantity'];
           $auto_exp_time  = $cart['order_time'];
        }else{
            $price   = $cart['product_price'];
            $amount  = $cart['product_price']*$cart['quantity'];
        }

        $sub_total  += $amount;
        $order_items[] = array(
            'cart_id'    => $cart['cart_id'],
            'product_id' => $cart['product_id'],
            'size_prop'  => $cart['size_prop'],
            'color_prop' => $cart['color_prop'],
            'price'      => $price,
            'num'        => $cart['quantity'],
            'amount'     => $amount,
            're_status'  => isset($cart['prize'])?5:1,
            'product_name'  => isset($cart['product_name'])?$cart['product_name']:NULL,
            'sku_prop'      => isset($cart['sku_prop'])?$cart['sku_prop']:NULL,
            'color_photo'   => isset($cart['thumb'])?$cart['thumb']:NULL,
            'presale'       => isset($cart['presale'])?$cart['presale']:0,
        );
        $product_ids[] = $cart['product_id'].'x'.$cart['quantity'];

        // 减去奖品的价格
        if(isset($cart['prize'])){
            $total_event_price += $cart['prize']['prize_price'];
            $prizes_list[] = $cart['prize'];
        }

        // 计算退款金额 单件退款=单价*优惠折扣
        $re_price = $price;
        /*if($carts[$ck]['seller_discounts'] > 0) {
            $re_price = round($price * $carts[$ck]['seller_discounts'] / 10 ,2);
        }*/

        //判断商品是否为奖品，奖品无退款
        if($order_items[$ck]['re_status'] == 5){
            $order_items[$ck]['re_price'] = 0;
        }else{
            //非奖品 
            $order_items[$ck]['re_price'] = $re_price;
        }
    }

    if(empty($order_items)){
        echo json_encode(
            array("status"=>"error")
        );
        exit;  
    }


    // 记录订单优惠
    $order_discounts = array();
    $item_event = array();
    foreach ($carts as $cart) {
        if(isset($cart['orig_price']) && $cart['orig_price'] > 0){
            $order_discounts[] = array(
                'user_id' => $user_id,
                'title'  => $cart['product_name'],
                'type'   => 5, // 5为分销折扣
                'discount' =>  $cart['orig_price']-$cart['product_price'],
                'event_coupon_id' => 0
            );
        }
    }

    if(!empty($prizes_list)){
        foreach ($prizes_list as $prize) {
            $order_discounts[] = array(
                'user_id' => $user_id,
                'title'  => $prize['prize_title'],
                'type'   => 3, // 1活动,2代金券,3奖品放送
                'discount' =>  $prize['prize_price'],
                'event_coupon_id'  => $prize['prize_id']
            );
        }
    }

    // 总优惠价
    $cut_total = $total_event_price;
    // 订单运费
    $ship_fee = $Common->get_ship_fee(implode(',', $product_ids),$address_id,$sub_total,$delivery_id);
    //订单总价
    $order_total  =  $sub_total+$ship_fee; // 商品小计+运费
    // 支付价钱
    $pay_total  =  $order_total-$cut_total;
    //去掉运费来计算退款(当有代金券时)
    $pay_total_for_coupon = $pay_total - $ship_fee;

    $order_sn_type = 'F';

}



/******************************************************************
    会员结算入口
*******************************************************************/
if(!$is_seller) {

    /******************************************************************
        活动入口
    *******************************************************************/

    // 判断是否套餐
    if (isCombomeal()) {
        // 判断套餐是否存在
        $Condition = "where combomeal_id=".(int)$_POST['combomeal_id']." AND NOW() < end_date";
        $combomeal = $DB->GetRs('combomeal','*',$Condition);
        if (!$combomeal) {
            // 不存在
            $error = [
                "status" => "error",
                'msg'    => '套餐不存在'
            ];
            echo json_encode($error);exit;
        }

        // 套餐选择商品数据
        $combomealChooseProducts = json_decode($_POST['ccp'],true);

        // 如果数据转换错误
        if (!is_array($combomealChooseProducts)) {
            $error = [
                "status" => "error",
                'msg'    => '选择套餐商品错误'
            ];
            echo json_encode($error);exit;
        }

        // 套餐总价
        $combomealTotal = 0;
        // 原商品总价
        $originalTotalPrice = 0;

        // 商品item项数据
        $order_items = [];
        // 商品id和数量组合
        $product_ids = [];

        foreach ($combomealChooseProducts as $k => $v) {
            // 商品信息
            $product = $DB->GetRs('products','products.product_id,products.product_name,products.price product_price,b.combomeal_price,b.combomeal_num quantity',"JOIN combomeal_item b ON products.product_id = b.product_id WHERE products.product_id = ".(int)$k);
            
            // 商品库存信息
            $propStock = $DB->GetRs('prop','prop.prop_id,prop.sku_sn,b.color_prop,b.size_prop,b.photo_prop thumb',"JOIN stock b ON b.sku_sn = prop.sku_sn AND b.color_prop = prop.color_prop AND b.size_prop = '".$v['size']."' WHERE prop.product_id = ".(int)$k.' AND prop.color_prop = "'.$v['color'].'"');

            // 添加商品项数据
            $order_items[] = [
                'product_id' => $product['product_id'],
                'size_prop'  => $propStock['size_prop'],
                'color_prop' => $propStock['color_prop'],
                'price'      => $product['combomeal_price'],
                're_price'   => $product['combomeal_price'],
                'num'        => $product['quantity'],
                'amount'     => $product['combomeal_price'] * $product['quantity'],
                're_status'  => 1,
                'product_name'  => $product['product_name'],
                'sku_prop'      => isset($cart['sku_prop'])?$cart['sku_prop']:NULL,
                'color_photo'   => $propStock['thumb'],
                'sku_sn'       => $propStock['sku_sn'],
                'combomeal_id' => $combomeal['combomeal_id']
            ];

            // 添加商品id和数量的组合
            $product_ids[] = $product['product_id'].'x'.$product['quantity'];

            // 计算套餐总价
            $combomealTotal += $product['combomeal_price'] * $product['quantity'];

            // 计算商品原总价
            $originalTotalPrice += $product['product_price'] * $product['quantity'];
        }

        // 设置订单商品小计
        $sub_total = $combomealTotal;

        // 添加套餐优惠数据
        $order_discounts = [];
        $order_discounts[] = array(
            'user_id' => $user_id,
            'title'  => $combomeal['combomeal_title'],
            'type'   => 7,  // 1活动,2代金券,3奖品放送,7套餐优惠
            'discount' =>  max($originalTotalPrice - $combomealTotal,0)
        );

    } else {

        // 按活动分组的购物车
        /*$carts = $Common->get_product_events($carts);
        $event_group = $Common->event_group($carts);
        // 根据活动分组的购物车优惠价
        foreach ($event_group as $key=>$val) {
            $amount = 0;
            $quantity = 0;
            $idsarr = array();
            $three = array(); //三免一优惠的价格
            foreach ($val['items'] as $item) {
                $amount += ($item['miao_price']?$item['miao_price']:$item['product_price'])*$item['quantity'];
                $quantity += $item['quantity'];
                $idsarr[] = $item['product_id'];
                //满件免所需价格列表
                for ($i=0; $i < $item['quantity'] ; $i++) { 
                    $three[] = $item['product_price'];
                }
                sort($three,SORT_NUMERIC); //由低到高排列
            }
            $event_group[$key]['total']['price'] = $amount;
            $event_group[$key]['total']['quantity'] = $quantity;
            $group = $Common->get_event_price_one($val['event']['event_id'],$amount,$quantity,$three);
            $event_group[$key]['group_price'] = $group['price'];
            $event_group[$key]['group_title'] = $group['title'];
            $event_group[$key]['item_ids'] = $idsarr;
        }
        $new_event_group = $Common->array_sort($event_group,'group_price','desc');*/


        /******************************************************************
            VIP入口 无活动商品专享会员折扣
        *******************************************************************/

        /*$discount_price = 0;

        if($user['level'] > 0) $vip = $DB->GetRs("level",'*',"where id=" . $user['level']);
        if(isset($vip)) {
            foreach ($carts as $value) {
                // 排除活动与奖品商品
                if(empty($value['event']) && empty($value['prize'])) {
                    $discount_price += $value['product_price'] * $value['quantity'] * (1 - $vip['discount'] / 10);
                }
            }  
        }*/


        /******************************************************************
            优惠汇总
        *******************************************************************/

        // 活动总优惠价
        $total_event_price = 0;
        /*foreach ($new_event_group as $group) {
            $total_event_price += $group['group_price'];
        }*/

        $product_ids = $prizes_list = array();
        foreach ($carts as $ck=>$cart){
            if ($cart['miao_price'] > 0){
               $price   = $cart['miao_price'];
               $amount  = $cart['miao_price']*$cart['quantity'];
               $auto_exp_time  = $cart['order_time'];
            }else{
                $price   = $cart['product_price'];
                $amount  = $cart['product_price']*$cart['quantity'];
            }

            $sub_total  += $amount;
            $order_items[] = array(
                'cart_id'    => $cart['cart_id'],
                'product_id' => $cart['product_id'],
                'size_prop'  => $cart['size_prop'],
                'color_prop' => $cart['color_prop'],
                'product_price'      => $price,
                'price'      => $price,
                'quantity'        => $cart['quantity'],
                'num'        => $cart['quantity'],
                'amount'     => $amount,
                're_status'  => isset($cart['prize'])?5:1,
                'product_name'  => isset($cart['product_name'])?$cart['product_name']:NULL,
                'sku_prop'      => isset($cart['sku_prop'])?$cart['sku_prop']:NULL,
                'color_photo'   => isset($cart['thumb'])?$cart['thumb']:NULL,
                'presale'       => isset($cart['presale'])?$cart['presale']:0,
                'sku_sn'       => isset($cart['sku_sn'])?$cart['sku_sn']:NULL,
            );
            $product_ids[] = $cart['product_id'].'x'.$cart['quantity'];


            // 减去奖励的价格
            if(isset($cart['prize'])){
                $total_event_price += $cart['prize']['prize_price'];
                $prizes_list[] = $cart['prize'];
            }


            // 计算退款金额 单件退款=单价*((总价-优惠)/总价)
            /*$product_discount_price = 0; //商品对应的优惠
            foreach ($new_event_group as $gk => $gv) {             
                if(!empty($gv['group_price']) && $gv['group_price'] > 0) {       
                    if(in_array($cart['product_id'], $gv['item_ids'])){
                        $discount = round(($gv['total']['price'] - $gv['group_price']) / $gv['total']['price'],4);
                        $product_discount_price += $price * (1 - $discount);
                    }
                }
            }
            $re_price = ($product_discount_price > 0) ? ($price - $product_discount_price)  : $price;

            //无活动商品，单件退款，即单件实付款=原价 * 折扣
            if(empty($cart['event'])) {
                if(isset($vip)) {
                   $re_price = $cart['product_price'] * $vip['discount'] / 10; 
                }
            }
            //判断商品是否为奖品，奖品无退款
            if($order_items[$ck]['re_status'] == 5){
                $order_items[$ck]['re_price'] = 0;
            }else{
                //非奖品 
                $order_items[$ck]['re_price'] = $re_price;
            }*/
        }
    }

    // 新购物车
    $order_items = $activityModel->carts_join_activitys($order_items, $activitys);
    // 原总价
    $goods_total = 0;
    $total_quantity = 0;
    foreach ($order_items as $key => $val)
    {
        $val['re_price'] = isset($val['re_price']) ? $val['re_price'] : $val['product_price'];
        $order_items[$key]['re_price'] = sprintf('%.2f', $val['re_price']);
        $order_items[$key]['subtotal'] = sprintf('%.2f', $val['re_price']*$val['quantity']);
        $order_items[$key]['org_subtotal'] = sprintf('%.2f', $val['product_price']*$val['quantity']);
        $order_items[$key]['activity_title'] = isset($val['activity_title']) ? $val['activity_title'] : '';
        $total_event_price += (($val['product_price']-$val['re_price'])*$val['quantity']);
        $goods_total += ($val['product_price']*$val['quantity']);
        $total_quantity += $val['quantity'];
    }

    // 会员组折扣
    $activityModel->userLevelDiscount($order_items, $user, $goods_total, $total_event_price);


    // 记录订单优惠信息
    /*$order_discounts = array();
    $total_event_price = 0;
    foreach ($carts as $key => $val) {
        // 重新计算优惠减免金额
        $tempPrice = (($val['product_price']-$val['re_price'])*$val['quantity']);
        $total_event_price += $tempPrice;
        if(empty($order_discounts[$val['activity_title']])) {
            $order_discounts[$val['activity_title']] = array(
                'user_id' => $user_id,
                'title'  => $val['activity_title'],
                'type'   => 1, // 1活动,2代金券,3奖品放送
                'discount' =>  $tempPrice,
                'event_coupon_id'  => 1
            );
        }
    }*/


    // 判断订单项数据
    if(empty($order_items)){
        echo json_encode(
            array("status"=>"error")
        );
        exit;  
    }

    // (2017-10-18)如果不是套餐数据
    /* if (!isCombomeal())
    {
        // 记录订单优惠
        $order_discounts = array();
        $item_event = array();
        if(is_array($new_event_group)){
            foreach ($new_event_group as $group) {
                if(strpos($group['group_title'], 'VIP') !== false) {
                    //找到VIP折扣
                    $type = 4;
                    $title = $group['group_title'];
                    $event_coupon_id = $vip['id'];
                }else {
                   $type = 1;
                   $title = $group['group_title'].' ('.$group['event']['event_title'].')';
                    $event_coupon_id = $group['event']['event_id'];
                }

                $order_discounts[] = array(
                    'user_id' => $user_id,
                    'title'  => $title,
                    'type'   => $type,  // 1活动,2代金券,3奖品放送
                    'discount' =>  $group['group_price'],
                    'event_coupon_id'  => $event_coupon_id
                  );
            }
        }
              
    }*/ 


    //无活动商品，VIP会员折扣
    /*if($discount_price > 0) {
        $order_discounts[] = array(
            'user_id' => $user_id,
            'title'  => $vip['level'].'专享'.$vip['discount'].'折',
            'type'   => 4, 
            'discount' =>  $discount_price,
            'event_coupon_id'  => $vip['id']
          );
    }*/

    //整理订单优惠,假如出现重复
    /*$arr = array();
    $vip_price = 0;
    if(!empty($order_discounts)) {
        foreach ($order_discounts as $key => $value) {
            if($value['type'] == 4) {
                 $arr = $value;
                 $vip_price += $value['discount'];
                 unset($order_discounts[$key]);
            }

        }
        if(!empty($arr)) {
            $arr['discount'] = $vip_price;
            $order_discounts[] = $arr;
        }
    }*/


    if(!empty($prizes_list)){
        foreach ($prizes_list as $prize) {
            $order_discounts[] = array(
                'user_id' => $user_id,
                'title'  => $prize['prize_title'],
                'type'   => 3, // 1活动,2代金券,3奖品放送
                'discount' =>  $prize['prize_price'],
                'event_coupon_id'  => $prize['prize_id']
            );
        }
    }

    // 总优惠价
    $cut_total = $total_event_price;

    // 订单运费
    $ship_fee = $Common->get_ship_fee(implode(',', $product_ids),$address_id,$sub_total,$delivery_id);

    $order_total  =  $sub_total+$ship_fee; // 商品小计+运费
    // 支付价钱
    $pay_total  =  $order_total-$cut_total;

    //去掉运费来计算退款(当有代金券时)
    $pay_total_for_coupon = $pay_total - $ship_fee;


    $order_sn_type = 'R';

    // 新版优惠券使用
    // 使用代金券,当结算时有代金券的话会自动传过来
    $voucher = [];
    if ( ! empty($voucher_id)) {
        $vouchers = $voucherModel->getAvailableVoucherGroupModel($user_id, $pay_total_for_coupon, $total_quantity, $goods_total);
        if ( ! empty($vouchers)) {
            foreach ($vouchers as $key => $value) {
                if ($value['voucher_id'] == $voucher_id) {
                    $voucher = $value;
                    break;
                }
            }
        }

        if (empty($voucher)) {
            echo json_encode(
                array(
                    "status"=>"voucher_error",
                    "msg"=>"找不到优惠券信息！",
                ));
            exit; 
        }

        // 不与其他活动叠加券
        $coupon_price = 0;
        if ($voucher['use_mode'] == 2) {
            $pay_total = 0; // 重新计算实付金额
            $ratio = $voucher['pay_total']/$voucher['original_pay_total'];
            foreach ($order_items as $key => $val) {
                $continue = false;
                // 部分不参与
                if($voucher['product_mode'] == "3" && in_array($val['product_id'], $voucher['goods'])) {
                    $continue = true;
                }
                // 部分参与
                if($voucher['product_mode'] == "2" && !in_array($val['product_id'], $voucher['goods'])) {
                    $continue = true;
                }

                if($continue) {
                    // 使用活动优惠
                    $re_price = $val['re_price'];
                }else{
                    // 使用券优惠
                    $re_price = $val['price']*$ratio;
                    $coupon_price += ($val['price'] - $re_price) * $val['num'];
                    $order_items[$key]['activity_title'] = $voucher['title'];
                }
                $order_items[$key]['re_price'] = $re_price;
                $pay_total += $re_price * $val['num'];
            }
        }else{
            // 可以与其他活动叠加的券
            foreach ($order_items as $ik => $iv) {
                // $re_price = $order_items[$ik]['re_price']-$coupon['cut_price']/count($order_items);
                $re_price = round($order_items[$ik]['re_price']*(($pay_total_for_coupon - $voucher['discount_total'])/$pay_total_for_coupon),3);
                $coupon_price += ($iv['price'] - $re_price) * $iv['num'];
                $order_items[$ik]['re_price'] = $re_price > 0 ? $re_price : 0;
            }
            // 减去代金券价格
            $pay_total -= $coupon_price;
        }
    }
}




// 优惠总价
$cut_total = 0;
// 订单优惠项
foreach ($order_items as $key => $val) {
    if(empty($val['activity_title'])) continue;
    $tempPrice = (($val['price']-$val['re_price'])*$val['num']);
    $cut_total += $tempPrice;
    if(empty($order_discounts[$val['activity_title']])) {
        $order_discounts[$val['activity_title']] = array(
            'user_id' => $user_id,
            'title'  => $val['activity_title'],
            'type'   => 1, // 1活动,2代金券,3奖品放送
            'discount' =>  $tempPrice,
            'event_coupon_id'  => 1
        );
    }else{
        $order_discounts[$val['activity_title']]['discount'] += $tempPrice;
    }
}

/*//余额不足以支付
if($user_balance<$pay_total && $balance){
    echo json_encode(
        array("status"=>"no_balance")
    );
    exit;
}*/

// 如果支付金额为0，改为钱包支付
if($pay_total == 0){
    $pay_method = 2;
    $balance = 1;
}

$data = array();
$receiver_name = $address_data["receiver_name"];
$receiver_phone = $address_data["receiver_phone"];
$row = $DB->GetRs("area","area_name","where area_id=".(int)$address_data["state"]);
$state_name = empty($row) ? "" : $row["area_name"];
$row = $DB->GetRs("area","area_name","where area_id=".(int)$address_data["district"]);
$district_name = empty($row) ? "" : $row["area_name"];
$row = $DB->GetRs("area","area_name","where area_id=".(int)$address_data["city"]);
$city_name = empty($row) ? "" : $row["area_name"];
$isTel="/^([0-9]{3,4}-)?[0-9]{7,8}$/";
if(!$Base->is_phone_number($receiver_phone) && !preg_match($isTel,$receiver_phone)){
    echo json_encode(
        array("status"=>"no_phone")
    );
    exit; 
}
if(!$state_name || !$city_name){
    echo json_encode(
        array("status"=>"no_address_id")
    );
    exit;  
}

$order_sn = $Base->build_order_no($order_sn_type);
$data['location']       = ($delivery_id=='self' ? '' : ($state_name.','.$city_name.','.$district_name.','.$address_data["address"]));
$data['receiver_name']  = $address_data['receiver_name'];
$data['receiver_phone'] = $address_data['receiver_phone'];
$data['user_id']        = $user_id;
$data['pay_total']      = $pay_total;
$data['order_total']    = $order_total;
$data['discount']       = $cut_total;
$data['status']         = 0;
$data['order_sn']       = $order_sn;
$data['pay_method']     = $pay_method;
$data['exp_date']       = date('Y-m-d H:i:s',time()+$auto_exp_time*60);
$data['ship_price']     = $ship_fee;
$data['buyer_note']     = ($delivery_id=='self'&&!$_POST['buyer_note'] ? '自提' : htmlspecialchars_decode(@$_POST['buyer_note']));   //买家留言
$data["order_date"]     = date('Y-m-d H:i:s');
$data['ship_id']        = 0;
$data['ip']             = @$Base->clientIp();
$data['is_seller']      = $is_seller ? ($seller['is_return'] ? 1 : 2) : 0;
$data['delivery_id']    = intval($delivery_id);   //配送方式
$data['business_qrcode']  = $Common->getBusinessCodeFrom();//扫码+020记录商户显性消费
// $data['store_code']     = isset($_COOKIE['store_code']) ? $_COOKIE['store_code'] : '';

// 如果是套餐
if (isCombomeal()) {
    $data['order_type'] = 'combomeal';
}

// print_r($data);exit;

// 添加订单
$order_id  =  add_order($data,$DB);

/* *********************************************************************
 * 购物返佣 数据准备
 * *********************************************************************/
$is_continue = false; //是否continue
if(!$is_seller && !empty($user['pid'])) {
/*    if(!empty($user['pid'])) {
        $promote_id = $user['pid'];
    }else {
        $promote_id = base64_decode($_COOKIE['rememberMe']);
        //将推广者id更新到用户
        $DB->Set("users","pid=".$promote_id,"where user_id=".$user_id);
    }*/

    $promote_id = $user['pid'];
    $is_continue = false; //是否continue
    $is_first    = false; //是否首次购物
    $promote_product_list = $promote_website = $promote_first = array();
    $promote = $DB->GetRs("promote", "promote_id,is_frozen", "WHERE promote_id = $promote_id");
    //空推广者或者被冻结的推广者不享受返佣
    if(empty($promote) || $promote['is_frozen']) {
        $is_continue = true;
    }else {
        //在这里判断，是否首次购物
        $sql = "";
        $row = $DB->GetRs("orders","*","WHERE user_id = ".$user_id." AND pay_status = 1");
        if(empty($row)) {
            $is_first = true;
            //获取首次购物优惠
            $promote_first = $Common->get_beyond_first($promote_id,4);
        }
        //后续购物返佣商品列表
        $promote_product_list = $Common->get_beyond_product_list($promote_id);
        // print_r($promote_product_list);exit();
    }
}

// 添加订单产品信息到数据库
foreach ($order_items as $item) {
           
    /*********************** 下单项目记录 ***********************/
    add_order_item($order_id,$item);

    /* *********************************************************************
     * 购物返佣 下单记录
     * *********************************************************************/
    if(!$is_seller && !empty($user['pid'])) {

        //空推广者或者被冻结的推广者不享受返佣
        if($is_continue) continue;

        $product_id  = $item['product_id']; //商品id
        $product_num = $item['num'];        //商品数量
        $re_price    = $item['re_price'];   //商品实际单价

        //获取返佣项目
        $promote_item = $DB->GetRs("promote_item", "pitem_id", "WHERE promote_id = $promote_id AND type = 0 AND item_id = $product_id");
        if(empty($promote_item['pitem_id'])) {
            // $link = $Common->get_beyond_link($promote_id,0,$product_id);
            $DB->Add('promote_item',array(
                    'promote_id'    => $promote_id,
                    'type'          => 0,
                    'item_id'       => $product_id,
                    'create_time'   => date("Y-m-d H:i:s"),
            ));
            $pitem_id = $DB->insert_id();
        }else {
            $pitem_id = $promote_item['pitem_id'];
        }

        if($is_first && !empty($promote_first)) {
            //首次购物
            $commission_rate = $promote_first['commission_rate'];
            $commission      = round($re_price * $product_num * $commission_rate / 100,2);
            $pplan_id        = $promote_first['pplan_id'];

            //返佣下单记录
            $DB->Add('promote_order',array(
                    're_type'        => 're_shopping',
                    'pplan_id'       => $pplan_id,
                    'pitem_id'       => $pitem_id,
                    'order_id'       => $order_id,
                    'promote_id'     => $promote_id,
                    'product_id'     => $product_id,
                    'product_num'    => $product_num,
                    're_price'       => $re_price,
                    'commission_rate'=> $commission_rate,
                    'commission'     => $commission,
                    'create_time'    => date("Y-m-d H:i:s"),
            )); 
        }else {
            //判断是否存在可推广商品(后续购物)
            if(!empty($promote_product_list[$product_id])) {

                $promote_product = $promote_product_list[$product_id];
                // print_r($promote_product);
                $commission_rate = $promote_product['commission_rate'];
                $commission      = round($re_price * $product_num * $commission_rate / 100,2);
                $pplan_id        = $promote_product['pplan_id'];
            
                //返佣下单记录
                $DB->Add('promote_order',array(
                        're_type'        => 're_shopping',
                        'pplan_id'       => $pplan_id,
                        'pitem_id'       => $pitem_id,
                        'order_id'       => $order_id,
                        'promote_id'     => $promote_id,
                        'product_id'     => $product_id,
                        'product_num'    => $product_num,
                        're_price'       => $re_price,
                        'commission_rate'=> $commission_rate,
                        'commission'     => $commission,
                        'create_time'    => date("Y-m-d H:i:s"),
                ));
                
            } 

        }

  
    }


}
// exit();

// 添加订单优惠信息到数据库
if($order_discounts){
    foreach ($order_discounts as $discount) {
        $discount['order_sn'] = $order_sn;
        $Common->add_order_discount($order_id,$discount);
    }
}


// 记录优惠券信息
if ( ! empty($voucher)) {
    $voucherModel->useVouchers($voucher, $order_sn);
}


/******************************************************************
    配送方式
*******************************************************************/

$deliverys = array();
$result = $DB->Get('delivery','*',"WHERE status = 1 ORDER BY is_default DESC,delivery_id ASC");
while (!!$rows = $DB->fetch_array($result)) {
    $deliverys[$rows['delivery_id']] = $rows;
}

/*********************************************************************
    使用余额+方式
**********************************************************************/
$salt = $_SESSION['salt'] = mt_rand();
if($balance == 2){
    if($user_balance > 0 && $user_balance < $pay_total){
        $prepaid_price = $pay_total - $user_balance;
        echo json_encode(
            array("status"=>"success","balance"=>2,"order_sn"=>$order_sn,'method'=>$pay_method,'recharge'=>$prepaid_price,'salt'=>$salt)
        );
        exit;
    }
}

// 使用余额支付
if($balance == 1){
    $pay_method = 2;
    $Condition = "where user_id=".(int)$user_id;
    $row = $DB->GetRs("users", "bag_total", $Condition);
    if(!empty($row)){

        if((float)$row["bag_total"]<$pay_total){
            echo json_encode(
                array("status"=>"no_balance")
            );
            exit;
        }else{
            //余额足已支付
            $new_balance= (float)$row["bag_total"] - $pay_total;
            //更新余额
            $DB->Set("users","bag_total=".(float)$new_balance,"where user_id=".$user_id);
            //更新订单历史
            $DB->Add("order_history",array(
                "order_id"=>$order_id,
                "content"=>"余额支付成功（手机）",
                "status"=>1,
                "create_date"=>date('Y-m-d H:i:s')
            ));
            //更新订单状态
            $DB->Set("orders","status=1,pay_status=1,pay_date=NOW(),pay_method=".$pay_method,"where order_id=".$order_id);
            //更新钱包付款状态
            $bag = array(
                "pay_status"=>'paid',
                "pay_sn"=>$order_sn,
                "method"=>'bag',
                "user_id"=>$user_id,
                "create_date"=>date('Y-m-d H:i:s'),
                "money"=>-$pay_total,
                "type" => 'goods',
                "note"=>'手机支付',
                "balance"=>$new_balance,  //当前余额
                "pay_date"=>date('Y-m-d H:i:s'),
                "business_qrcode"=>$data['business_qrcode'],//扫码+020记录商户显性消费
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
                $DB->Set("users","consume_total = consume_total + ".$pay_total,"where user_id=".$user_id);

                // 添加 调用gyerp类，添加订单（测试时注释，上传时必须打开注释）
                include_once($_SERVER['DOCUMENT_ROOT']."/class/grerp.php");
                $gyerp = new gyerp();
                $erp = $gyerp->getOrderForErp($order_id);
                $gyerp->add_order($erp);
            }

            // ----------------添加订单 end----------------------------------------------------------------------------------------
        }
    }else{
        //余额不足以支付
        echo json_encode(
            array("status"=>"no_balance")
        );
        exit;
    }
}

echo json_encode(
    array("status"=>"success","balance"=>$balance,"order_id"=>$order_id,"order_sn"=>$order_sn,'salt'=>$salt)
);
exit;




/**
 * 訂單提交
 * @param array $data 訂單信息
 * @return int  訂單ID
 */
function add_order($data,$DB)
{
    $table = "orders";
    $result = $DB->Add($table,$data);
    $last_id=$DB->insert_id();

    global $history_content;

    $DB->Add("order_history",array(
        "order_id"=>$last_id,
        "content"=>$history_content,
        "status"=>0,
        "create_date"=>date('Y-m-d H:i:s')
    ));

    return  $last_id;
}
/**
 * 添加订单产品信息
 * @param int $order_id  订单ID
 * @param array $data   订单产品信息
 */
function add_order_item($order_id ,$data)
{
    global $DB,$SITECONFIGER;
    $re_price = isset($data['re_price'])?$data['re_price']:0;
    $re_status = isset($data['re_status'])?$data['re_status']:1;
    $product_name = isset($data['product_name'])?$data['product_name']:NULL;
    $sku_prop = isset($data['sku_prop'])?$data['sku_prop']:NULL;
    $color_photo = isset($data['color_photo'])?$data['color_photo']:NULL;
    $presale = isset($data['presale'])?(int)$data['presale']:0;
    $sku_sn = isset($data['sku_sn']) ? $data['sku_sn'] : NULL;

    // 2017-10-19新增combomeal_id字段
    $combomeal_id = 0;
    if (isset($data['combomeal_id'])) {
        $combomeal_id = $data['combomeal_id'];
    }

    $table = "order_items";
    $order_item = array(
        "order_id"=>$order_id,
        "product_id"=>$data["product_id"],
        "size_prop"=>$data["size_prop"],
        "color_prop"=>$data["color_prop"],
        "price"=>$data["price"],
        "num"=>$data["num"],
        "amount"=>$data["amount"],
        're_price'=>$re_price,
        're_status'=>$re_status,
        'product_name'=>$product_name,
        'sku_prop'=>$sku_prop,
        'color_photo'=>$color_photo,
        'presale'=>$presale,
        'combomeal_id'=>$combomeal_id
    );
    $result = $DB->Add($table,$order_item);

    //求取sku
    if(empty($sku_sn)) {
        $row = $DB->GetRs('products', "sku_sn", "WHERE product_id = {$data['product_id']}");
        $sku_sn = isset($row['sku_sn']) ? $row['sku_sn'] : '';
    }

    $table = "stock";
    $result = $DB->Set($table,"quantity=quantity-".(int)$data['num']," WHERE sku_sn = '".$sku_sn."' AND sku_prop = '".$data['sku_prop']."' AND depot_id=".$SITECONFIGER['sys']['default_depot_id']);

    $table = "products";
    $result = $DB->Set($table,"sale=sale+".(int)$data['num'].",total_quantity=total_quantity-".(int)$data['num'],"WHERE product_id = ".(int)$data['product_id']);

    $Condition = "where product_id = ".(int)$data['product_id'];
    $row = $DB->GetRs($table,"total_quantity",$Condition);
    if(!empty($row)){
        if($row["total_quantity"]==0){
            $result = $DB->Set($table,array("stock"=>0),"WHERE product_id = ".(int)$data['product_id']);
        }
    }

    $table = "cart";
    $DB->Del($table,"","","cart_id in(".(int)$data['cart_id'].")");
}