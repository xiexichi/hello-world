<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
include_once($_SERVER['DOCUMENT_ROOT']."/class/Activity.php");
include_once($_SERVER['DOCUMENT_ROOT']."/class/Curl.php");
$activityModel = new Activity();
$curlClass = new Curl();


$pageSize = isset($_GET["pagesize"]) ? (int)$_GET["pagesize"] : 20;
$pagecurrent =  isset($_GET["page"]) ? (int)$_GET["page"] : 1;
$user_id = isset($_SESSION["user_id"]) ? intval($_SESSION["user_id"]) : 0;

if(!isset($user_id)||empty($user_id)) {
    echo json_encode(
        array("status"=>"nologin")
    );
    exit;
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
    $query = $DB->query("select u.user_id,u.`level`,s.seller_level_id from users u left join seller s on u.user_id=s.user_id where u.user_id={$user_id}");
    $user = $DB->fetch_array($query);
}

// 新活动 - 按活动分组的购物车
$activitys_title = '';
$activitys = $activityModel->getUserActivity($business_id, $user);
if(!empty($activitys)){
    $small_title = [];
    foreach ($activitys as $val) {
        $small_title[] = $val['title'];
    }
    $activitys_title = join($small_title, ',');
    $activitys_title .= '，点击结算享受优惠！';
}



//获取分销商
$seller = $DB->GetRs('seller', '*',"where user_id=" . $user_id);
$is_seller = $seller ? 1 : 0;


$carts = $Common->carts(null, $seller);
$productlist = array("status"=>"success","list"=>array());
$outstock = array(); //缺货商品列表

if(count($carts) > 0){
    foreach ($carts as $result) {
        /*$miao = $Common->check_miao($result["product_id"]);
        $event = $Common->check_events($result["product_id"]);
        $event_title = array();
        foreach ($event as $k => $v) {
            $event_value = unserialize($v['event_value']);
            $event_price = unserialize($v['event_price']);
            //活动提示
            for($i=0;$i<count($event_price);$i++){
              switch ($v['event_index']) {
                case '1':
                  $event_title[]= '满'.$event_value[$i].'元,减'.$event_price[$i].'元';
                  break;
                case '2':
                  $event_title[] = '满'.$event_value[$i].'元,打'.$event_price[$i].'折';
                  break;
                case '3':
                  $event_title[] = '满'.$event_value[$i].'件,减'.$event_price[$i].'元';
                  break;
                case '4':
                  $event_title[] = '满'.$event_value[$i].'件,打'.$event_price[$i].'折';
                  break;
                case '5':
                  $event_title[] = '满'.$event_value[$i].'件,免'.$event_price[$i].'件';
                  break;
                default:
                  $event_title[] = '';
                  break;
              }

            }
        }*/
        
        /*$seller_discount = null;

        if($is_seller) {
            //取得产品的折扣
            $where = "WHERE product_id = ".$result["product_id"];
            $item = $DB->GetRs('seller_item', '*', $where);
            if($item) {
                $discounts = unserialize($item['discounts']);
                $seller_discount = $discounts[$seller['seller_level_id']];
                if(isset($seller_discount['type']) && $seller_discount['type']=='price'){
                    // 设置分销供货价
                    $seller_price = number_format($seller_discount['value'], 2, '.', '');
                }else{
                    // 按折扣计算分销供货价
                    if(isset($seller_discount["value"]) && $seller_discount["value"] > 0 && $seller_discount["value"] < 10 ){
                        $seller_price = round($seller_discount["value"]*$result['price']/10, 2);
                    }
                }
            }
        }

        $arr = array(
            "cart_id"=>$result["cart_id"],
            "product_id"=>$result["product_id"],
            "product_name"=>$result["product_name"],
            "market_price"=>ceil($result["market_price"]),
            // "price"=>ceil($result['price']),
            "miao_price"=>$miao['miao_price']?ceil($miao['miao_price']):0,
            "total_quantity"=>$result["total_quantity"],
            "size_prop"=>$result["size_prop"],
            "quantity"=>$result["quantity"],
            "color_prop"=>$result["color_prop"],
            "stock"=>$result["stock"],
            "siglequantity"=>$result["siglequantity"],
            "thumb"=>$result["thumb"]."!w200",
            "order_time"=>isset($miao['order_time'])?$miao['order_time']:NULL,
            "event"=> !empty($event) ? $event_title : '',
            "presale"=>empty($result['sync'])?1:0,
            "presale_date"=>empty($result['presale_date'])?'':date('Y-m-d',strtotime($result['presale_date'])),
            "is_seller" => $is_seller,
            "seller_discount"=>$seller_discount,
        );
        if(isset($seller_price) && $seller_price>0){
            $arr['orig_price'] = ceil($result['price']);
            $arr['price'] = number_format($seller_price, 2, '.', '');
        }else{
            $arr['price'] = ceil($result['price']);
        }
        // 预售时间小于当前，改为非预售
        if(strtotime($arr['presale_date'])<time()){
            $arr['presale']=0;
        }*/
        if($result['siglequantity']<=0 || $result['stock']==0){
            array_push($outstock, $result);
        }else{
            array_push($productlist['list'], $result);
        }
    }
}


$carts = $productlist['list'];
$carts = $activityModel->carts_join_activitys($carts, $activitys);
//优惠减免金额
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
$userlevel_title = $activityModel->userLevelDiscount($carts, $user, $org_pay_total, $total_event_price);

// 输出数据
$productlist['list'] = $carts;
$productlist['activitys_title'] = $userlevel_title ? $userlevel_title : $activitys_title;


// 将缺货商品放置列表底部
$productlist['list'] = array_merge($productlist['list'], $outstock);

echo json_encode($productlist);
exit;

if($pagecurrent>$pageAll){
    echo json_encode(array("status"=>"nomore"));
}else{
    echo json_encode($productlist);
}

