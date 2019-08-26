<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
if(isset($_SESSION["user_id"])&&!empty($_SESSION["user_id"])) {
    $Table="cart";
    $Fileds = "cart_id";
    $Condition = "where user_id=".(int)$_SESSION["user_id"];

    $row = $DB->GetRs($Table,$Fileds,$Condition);
    if(empty($row)){
        echo json_encode(array(
            "status"=>"empty"
        ));
        exit;
    }else{
        echo json_encode(array(
            "status"=>"success"
        ));
        exit;
    }

}else{
    echo json_encode(array(
        "status"=>"nologin"
    ));
    exit;
}