<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

//if(isset($_SESSION["user_id"])||$_SESSION["user_id"]!=""){
//    echo json_encode(array(
//        "status"=>"error"
//    ));
//    exit;
//}



if(!isset($_SESSION["openid"])) {
    echo json_encode(array(
        "status"=>"error"
    ));
    exit;
}

if($_SESSION["openid"]=="") {
    echo json_encode(array(
        "status"=>"error"
    ));
    exit;
}


$email = isset($_POST["email"]) ? $_POST["email"] : "";

$password = isset($_POST["password"]) ? md5($_POST["password"]) : "";

if($email==""||$password==""){
    echo json_encode(array(
        "status"=>"empty"
    ));
    exit;
}

$Table="users";
$Fileds = "email";
$Condition = "where openid='".$_SESSION["openid"]."'";
$Row = $DB->Get($Table,$Fileds,$Condition,0);
$Row = $DB->result;
$RowCount = $DB->num_rows($Row);
if($RowCount!=0){
    while($result = $DB->fetch_assoc($Row)) {
        if($result["email"]!=$email){
            echo json_encode(array(
                "status"=>"openid",
                "email"=>$result["email"]
            ));
            exit;
        }
    }
}




$Table="users";
$Fileds = "user_id,password,nickname";
$Condition = "where email='".$email."'";
$row = $DB->GetRs($Table,$Fileds,$Condition);
if(empty($row)){
    echo json_encode(array(
        "status"=>"nouser"
    ));
    exit;
}else{
    if($password!=$row["password"]){
        echo json_encode(array(
            "status"=>"nopassword"
        ));
        exit;
    }else{
        $result = $DB->Set($Table,array("openid"=>$_SESSION["openid"]),"where email='".$email."'");

        $_SESSION["user_id"] = $row["user_id"];
        $_SESSION["nickname"] = $row["nickname"];
        echo json_encode(array(
            "status"=>"success",
            "nickname"=>$row["nickname"]
        ));
        exit;
    }
}


