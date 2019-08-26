<?php
/*
* 品牌介绍二级页面
*/

$brand_id = isset($_GET['id']) ? intval($_GET['id']) : 2;
if(empty($brand_id)){
    header('location:/');
    exit;
}

$brand = $products = array();

$Table = "brands";
$Fileds = "*";
$Condition = "where brand_id=" . (int)$brand_id;
$brand = $DB->GetRs($Table, $Fileds, $Condition);
switch ($brand['brand_id']) {
	case '2':
		$brand['img_url'] = 'http://img.25miao.com/695/1474181314.jpg!w640';
		break;
	case '5':
		$brand['img_url'] = 'http://img.25miao.com/695/1470712278.jpg!w640';
		break;
	case '4':
		$brand['img_url'] = 'http://img.25miao.com/695/1488944935.jpg!w640';
		break;
	case '3':
		$brand['img_url'] = 'http://img.25miao.com/695/1488944934.jpg!w640';
		break;
	case '6':
		$brand['img_url'] = 'http://img.25miao.com/695/1488944936.jpg!w640';
		break;
	case '8':
		$brand['img_url'] = 'http://img.25miao.com/695/1488944938.jpg!w640';
		break;
	default:
		# code...
		break;
}

/*if(!empty($brand)){
	$Table = "v_product_list";
	$Fileds = "product_id,product_name,market_price,price,sku_sn,url,brand_id,brand_name,miao_price";
	$Condition = "where brand_id=" . (int)$brand_id;
	$Row = $DB->Get($Table, $Fileds, $Condition,20);
	$Row = $DB->result;
    $RowCount = $DB->num_rows($Row);
    if ($RowCount != 0) {
	    while($result = $DB->fetch_assoc($Row)){
	        array_push($products, array(
	            "product_id"=>$result["product_id"],
	            "product_name"=>$result["product_name"],
	            "miao_price"=>$result["miao_price"] ? ceil($result["miao_price"]) : "",
	            "market_price"=>ceil($result["market_price"]),
	            "price"=>ceil($result["price"]),
	            "total_quantity"=>$result["total_quantity"],
	            "sale"=>$result["sale"],
	            "hot"=>$result["hot"],
	            "brand_name"=>$result["brand_name"],
	            "thumb"=>$result["url"]."!w390"
	        ));
	    }
	}
}*/


// 页面seo
$page_title = $brand['brand_name'] . ' 官网购物平台';
$page_sed_title = $brand['brand_name'];


$sm->assign("brand", $brand, true);
$sm->assign("products", $products, true);