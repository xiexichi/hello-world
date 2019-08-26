<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
if(!isset($_SESSION["user_id"])||$_SESSION["user_id"]=="") {
    echo json_encode(array(
        "status"=>"nologin"
    ));
    exit;
}

$address_id = isset($_GET["address_id"]) ? intval($_GET["address_id"]) : 0;
$currentid = isset($_GET["currentid"]) ? intval($_GET["currentid"]) : '';

if($address_id==0 || $currentid===''){
    echo json_encode(array(
        "status"=>"geterror"
    ));
    exit;
}
$table = "address";
$DB->Del($table,"","","address_id=".$address_id);

echo json_encode(array(
    "status"=>"success",
    "currentid"=>$currentid
));