<?php
include_once($_SERVER['DOCUMENT_ROOT']."/class/Activity.php");
include_once($_SERVER['DOCUMENT_ROOT']."/class/Curl.php");
$activityModel = new Activity();
$curlClass = new Curl();

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
$user_id = isset($_SESSION["user_id"]) ? intval($_SESSION["user_id"]) : 0;
if($id==0){
    header("Location: /");
}

$productdetail = array();
//print_r($signPackage);

// 查询商品
$query = $DB->query("SELECT p.*,b.brand_name FROM products AS p LEFT JOIN brands AS b ON p.brand_id=b.brand_id WHERE p.product_id={$id}");
$productdetail = $DB->fetch_array($query);

// 商品状态，0=正常，1=仅会员可见，2=仅分销可见
switch ($productdetail['status']) {
  case '2':
    if(empty($is_seller)){
      // '不是分销，禁止浏览';
      header("Location: /");
    }
    break;
  case '1':
    if(empty($user_id) || !empty($is_seller)){
      // '不是普通会员，禁止浏览';
      header("Location: /");
    }
    break;
  default:
    # code...
    break;
}

if(empty($productdetail)){
    header("Location: /");
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

// 活动信息
$activitys = $activityModel->getUserActivity($business_id, $user);


// 商品预售
$productdetail["presale_date"]=empty($productdetail["presale_date"])?'':date('Y-m-d',strtotime($productdetail["presale_date"]));
// 查询收藏数
$favorites = $DB->GetRs("favorites","count(*) as count","WHERE product_id={$productdetail['product_id']}");
$productdetail["favorites"] = isset($favorites['count']) ? intval($favorites['count']) : 0;
// 查询秒杀价
$query = $DB->query("SELECT `mi`.`miao_price` FROM `miao_item` `mi` LEFT JOIN `miao` `m` ON `m`.`miao_id` = `mi`.`miao_id` WHERE `mi`.`product_id` = {$productdetail['product_id']} AND (`m`.`start_date` < now() AND `m`.`end_date` > now()) OR `m`.`start_date` > now()");
$miao = $DB->fetch_array($query);
$productdetail["miao_price"] = isset($miao['miao_price']) ? $miao['miao_price'] : 0;
// 查询配送信息
$delivery = $DB->GetRs('delivery','delivery_desc',"WHERE delivery_id={$productdetail['delivery_id']}");
$productdetail["delivery_desc"] = isset($delivery['delivery_desc']) ? trim($delivery['delivery_desc']) : '';

// 查询图片
$Table="product_img";
$Fileds = "*";
$Condition = "where product_id=".$id." Order by sort asc";
$productimg = array();
$Row = $DB->Get($Table,$Fileds,$Condition,0);
$Row = $DB->result;
$RowCount = $DB->num_rows($Row);
if($RowCount!=0){
    while($result = $DB->fetch_assoc($Row)){
        array_push($productimg, array(
            "id"=>$result["temp_id"],
            "product_id"=>$result["product_id"],
            "full"=>$result["url"]."!wxbp",
            "thumb"=>$result["url"]."!w640"
        ));
    }
}
$productdetail["img"]=$productimg;
$productdetail["url"] = isset($productimg[0]['url']) ? trim($productimg[0]['url']) : '';

//获取分销商
$seller = $DB->GetRs('seller', '*',"where user_id=" . $user_id);
$is_seller = $seller ? 1 : 0;

// 分销供货价
if($is_seller){
  $item = $DB->GetRs('seller_item', '*', "WHERE product_id = ".$productdetail["product_id"]);
  if($item) {
      $discounts = unserialize($item['discounts']);
      $seller_discount = $discounts[$seller['seller_level_id']];
      if(isset($seller_discount['type']) && $seller_discount['type']=='price'){
          // 设置分销供货价
          $productdetail['seller_price'] = number_format($seller_discount['value'], 2, '.', '');
      }else{
          // 按折扣计算分销供货价
          if(isset($seller_discount["value"]) && $seller_discount["value"] > 0 && $seller_discount["value"] < 10 ){
              $productdetail['seller_price'] = round($seller_discount["value"]*$productdetail['price']/10, 2);
          }
      }
  }
}


/*$Table="tags";
$Fileds = "*";
$Condition = "where product_id=".$id." Order by clicks desc";
$Row = $DB->Get($Table,$Fileds,$Condition,0);
$Row = $DB->result;
$RowCount = $DB->num_rows($Row);*/

// 标签
$query = $DB->query("SELECT * FROM tags AS t JOIN tag_product AS tp ON t.tag_id=tp.tag_id WHERE tp.product_id={$id}");
$producttags = array();
while($result = $DB->fetch_assoc($query)){
  array_push($producttags, array(
    "tag_name"=>$result["tag_name"],
    "clicks"=>$result["clicks"]
  ));
}
$productdetail["tag"]=$producttags;
if($productdetail["stock"]==1 && $productdetail["total_quantity"]>0){
    $undercarriage = false;
}else{
    $undercarriage = true;
}

if(!$undercarriage){
    /* 商品规格 新 */
    $Row = $DB->Get("prop","DISTINCT color_prop,prop_id,sku_sn,(select presale_date from products where product_id=prop.product_id) as presale_date","where product_id=".$id." GROUP BY color_prop");
    $Row = $DB->result;
    $props = array();
    $sqlcolor = $where = '';
    $presale_date = null;
    while($result = $DB->fetch_assoc($Row)) {
        $props[] = $result;
        $sqlcolor .= ",'".$result['color_prop']."'";
        $presale_date = $val['presale_date'];
    }

    // 按颜色分组
    
    $Condition = "WHERE sku_sn='".$productdetail['sku_sn']."' AND depot_id=".(int)$SITECONFIGER['sys']['default_depot_id']." AND quantity>0";
    if(!empty($sqlcolor)){
        if(count($props) > 1){
            $Condition .= " AND color_prop IN(".trim($sqlcolor,',').") ";
        }else{
            $Condition .= " AND color_prop=".trim($sqlcolor,',');
        }
    }
    $Condition .= " ORDER BY sort ASC";
    $Row = $DB->Get("stock","*",$Condition);
    $Row = $DB->result;
    $stocks = $stockprops = array();
    while($val = $DB->fetch_assoc($Row)) {
        $stocks[$val['color_prop']][] = $val;
    }
    foreach ($stocks as $key => $val) {
        $stockprops[$key]['img'] = $val[0]['photo_prop'];
        $stockprops[$key]['name'] = $val[0]['color_prop'];
        foreach ($val as $k => $v) {
          $stockprops[$key]['size'][$v['size_prop']] = array(
            'sku' => $v['size_prop'],
            'num' => $v['quantity'],
            'sync' => ((strtotime($presale_date)>time()) ? $v['sync'] : 1),
          );
        }
    }
    // 按尺码排序
    foreach ($stockprops as $key => $val) {
        $stockprops[$key]['size'] = $Base->propKsSort($stockprops[$key]['size'],false);
    }
    // print_r($stockprops);
    $productdetail["prop"] = $stockprops;
    $productdetail["size_props"] = reset($stockprops);
    
    // 收藏夹
    if($user_id){
        $Row = $DB->Get("favorites","*","where user_id=".$user_id." and product_id=".$id,0);
        $Row = $DB->result;
        $RowCount = $DB->num_rows($Row);
        $productdetail["favorites"] = $RowCount!=0 ? 1 : 0;
    }else{
        $productdetail["favorites"] = 0;
    }
}

$DB->Set('products', "click=click+1", "where product_id=" . (int)$id);


//浏览历史 每个用户只记录50条
if(!$undercarriage) { //是否下架
    if ($user_id) { //是否登录
        $Table = "browse_history";
        $Fileds = "product_id,user_id";
        $Condition = "where product_id=" . $id . " and user_id=" . $user_id;
        $row = $DB->GetRs($Table, $Fileds, $Condition);
        if (empty($row)) {

            $result = $DB->Get($Table, "history_id", "where user_id=".$user_id, 0);
            $RowCount = $DB->num_rows($result);
            if($RowCount>50){
                $DB->Del($Table,"","","user_id=".$user_id." order by click asc,create_time asc limit 1");
            }

            $DB->Add($Table, array(
                "product_id" => $id,
                "user_id" => $user_id,
                "create_time" => date('Y-m-d H:i:s'),
                "click" => 1,
                "product_price"=>$productdetail["price"],
                "product_name"=>$productdetail["product_name"],
                "product_image"=>$productdetail["url"]
            ));
        } else {
            $DB->Set($Table, "click=click+1,create_time=NOW()", "where product_id=" . $id . " and user_id=" . $user_id);
        }
    }
}

// 秒杀活动
$Table="v_miao";
$Fileds = "*";
$Condition = "where start_date > NOW() AND product_id=".$id;
$miao = $DB->GetRs($Table,$Fileds,$Condition,0);

if(isset($miao['start_date'])){
    $miao['start_date'] = date('m月d日 H:i',strtotime($miao['start_date']));
}
// --------
$Condition = "where start_date < NOW() AND end_date > NOW() AND product_id=".$id;
$miaoing = $DB->GetRs($Table,$Fileds,$Condition,0);
$is_plus = 1;
if(isset($miaoing['end_date'])){
    $miaoing['end_date'] = date('m月d日 H:i',strtotime($miaoing['end_date']));
    $is_plus = $miaoing['plus'];
}

// 普通活动，排除不允许秒杀叠加
$events = array();
/*if($is_plus==1){
    $events = $Common->isEvent($id);
}*/

// 活动后的价格
$afterEventPrice = $productdetail['price'];

/*if(count($events) > 0){
    $events_new = array();
    foreach ($events as $key=>$val) {
        if($key>0){break;}
        $event_value = unserialize($val['event_value']);
        $event_price = unserialize($val['event_price']);
        $event_set = '';
        for($i=0;$i<count($event_price);$i++){
            switch ($val['event_index']) {
                case '1':
                  $event_title = $i+1 .'. 满'.$event_value[$i].'元,减'.$event_price[$i].'元<br/>';
                  break;
                case '2':
                  $event_title = $i+1 .'. 满'.$event_value[$i].'元,打'.$event_price[$i].'折<br/>';
                  break;
                case '3':
                  $event_title = $i+1 .'. 满'.$event_value[$i].'件,减'.$event_price[$i].'元<br/>';
                  break;
                case '4':
                  $event_title = $i+1 .'. 满'.$event_value[$i].'件,打'.$event_price[$i].'折<br/>';
                  if ($event_value[$i] == 1) {
                    $afterEventPrice *= $event_price[$i] * 0.1;
                  }
                  break;
                default:
                  $event_title = '';
                  break;
            }
            $event_set .= $event_title;
        }
        $val['event_set'] = $event_set;
        $val['start_date'] = date('Y-m-d H:i',strtotime($val['start_date']));
        $val['end_date'] = date('Y-m-d H:i',strtotime($val['end_date']));
        $events_new[$key] = $val;
    }
}*/

// pe($afterEventPrice);
// pe($events_new);

// 页面标题
$page_sed_title = $productdetail["product_name"];
$page_title = $productdetail["brand_name"].' '.$productdetail["product_name"];
$keys = array();
foreach ($productdetail['tag'] as $tag) {
    $keys[] = $tag['tag_name'];
}
$keys[] = $productdetail['sku_sn'];
$seo_keyword = implode(',', $keys).','.$seo_keyword;

  /*------------------------------------- 记录推广信息 -------------------------------*/

  $product_id = $id;
  $PI = isset($_GET["PI"])?$_GET["PI"]:0;
  if(!empty($PI)) {
    //判断推广者
    $promote_id = $Base->myDecode($PI);
    $promote = $DB->GetRs("promote","*","WHERE promote_id = ".$promote_id);
    // print_r($promote);exit();
    //存在未被冻结的推广者
    if(!empty($promote) && !$promote['is_frozen']) {
      $promote_id = $promote['promote_id'];
      //记录cookie
      $default_expire = time()+3600*24*30; //默认有效期
      $promote_save = $promote_config['promote_save']; //保存天数
      $promote_expire = empty($promote_save) ? $default_expire : time()+3600*24*$promote_save;
      if(!isset($_COOKIE['rememberMe'])) {
        setcookie('rememberMe',base64_encode($promote_id),$promote_expire,'/','25boy.cn');  
      }
      // exit();
      //判断推广项目(首次全站/后续购物)
      $row = $DB->GetRs("promote_item","*","WHERE promote_id = ".$promote_id." AND type = 0 AND item_id = ".$product_id);
      // print_r($row);exit();
      //如果项目为空，则插入新的推广项目
      if(empty($row)) {
        $DB->Add("promote_item",array(
            "promote_id" => $promote_id,
            "type"       => 0,
            "item_id"    => $product_id,
            "create_time"=> date("Y-m-d H:i:s"),
        ));       
        $pitem_id = $DB->insert_id();
      }else {
        $pitem_id = $row['pitem_id'];
      }
      // echo $pitem_id;exit();
      //获取计划(后续购物)
      $pplan_id = 0;
      $promote_product_list = $Common->get_beyond_product_list($promote_id);
      // print_r($promote_product_list);exit();
      if(!empty($promote_product_list[$product_id])) {
        $promote_product = $promote_product_list[$product_id];
        $pplan_id = isset($promote_product['pplan_id'])?$promote_product['pplan_id']:0;
      }

      //记录点击(首次/后续购物)
      $DB->Add("promote_click",array(
          "pitem_id"  => $pitem_id,
          "pplan_id"  => $pplan_id,
          "source"    => 'mobile',
          "click_time"=> date("Y-m-d H:i:s"),
      )); 
    }
  }

  /*------------------------------------- 判断推广者,获取分享地址 -------------------------------*/

//判断推广者
$promote = $promote_link = '';

if ($user_id) {
    $promote = $DB->GetRs("promote","*","WHERE user_id = ".$user_id);

    if(!empty($promote)) {
        $promote_link = $Base->getPromoteLink(PROMOTE_HTTP,$promote['promote_id'],0,$product_id);
    }else {
        $promote = '';
    }
}

// 简单的标题
$simple_title = str_replace(array("此商品不支持7天无理由退换货","【部分预售】","【预售】"),"",$productdetail['product_name']);


// 微信分享
$link = 'http://m.25boy.cn/?m=category&a=product&id='.$productdetail['product_id'];
$wxconfigarray = array(
    'title' => $productdetail['brand_name'].' '.$productdetail['product_name'],
    'link' => $promote_link ? $promote_link : $link,
    'imgUrl' => $productdetail['url'],
    'desc' => "我在25BOY发现了【{$simple_title}】，25BOY国潮男装。",
);

// pe($miaoing);
// echo $promote_link;exit();
$sm->assign("promote", $promote, true);
$sm->assign("promote_link", $promote_link, true);
$sm->assign("undercarriage", $undercarriage, true);
$sm->assign("productdetail", $productdetail, true);
// print_r($productdetail);
$sm->assign("stock_props_json", json_encode($productdetail['prop']), true);
$sm->assign("size_props_json", json_encode($productdetail['size_props']), true);
$sm->assign("events", $events_new, true);
$sm->assign("activitys", $activitys, true);
$sm->assign("after_event_price", $afterEventPrice, true);
$sm->assign("miao", $miao, true);
$sm->assign("miaoing", $miaoing, true);
// 副标题
$sm->assign("product_title2", $sysinfo['product_title'], true);
// 推广用的标题
$sm->assign("simple_title", $simple_title, true);


// 隐藏底部导航栏
$site_nav_display = 'hide';


// 2017-10-19 新增商品套餐查找
// 查找条件
$combomeal_where = "JOIN combomeal b ON b.combomeal_id = combomeal_item.combomeal_id WHERE combomeal_item.product_id = {$product_id} AND NOW() < b.end_date";
$combomeals = $DB->GetAll('combomeal_item',"b.*",$combomeal_where);

if ($combomeals) {
    foreach ($combomeals as $k => $v) {
        // 查找套餐图片
        $combomeal_img = $DB->GetRs('combomeal_img','url',"WHERE combomeal_id = {$v['combomeal_id']} ORDER BY sort");
        if ($combomeal_img) {
            $combomeals[$k]['url'] = $combomeal_img['url'];
        }

        // 计算可省金额
        // 查找商品信息
        $t = $Table="combomeal_item";
        $Fileds = "{$t}.combomeal_id,{$t}.combomeal_price,{$t}.combomeal_num,b.product_id,b.product_name,b.price,b.market_price,b.sku_sn";
        $Condition = "join products b on combomeal_item.product_id = b.product_id where combomeal_id=".$v['combomeal_id'];
        $products = $DB->GetAll($Table,$Fileds,$Condition);

        // 可省金额
        $discountTotal = 0;
        foreach ($products as $k1 => $v1) {
            $discountTotal += ($v1['price'] - $v1['combomeal_price']) * $v1['combomeal_num'];
        }
        // 设置套餐可省金额
        $combomeals[$k]['discountTotal'] = $discountTotal;

        // 时间日期转日期
        $combomeals[$k]['end_date'] = date('Y-m-d', strtotime($v['end_date']));
    }
}

// 模拟多条数据
// $combomeals[1] = $combomeals[0];
// $combomeals[2] = $combomeals[0];

// 分配套餐数据
$sm->assign('combomeals',$combomeals,true);

