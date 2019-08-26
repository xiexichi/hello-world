<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

if(!isset($_SESSION["user_id"])) {
    echo json_encode(array(
        "status"=>"illegal"
    ));
    exit;
}

if($_SESSION["user_id"]=="") {
    echo json_encode(array(
        "status"=>"illegal"
    ));
    exit;
}


$row = $DB->GetRs("orders", "*", "WHERE order_id = ".$_GET['order_id']);
if($row) {
    echo json_encode(array(
        "status"=>"success",
        "data" => $row
    ));
}else {
    echo json_encode(array(
        "status"=>"illegal"
    ));
}

