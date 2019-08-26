<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
if(!isset($_SESSION["user_id"])||$_SESSION["user_id"]=="") {
    echo json_encode(array(
        "status"=>"nologin"
    ));
    exit;
}
$quick_buy = isset($_POST["quick_buy"]) ? (int)$_POST["quick_buy"] : 0;
$product_id = isset($_POST["product_id"]) ? (int)$_POST["product_id"] : 0;
$quantity = isset($_POST["quantity"]) ? (int)$_POST["quantity"] : 0;
$color = isset($_POST["color"]) ? htmlspecialchars_decode($_POST["color"]) : "";
$size = isset($_POST["size"]) ? htmlspecialchars_decode($_POST["size"]) : "";
$sku_sn = isset($_POST["sku_sn"]) ? htmlspecialchars_decode($_POST["sku_sn"]) : "";

if(empty($product_id) || empty($quantity) || empty($color) || empty($size) || empty($sku_sn)){
    echo json_encode(array(
        "status"=>"posterror"
    ));
    exit;
}

$table = "cart";
$Condition = "where user_id=".(int)$_SESSION["user_id"]." AND product_id=".$product_id." AND color_prop='".$color."' AND size_prop='".$size."'";
$row = $DB->GetRs($table,"cart_id,quantity",$Condition);
if(empty($row)){
    $quantity_org = 0;
}else{
    $targetid = $row["cart_id"];
    $quantity_org = (int)$row["quantity"];
}
$Condition = "where depot_id=".$SITECONFIGER['sys']['default_depot_id']." AND sku_sn='".$sku_sn."' AND color_prop='".$color."' AND size_prop='".$size."'";
$stock = $DB->GetRs('stock',"*",$Condition);
if($stock['quantity'] < $quantity){
    echo json_encode(array(
        "status"=>"nostock"
    ));
    exit;
}
$formdata = array(
    "product_id"=>$product_id,
    "color_prop"=>$color,
    "size_prop"=>$size,
    "quantity"=>$quantity+$quantity_org,
    "user_id"=>$_SESSION["user_id"],
    "create_date"=>date('Y-m-d H:i:s'),
    "sku_prop"=>isset($stock['sku_prop'])?$stock['sku_prop']:NULL,
    "presale"=>empty($stock['sync'])?1:0,
    "sku_sn"=>isset($stock['sku_sn'])?$stock['sku_sn']:NULL,
);
if($quick_buy){
    $formdata['quantity'] = (int)$_POST["quantity"];
}

$last_insert_id = 0;
if($quantity_org==0){
    $result = $DB->Add($table,$formdata);
    $last_insert_id = $DB->insert_id();
}else{
    $result = $DB->Set($table,$formdata,"where cart_id=".$targetid);
    $last_insert_id = $targetid;
}

echo json_encode(array(
    "status"=>"success",
    "last_insert_id"=>$last_insert_id,
    "quick_buy" => $quick_buy,
));