<?php
include_once($_SERVER['DOCUMENT_ROOT']."/class/Activity.php");
include_once($_SERVER['DOCUMENT_ROOT']."/class/Voucher.php");
include_once($_SERVER['DOCUMENT_ROOT']."/class/Curl.php");
$voucherModel = new Voucher();
$activityModel = new Activity();
$curlClass = new Curl();


$cart_id = isset($_POST["cart_id"]) ? $_POST["cart_id"] : "";
$voucher_id = isset($_POST["voucher_id"]) ? intval($_POST["voucher_id"]) : 0;
$user_id = isset($_SESSION["user_id"]) ? intval($_SESSION["user_id"]) : 0;
$goods_total = 0;
$total_quantity = 0;
$pay_total = 0;

$cart_id_array = explode(",",$cart_id);
$order_list = array();
if($cart_id==""){
    header("location:/?m=home");
}
$default_ID = 0;
$balance = 0;


// 附近店铺信息
$business_id = '';
$nearstores = $curlClass->get('o2o/getNearStores');
if($nearstores && $nearstores['code'] === 0){
  $business_id = $nearstores['rs']['business_id'];
}

// 会员信息
$user = [];
if(!empty($user_id)){
    $query = $DB->query("select u.user_id,u.`level`,s.seller_level_id from users u left join seller s on u.user_id=s.user_id where u.user_id={$user_id}");
    $user = $DB->fetch_array($query);
}

// 新活动 - 按活动分组的购物车
$activitys = $activityModel->getUserActivity($business_id, $user);



/******************************************************************
    配送方式
*******************************************************************/

$deliverys = array();
$result = $DB->Get('delivery','*',"WHERE status = 1 ORDER BY is_default DESC,delivery_id ASC");
while (!!$rows = $DB->fetch_array($result)) {
    $deliverys[$rows['delivery_id']] = $rows;
}



/******************************************************************
    获取用户信息
*******************************************************************/
$Table="users";
$Fileds = "address_id,bag_total,level";
$Condition = "where user_id=" . (int)$user_id;
$row = $DB->GetRs($Table,$Fileds,$Condition);
if(!empty($row)){
    $default_ID = $row["address_id"];
    $balance = $row["bag_total"];
}else{
    $default_ID = 0;
}

/******************************************************************
    地址信息
*******************************************************************/

