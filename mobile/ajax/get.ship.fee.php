<?php
/* 
* 计算运费模板
* @varchar      $product_ids    商品ID，多个以,分隔，例: 58x2,59x1
* @int/array    $address_id     (int)收货人地址ID，(array)收货省/市/区
* @float        $price          购物车总价
*/

include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
if(isset($_SESSION["user_id"])&&!empty($_SESSION["user_id"])) {

    $product_ids = isset($_GET["product_ids"]) ? $_GET["product_ids"] : 0;
    $address_id = isset($_GET["address_id"]) ? $_GET["address_id"] : 0;
    $price = isset($_GET["price"]) ? $_GET["price"] : 0;

   	$fee = $Common->get_ship_fee($product_ids, $address_id, $price);

   	echo (int)$fee;exit;

}else{
    echo json_encode(array(
        "status"=>"nologin"
    ));
    exit;
}