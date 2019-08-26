<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

if(empty($_SESSION["user_id"])) {
    echo json_encode(array(
        "status"=>"nolgoin"
    ));
    exit;
}

$password = isset($_POST["password"]) ? $_POST["password"] : "";
if($password==""){
    echo json_encode(array(
        "status"=>"empty"
    ));
    exit;
}


$Table="users";
$Fileds = "user_id,password,nickname";
$Condition = "where user_id=".$_SESSION["user_id"];
$row = $DB->GetRs($Table,$Fileds,$Condition);
if(empty($row)){
    echo json_encode(array(
        "status"=>"nolgoin"
    ));
    exit;
}else{
    if(md5($password) != $row["password"] && $Base->pass_crypt($password) != $row["password"] ){
        echo json_encode(array(
            "status"=>"nopassword"
        ));
        exit;
    }else{
        echo json_encode(array(
            "status"=>"success"
        ));
        exit;
    }
}
