<?php
/**
 * 商品套餐
 */

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
if($id==0){
    header("Location: ?m=public&a=404&p1=product&p2=detail&e=noid&id=".$id);
}

// 查找套餐信息
$Table="combomeal";
$Fileds = "*";
$Condition = "where combomeal_id=".$id." AND NOW() < end_date";

$combomeal = $DB->GetRs($Table,$Fileds,$Condition);

if (!$combomeal) {
	// 不存在
	header("location:/?m=home");
	exit;
}

// 查找套餐图片
$Table="combomeal_img";
$Fileds = "*";
$Condition = "where combomeal_id=".$combomeal['combomeal_id']." Order by sort asc";
$combomealimg = $DB->GetAll($Table,$Fileds,$Condition);

// 套餐图片
$combomeal['imgs'] = $combomealimg;


// 查找商品信息
$t = $Table="combomeal_item";
// 商品图片
$product_img = "(select url from product_img where product_id = b.product_id order by sort limit 1) product_img";
$Fileds = "{$t}.combomeal_id,{$t}.combomeal_price,{$t}.combomeal_num,b.product_id,b.product_name,b.price,b.market_price,b.sku_sn,{$product_img}";
$Condition = "join products b on combomeal_item.product_id = b.product_id where combomeal_id=".$combomeal['combomeal_id']." ";
$products = $DB->GetAll($Table,$Fileds,$Condition);


// 套餐金额
$combomeal['price'] = 0;

// 套餐商品总数原价
$originalPrice = 0;

// 商品库存
foreach ($products as $k => $v) {
	// 计算套餐价
	$combomeal['combomeal_price'] += $v['combomeal_price'] * $v['combomeal_num'];
	// 计算套餐商品原总价
	$originalPrice += $v['price'] * $v['combomeal_num'];

	// 查找
	/* 商品规格 新 */
    $props = $DB->GetAll("prop","DISTINCT color_prop,prop_id,sku_sn,(select presale_date from products where product_id=prop.product_id) as presale_date","where product_id=".$v['product_id']." GROUP BY color_prop");

    // 查找库存数据
    foreach ($props as $k1 => $v1) {
	    $stockCondition = "WHERE sku_sn='".$v['sku_sn']."' AND color_prop='".$v1['color_prop']."' AND depot_id=".(int)$SITECONFIGER['sys']['default_depot_id']." AND quantity>0 GROUP BY size_prop";
	    // 库存数据
	    $stocks = $DB->GetAll("stock","*",$stockCondition);

	    if ($stocks) {
	    	// 重组库存
	    	$newStocks = [];

	    	// 计算库存总数
	    	foreach ($stocks as $k2 => $v2) {
	    		$products[$k]['total_quantity'] += $v2['quantity'];
	    		$newStocks[$v2['size_prop']] = $v2;
	    	}

	    	// 库存排序
	    	$props[$k1]['stocks'] = $Base->propKsSort($newStocks,false);

	    	// 提取商品图片
		    if ($props[$k1]['stocks']) {
		    	$props[$k1]['img'] = $props[$k1]['stocks'][0]['photo_prop'];
			}
	    }
    }

    // 添加颜色库存
    $products[$k]['props'] = $props;
}

// 计算优惠金额
$combomeal['combomeal_discount_price'] = $originalPrice - $combomeal['combomeal_price'];
// 套餐商品
$combomeal['products'] = $products;

// p($combomeal);

// 分配模板数据
$sm->assign("combomeal", $combomeal, true);

// 隐藏底部导航栏
$site_nav_display = 'hide';

/*------------------------------------- 判断推广者,获取分享地址 -------------------------------*/
//判断推广者
$promote = $promote_link = '';

if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] != 0) {
    $promote = $DB->GetRs("promote","*","WHERE user_id = ".$_SESSION["user_id"]);

    if(!empty($promote)) {
        $promote_link = $Base->getPromoteLink(PROMOTE_HTTP,$promote['promote_id'],0,$product_id);
    }else {
        $promote = '';
    }
}
// echo $promote_link;exit();
$sm->assign("promote", $promote, true);
$sm->assign("promote_link", $promote_link, true);

// 默认图片地址
$promote_imgurl = 'http://m.25boy.cn/statics/img/logo_128x128_small.png';
// 如果有图片数组
if (count($combomeal['imgs'])) {
	$promote_imgurl = $combomeal['imgs'][0]['url'];
}
$sm->assign("promote_imgurl", $promote_imgurl, true);

