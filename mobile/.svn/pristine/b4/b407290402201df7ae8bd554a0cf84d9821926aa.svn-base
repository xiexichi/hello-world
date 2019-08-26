<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
if(!isset($_SESSION["user_id"])||$_SESSION["user_id"]=="") {
    exit;
}

$cart_id = isset($_GET["cart_id"]) ? (int)$_GET["cart_id"] : 0;
$quantity = isset($_GET["quantity"]) ? (int)$_GET["quantity"] : 0;


if($quantity==0||$cart_id==0){
    exit;
}

$table = "cart";

$result = $DB->Set($table,array("quantity"=>$quantity),"where cart_id=".$cart_id);

echo json_encode([]);