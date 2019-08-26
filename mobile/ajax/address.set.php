<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

if(!isset($_SESSION["user_id"]) || !$_SESSION["user_id"]) {
    echo json_encode(array(
        "status"=>"error"
    ));
    exit;
}

$act = isset($_POST["act"]) ? $_POST["act"] : '';
$address_id= isset($_POST["address_id"]) ? (int)$_POST["address_id"] : 0;
// 设置默认收货地址
if($act == 'default' && $address_id>0){
    $result = $DB->Set("users",array("address_id"=>$address_id),"where user_id=".$_SESSION["user_id"]);
    echo json_encode(array(
        "status"=>"success"
    ));
    exit;
}

$Table="address";
$Fileds = "*";
$Condition = "where user_id='".$_SESSION["user_id"]."'";
$Row = $DB->Get($Table,$Fileds,$Condition,0);
$Row = $DB->result;
$RowCount = $DB->num_rows($Row);

if($RowCount>5){
    echo json_encode(array(
        "status"=>"noquota"
    ));
    exit;
}

$Table="address";
$Fileds = "*";
$Condition = "where user_id='".$_SESSION["user_id"]."'";
$row = $DB->GetRs($Table,$Fileds,$Condition);
if(empty($row)){
    $turn = 1;
}else{
    $turn = 0;
}

$liid = isset($_POST["liid"]) ? (int)$_POST["liid"] : 0;
$state_id = isset($_POST["state_id"]) ? (int)$_POST["state_id"] : 0;
$city_id = isset($_POST["city_id"]) ? (int)$_POST["city_id"] : 0;
$district_id = isset($_POST["district_id"]) ? (int)$_POST["district_id"] : 0;
$address = isset($_POST["address"]) ? $_POST["address"] : "";
$receiver_name = isset($_POST["receiver_name"]) ? $_POST["receiver_name"] : "";
$receiver_phone = isset($_POST["receiver_phone"]) ? $_POST["receiver_phone"] : "";


if($state_id==0||$city_id==0||$address==""||$receiver_name==""||$receiver_phone==""){
    echo json_encode(array(
        "status"=>"empty"
    ));
    exit;
}


$row = $DB->GetRs("area","area_name","where area_id=".$state_id);
$state_name = empty($row) ? "" : $row["area_name"];

$row = $DB->GetRs("area","area_name","where area_id=".$district_id);
$district_name = empty($row) ? "" : $row["area_name"];

$row = $DB->GetRs("area","area_name","where area_id=".$city_id);
$city_name = empty($row) ? "" : $row["area_name"];


$formdata = array(
    "state"=>$state_id,
    "city"=>$city_id,
    "district"=>$district_id,
    "address"=>$address,
    "receiver_name"=>$receiver_name,
    "receiver_phone"=>$receiver_phone,
    "zip"=>"000000",
    "user_id"=>$_SESSION["user_id"],
    "modify_date"=>date('Y-m-d H:i:s'),
    "create_date"=>date('Y-m-d H:i:s')
);
$result = $DB->Set("address",$formdata,"where address_id=".$address_id);


if($turn){
    $result = $DB->Set("users",array("address_id"=>$address_id),"where user_id=".$_SESSION["user_id"]);
}

echo json_encode(array(
    "liid"=>$liid,
    "status"=>"success",
    "state_id"=>$state_id,
    "city_id"=>$city_id,
    "district_id"=>$district_id,
    "state_name"=>$state_name,
    "city_name"=>$city_name,
    "district_name"=>$district_name,
    "full_address"=>$state_name.$city_name.$district_name.$address,
    "address"=>$address,
    "receiver_name"=>$receiver_name,
    "receiver_phone"=>$receiver_phone,
    "address_id"=>$address_id,
    "turn"=>$turn
));