$default_address = array();
$address_list = array();
$query = $DB->query("SELECT a.*,
        (select area_name from area where a.state=area.area_id and area.area_type=1) as state_name,
        (select area_name from area where a.city=area.area_id and area.area_type=2) as city_name,
        (select area_name from area where a.district=area.area_id and area.area_type=3) as district_name 
        FROM address a WHERE user_id={$user_id} ORDER BY modify_date DESC");
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

/******************************************************************
    分销结算入口
*******************************************************************/
if($is_seller) {

    // 购物车
    $cart_product = $Common->carts($cart_id, $seller);
    if(count($cart_product)==0){
        header("location:/?m=cart");
    }

    // 检查库存
    $no_quantity = null;
    foreach ($cart_product as $key => $val) {
        if($val['quantity'] > $val['siglequantity']){
            $no_quantity .= $no_quantity ? "，" : '';
            $no_quantity .= $val['product_name'].' '.$val['color_prop'].$val['size_prop'].' 库存不足';
        }
    }
    if($no_quantity == true){
        echo '<script>alert("'.$no_quantity.'");javascript:history.go(-1);</script>';
        exit();
    }

    // 查询当前用户是否符合可领取产品奖品
    $prize_data = $Common->get_product_prizes($cart_product, $user_id);
    $prizes = $prize_data['prizes'];
    $prize_cut_total = isset($prize_data['prize_cut_total'])?$prize_data['prize_cut_total']:0;
    $cart_product = $prize_data['carts'];

    /******************************************************************
        优惠入口
    *******************************************************************/
    $carts = $cart_product;

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

    /******************************************************************
        优惠提示入口
    *******************************************************************/

    $ship_price = 0;    //运费
    $total_price = 0;   //总价(商品原价)
    $products_num = 0;  //商品数量
    $hasevents = "no";
    //得出每个商品的优惠名称
    foreach ($carts as $k=>$cart) {
        $event_title = '';
        $products_num += $cart["quantity"];
        $price = isset($cart['miao_price'])&&$cart['miao_price']>0?$cart['miao_price']:$cart['product_price'];
        $total_price += $price*$cart["quantity"];

        if(isset($cart['prize']['prize_title'])){
            $event_title = $cart['prize']['prize_title'];
        }
        // 这里是无活动商品又是会员的专享提示
        if(isset($cart['orig_price'])) {
            $event_title = '供货价';
            $carts[$k]['re_price'] = $cart['product_price'];
            $carts[$k]['product_price'] = $cart['orig_price'];
        }
        $carts[$k]['re_price'] = isset($carts[$k]['re_price']) ? $carts[$k]['re_price']: $price;
        $carts[$k]['event_title'] = $event_title;
    }

    $total_price_and_ship = $total_price + $ship_price;

    $cart_product = $carts;

    

    //运费
    $ship_fee = $Common->get_ship_fee(implode(',', $product_ids),$address_id,$sub_total);
    
    $sm->assign("ship_fee", $ship_fee, true);
    $sm->assign("seller", $seller, true);
}


/******************************************************************
    会员结算入口
*******************************************************************/

if(!$is_seller && isset($user_id) && !empty($user_id)) {

    // 购物车
    $cart_product = $Common->carts($cart_id);
    if(count($cart_product)==0){
        header("location:/?m=cart");
    }

    // 检查库存
    $no_quantity = null;
    foreach ($cart_product as $key => $val) {
        if($val['quantity'] > $val['siglequantity']){
            $no_quantity .= $no_quantity ? "\n" : '';
            $no_quantity .= $val['product_name'].' '.$val['color_prop'].$val['size_prop'].' 库存不足';
        }
    }
    if($no_quantity == true){
        echo '<script>alert("'.$no_quantity.'");javascript:history.go(-1);</script>';
        exit();
    }


    // 查询当前用户是否符合可领取产品奖品
    $prize_data = $Common->get_product_prizes($cart_product, $user_id);
    $prizes = $prize_data['prizes'];
    $prize_cut_total = $prize_data['prize_cut_total'];
    $cart_product = $prize_data['carts'];
        
    /******************************************************************
        活动入口
    *******************************************************************/

    // 按活动分组的购物车
    /*$cart_product = $Common->get_product_events($cart_product);
    
    $event_group = $Common->event_group($cart_product);
    // 根据活动分组的购物车优惠价
    foreach ($event_group as $key=>$val) {
        $amount = 0;
        $quantity = 0;
        $three = array(); //三免一优惠的价格
        foreach ($val['items'] as $item) {
            $amount += ($item['miao_price']?$item['miao_price']:$item['product_price'])*$item['quantity'];
            $quantity += $item['quantity'];
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
    }
    $new_event_group = $Common->array_sort($event_group,'group_price','desc');
    // 活动总优惠价
    $total_event_price = 0;
    foreach ($new_event_group as $group) {
        $total_event_price += $group['group_price'];
    }*/

    /******************************************************************
        VIP入口 无活动商品专享会员折扣
    *******************************************************************/

    /*$discount_price = 0;
    $cart2 = array(); //这里面装的是没有参加活动的商品，可又是VIP会员的card_id
    if($row['level'] > 0) {
        $Table="level";
        $Fileds = "*";
        $Condition = "where id=" . $row['level'];
        $vip = $DB->GetRs($Table,$Fileds,$Condition);
    }
    if(isset($vip)) {
        foreach ($cart_product as $value) {
            // 排除活动与奖品商品
            if(empty($value['event']) && empty($value['prize'])) {
                $discount_price += $value['product_price'] * $value['quantity'] * (1 - $vip['discount'] / 10);
                $cart2[] = $value['cart_id'];
            }
        }  
    }
    //总优惠价＝活动商品的优惠价+无活动商品专享会员折扣(VIP)
    $total_event_price += $discount_price;*/

    /******************************************************************
        优惠提示入口
    *******************************************************************/

    $ship_price = 0;    //运费
    $total_price = 0;   //总价(商品原价)
    $products_num = 0;  //商品数量
    $hasevents = "no";
    //得出每个商品的优惠名称
    foreach ($cart_product as $k=>$cart) {
        $event_title = array();
        $products_num += $cart["quantity"];
        $price = isset($cart['miao_price'])&&$cart['miao_price']>0?$cart['miao_price']:$cart['product_price'];
        $total_price += $price*$cart["quantity"];

        /*if(isset($cart['prize']['prize_title'])){
            $event_title[] = $cart['prize']['prize_title'];
        }else{
            // 活动优惠提示 
            foreach ($new_event_group as $group) {     
               foreach ($group['items'] as $item) {
                  if($item['product_id'] == $cart['product_id']){
                      $event_title[] = $group['group_title'];
                      $hasevents = "yes";
                      break;
                  }
                }
             }
        }
        // 这里是无活动商品又是会员的专享提示
        if(isset($vip) && empty($cart['event']) && empty($cart['prize'])) {
            $event_title[] = $vip['level'].'专享'.$vip['discount'].'折';
        }
        $cart_product[$k]['event_title'] = $event_title;*/
    }

    $total_price_and_ship = $total_price + $ship_price;


    // 新购物车

    $carts = $cart_product;
    $carts = $activityModel->carts_join_activitys($carts, $activitys);
    // 购物优惠后总价
    $total_event_price = 0;
    // 原总价
    $org_pay_total = 0;
    foreach ($carts as $key => $val)
    {
        $val['re_price'] = isset($val['re_price']) ? $val['re_price'] : $val['product_price'];
        $carts[$key]['re_price'] = sprintf('%.2f', $val['re_price']);
        $carts[$key]['subtotal'] = sprintf('%.2f', $val['re_price']*$val['quantity']);
        $carts[$key]['org_subtotal'] = sprintf('%.2f', $val['product_price']*$val['quantity']);
        $total_event_price += (($val['product_price']-$val['re_price'])*$val['quantity']);
        $org_pay_total += ($val['product_price']*$val['quantity']);
    }

    // 会员组折扣
    $activityModel->userLevelDiscount($carts, $user, $org_pay_total, $total_event_price);
    // 重新计算优惠减免金额
    $total_event_price = 0;
    // 实付价格
    $pay_total = 0;
    $total_quantity = 0;
    $goods_total = 0;
    foreach ($carts as $key => $val) {
        $total_event_price += (($val['product_price']-$val['re_price'])*$val['quantity']);
        $pay_total += ($val['re_price']*$val['quantity']);
        $total_quantity += $val['quantity'];
        $goods_total += $val['product_price']*$val['quantity'];
    }

    $cart_product = $carts;

    /******************************************************************
        代金券入口
     *******************************************************************/

    // 店铺代金券，有奖品 和 VIP设置不可用 - 旧的
    /*$coupon_list = array();
    if($prize_cut_total==0 && (empty($vip) || (isset($vip) && $vip['is_coupon']==1))) {
        $coupon_list = $Common->get_coupon($total_price - $total_event_price, $cart_product, $hasevents);
    }*/
}

// ajax返回
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // 优惠券 - 新
    $vouchers = $voucherModel->getAvailableVoucherGroupModel($user_id, $pay_total, $total_quantity, $org_pay_total);
    if ( ! empty($vouchers)) {
        foreach ($vouchers as $key => $value) {
            if ($value['voucher_id'] == $voucher_id) {
                $voucher = $value;
                break;
            }
        }
    }
    // 不与其他活动叠加券，先计算
    if(!empty($voucher)) {
        if ($voucher['use_mode'] == 2) {
            $pay_total = 0; // 重新计算实付金额
            $total_event_price = 0; // 其他活动优惠
            $coupon_price = 0;
            $ratio = $voucher['pay_total']/$voucher['original_pay_total'];
            foreach ($cart_product as $key => $val) {
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
                    $total_event_price += ($val['product_price'] - $re_price) * $val['quantity'];
                }else{
                    // 使用券优惠
                    $re_price = $val['product_price']*$ratio;
                    $coupon_price += ($val['product_price'] - $re_price) * $val['quantity'];
                    $cart_product[$key]['activity_title'] = $voucher['title'];
                }
                $cart_product[$key]['re_price'] = $re_price;
                $pay_total += $re_price * $val['quantity'];
            }
        }else{
            // 非叠加模式，直接减免
            $coupon_price = $voucher['discount_total'];
            $pay_total -= $coupon_price;
        }
    }

    $resp = array(
        'carts' => $cart_product,
        'pay_total' => round($pay_total, 2),
        'coupon_price' => round($coupon_price, 2),
        'total_event_price' => round($total_event_price, 2)
    );
    echo json_encode($resp);
    exit;
}


