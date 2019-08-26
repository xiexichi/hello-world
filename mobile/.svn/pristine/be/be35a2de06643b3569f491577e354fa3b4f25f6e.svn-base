<?php
/**
* 套餐下单
*/


// 判断是否有登陆
if(!isset($_SESSION["user_id"])||empty($_SESSION["user_id"])) {
	header('Location:/?m=account');
    // echo json_encode(
    //     array("status"=>"no_login")
    // );
    exit;
}

// 处理提交数据
if (empty($_POST) || !isset($_POST['combomeal_id']) || !isset($_POST['products'])) {
	// 返回上一个页面
	if(isset($_SERVER["HTTP_REFERER"])){
		header('Location:'.$_SERVER["HTTP_REFERER"]);
	} else {
		header('Location:/');
	}
	exit;
}

// 查找套餐数据 （目前页面不需要显示套餐数据）
$combomeal = $DB->GetRs('combomeal','*',"WHERE combomeal_id = ".(int)$_POST['combomeal_id']);

// 查找商品数据
$products = json_decode($_POST['products'], TREU);


// 套餐总价
$combomealTotal = 0;

foreach ($products as $k => $v) {
	// 商品信息
	$product = $DB->GetRs('products','products.product_id,products.product_name,products.price product_price,b.combomeal_price,b.combomeal_num quantity',"JOIN combomeal_item b ON products.product_id = b.product_id WHERE products.product_id = ".(int)$k);
	
	// 商品库存信息
	$propStock = $DB->GetRs('prop','prop.prop_id,prop.sku_sn,b.color_prop,b.size_prop,b.photo_prop thumb',"JOIN stock b ON b.sku_sn = prop.sku_sn AND b.color_prop = prop.color_prop AND b.size_prop = '".$v['size']."' WHERE prop.product_id = ".(int)$k.' AND prop.color_prop = "'.$v['color'].'"');

	// 合并数据
	$products[$k] = array_merge($product,$v,$propStock);

	// 计算套餐总价
	$combomealTotal += $product['combomeal_price'];
}


// 默认信息
$default_ID = 0;
$balance = 0;

// 物流信息
$deliverys = array();
$result = $DB->Get('delivery','*',"WHERE status = 1 ORDER BY is_default DESC,delivery_id ASC");
while (!!$rows = $DB->fetch_array($result)) {
    $deliverys[$rows['delivery_id']] = $rows;
}


// p($deliverys);

//获取用户信息
$Table="users";
$Fileds = "address_id,bag_total,level";
$Condition = "where user_id=" . (int)$_SESSION["user_id"];
$row = $DB->GetRs($Table,$Fileds,$Condition);
if(!empty($row)){
    $default_ID = $row["address_id"];
    $balance = $row["bag_total"];
}else{
    $default_ID = 0;
}

$default_address = array();
$address_list = array();

$Table = "v_address";
$Fileds = "*";
$Condition = "where user_id=" . $_SESSION["user_id"]." order by modify_date desc";
$Row = $DB->Get($Table, $Fileds, $Condition, 0);
$RowCount = $DB->num_rows($Row);
if($RowCount!=0){
    $i=0;
    while($result = $DB->fetch_assoc($Row)){
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
            "state_id"=>$result["state_id"],
            "city_id"=>$result["city_id"],
            "district_id"=>$result["district_id"],
        ));
        $i++;
    }

}

// 地址列表
// p($address_list);



// 分配数据到视图
$page_title = "订单确认";
$page_sed_title = '收货地址与优惠';

// 收货地址信息
$sm->assign("default_address", $default_address, true);
$sm->assign("default_ID", $default_ID, true);
$sm->assign("balance", $balance, true);
$sm->assign("address_list", $address_list, true);

// 物流信息
$sm->assign("delivery", $deliverys, true);
$default_delivery_id = current($deliverys)['delivery_id'];
$sm->assign("default_delivery_id",$default_delivery_id , true);

// 商品信息
$sm->assign("products", $products, true);

// 套餐信息
$sm->assign("combomeal", $combomeal, true);
// 套餐总价
$sm->assign("total_price", $combomealTotal, true);

// 套餐选择商品
$sm->assign("combomeal_choose_products", $_POST['products'], true);

// 隐藏底部导航栏
$site_nav_display = 'hide';


