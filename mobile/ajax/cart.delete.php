<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
if(!isset($_SESSION["user_id"])||$_SESSION["user_id"]=="") {
    echo json_encode(array(
        "status"=>"nologin"
    ));
    exit;
}

$id = isset($_GET["id"]) ? trim($_GET["id"],',') : "";
$liid = isset($_GET["liid"]) ? trim($_GET["liid"],',') : "";


if($id==""||$liid==""){
    echo json_encode(array(
        "status"=>"geterror"
    ));
    exit;
}

$table = "cart";

$DB->Del($table,"","","cart_id in(".$id.")");


echo json_encode(array(
    "status"=>"success",
    "liid"=>$id
));