$page_title = "订单确认";
$page_sed_title = '收货地址与优惠';
$sm->assign("total_price_and_ship",$total_price_and_ship,true);
$sm->assign("ship_price",$ship_price,true);
$sm->assign("total_price",$total_price,true);
$sm->assign("goods_total",$goods_total,true);
$sm->assign("total_quantity",$total_quantity,true);
$sm->assign("pay_total",$pay_total,true);
$sm->assign("cart_product",$cart_product,true);
$sm->assign("cart_id", $cart_id, true);
$sm->assign("default_address", $default_address, true);
$sm->assign("default_ID", $default_ID, true);
$sm->assign("balance", $balance, true);
$sm->assign("address_list", $address_list, true);
$sm->assign("return_code", $return_code, true);
$sm->assign("products_num", $products_num, true);
$sm->assign("coupon_list", $coupon_list, true);  // 直接在页面选择代金券
$sm->assign("coupon_list_json", NULL, true); // 弹出页面方式选择代金券
$sm->assign("event_group", $new_event_group, true);
$sm->assign("total_event_price", $total_event_price, true);
$sm->assign("hasevents", $hasevents, true);
$sm->assign("prizes", $prizes, true);
$sm->assign("prize_cut_total", $prize_cut_total, true);
$sm->assign("delivery", $deliverys, true);

$default_delivery_id = current($deliverys)['delivery_id'];
$sm->assign("default_delivery_id",$default_delivery_id , true);

