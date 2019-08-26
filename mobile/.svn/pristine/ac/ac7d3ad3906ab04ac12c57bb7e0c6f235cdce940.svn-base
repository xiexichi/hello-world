<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
if(!isset($_SESSION["user_id"])||$_SESSION["user_id"]=="") {
    echo 'nologin';exit();
}

$user_id = isset($_SESSION["user_id"]) ? (int)$_SESSION["user_id"] : 0;
$product_id = isset($_GET["id"]) ? $_GET["id"] : 0;
$favorite = isset($_GET["favorite"]) ? (int)$_GET["favorite"] : 0;



if($user_id==0||$product_id==0){
    exit;
}

$table = "favorites";

if($favorite==1){
    $result = $DB->Add($table,array(
        "user_id"=>$user_id,
        "product_id"=>$product_id,
        "create_date"=>date('Y-m-d H:i:s')
    ));
    echo "1";
}else{
    $result = $DB->Del($table,"","","user_id=".$user_id." and product_id=".$product_id);
    echo "0";
}
