<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

//if(isset($_SESSION["user_id"])||$_SESSION["user_id"]!=""){
//    echo json_encode(array(
//        "status"=>"error"
//    ));
//    exit;
//}

if(!isset($_SESSION["user_id"])||$_SESSION["user_id"]==0) {
    echo json_encode(array(
        "status"=>"error"
    ));
    exit;
}

$password = isset($_POST["password"]) ? trim($_POST["password"]) : "";
$type = isset($_POST["type"]) ? trim($_POST["type"]) : "";

if(is_weixin() && $type=='weixin'){
    if(!isset($_SESSION["openid"]) || $_SESSION["openid"]=="") {
        echo json_encode(array(
            "status"=>"error"
        ));
        exit;
    }
}

/*if($password==""){
    echo json_encode(array(
        "status"=>"empty"
    ));
    exit;
}*/



$Table="users";
$Fileds = "user_id,password,nickname";
$Condition = "where user_id=".$_SESSION["user_id"];
$row = $DB->GetRs($Table,$Fileds,$Condition);
// if(empty($row)){
//     echo json_encode(array(
//         "status"=>"nouser"
//     ));
//     exit;
// }else{
    // 解绑不需要验证密码
    if($type=='weixin' || $type=='weapp'){
        $result = $DB->Set($Table,array("openid"=>"","weapp_openid"=>"","unionid"=>""),"where user_id=".$_SESSION["user_id"]);
        $DB->Del("social","","","(type='weixin' or type='weapp') and user_id=".$_SESSION["user_id"]);
    }else if($type!=''){
        $DB->Del("social","","","type='".$type."' and user_id=".$_SESSION["user_id"]);
    }

    session_unset();
    session_destroy();
    echo json_encode(array(
        "status"=>"success",
        "nickname"=>isset($row["nickname"])?$row["nickname"]:''
    ));
// }


