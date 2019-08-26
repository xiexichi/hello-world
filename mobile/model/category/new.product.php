<?php

$page_title = "新品推荐";
$page_sed_title = '新品推荐';

$productlist = array();

$CKey = 'new_products';
$resultCache = $Cache -> get($CKey);
if (is_null($resultCache)){
    $Row = $DB->Get("v_picshow","DISTINCT(product_id),product_name,brand_name,brand_id,product_img,price,market_price,miao_price,sku_sn","where status=1 AND type='product' AND find_in_set(5,pos_ids) AND (start_date<NOW() OR start_date IS NULL) AND (end_date>NOW() OR end_date IS NULL) order by sort asc, date_added desc",50);
    $Row = $DB->result;
    $RowCount = $DB->num_rows($Row);
    $pageAll = $DB->pageAll;

    if($RowCount!=0){
        while($result = $DB->fetch_assoc($Row)){
            array_push($productlist, array(
                "product_id"=>$result["product_id"],
                "product_name"=>$result["product_name"],
                "miao_price"=>$result["miao_price"]!=""&&$result["miao_price"]!=null ? ceil($result["miao_price"]) : "",
                "market_price"=>ceil($result["market_price"]),
                "price"=>ceil($result["price"]),
                "brand_name"=>$result["brand_name"],
                "thumb"=>$result["product_img"]."!w390"
            ));
        }
    }
    $Cache->set($CKey, $productlist);
}else{
    $productlist = $resultCache;
}

$sm->assign("a",'new', true);
$sm->assign("productlist",$productlist, true);

// 隐藏底部导航栏
$site_nav_display = 'hide';