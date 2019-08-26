<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

//if(isset($_SESSION["user_id"])||$_SESSION["user_id"]!=""){
//    echo json_encode(array(
//        "status"=>"error"
//    ));
//    exit;
//}
if(!isset($_SESSION["user_id"])) {
    echo json_encode(array(
        "status"=>"error"
    ));
    exit;
}

if($_SESSION["user_id"]==""||$_SESSION["user_id"]==0) {
    echo json_encode(array(
        "status"=>"error"
    ));
    exit;
}


if(is_weixin()){
    if(!isset($_SESSION["openid"]) || $_SESSION["openid"]=="") {
        echo json_encode(array(
            "status"=>"error"
        ));
        exit;
    }
}


$repassword = isset($_POST["repassword"]) ? trim($_POST["repassword"]) : "";
$oldpassword = isset($_POST["oldpassword"]) ? trim($_POST["oldpassword"]) : "";
$password = isset($_POST["password"]) ? trim($_POST["password"]) : "";


if(empty($password) || empty($repassword)){
    echo json_encode(array(
        "status"=>"empty"
    ));
    exit;
}

if($password!=$repassword){
    echo json_encode(array(
        "status"=>"nosame"
    ));
    exit;
}

$Table="users";
$Fileds = "user_id,password,nickname";
$Condition = "where user_id=".$_SESSION["user_id"];
$row = $DB->GetRs($Table,$Fileds,$Condition);
if(empty($row)){
    echo json_encode(array(
        "status"=>"nouser"
    ));
    exit;
}else{
    // if($oldpassword!=$row["password"]){
    //     echo json_encode(array(
    //         "status"=>"nopassword"
    //     ));
    //     exit;
    // }else{
        $result = $DB->Set($Table,array("password"=>md5($password)),"where user_id=".$_SESSION["user_id"]);

        $_SESSION["user_id"] = 0;
        $_SESSION["nickname"] = "";
        echo json_encode(array(
            "status"=>"success",
            "nickname"=>$row["nickname"]
        ));
        exit;
    // }
}


