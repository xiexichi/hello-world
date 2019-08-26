<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

if(!isset($_SESSION["user_id"])||empty($_SESSION["user_id"])) {
    echo json_encode(
        array("status"=>"nologin")
    );
    exit;
}

$addresslist = array("status"=>"error","list"=>array());
$Table="address";
$Fileds = "*";
$Condition .= "where user_id=".$_SESSION["user_id"]." Order by modify_date desc";
$Row = $DB->Get($Table,$Fileds,$Condition,$pageSize);
$Row = $DB->result;
$RowCount = $DB->num_rows($Row);


if($RowCount!=0){




    while($result = $DB->fetch_assoc($Row)){

        $row = $DB->GetRs("area","area_name","where area_id=".(int)$result["state"]);
        $state_name = empty($row) ? "" : $row["area_name"];

        $row = $DB->GetRs("area","area_name","where area_id=".(int)$result["district"]);
        $district_name = empty($row) ? "" : $row["area_name"];

        $row = $DB->GetRs("area","area_name","where area_id=".(int)$result["city"]);
        $city_name = empty($row) ? "" : $row["area_name"];

        array_push($addresslist["list"], array(
            "address_id"=>$result["address_id"],
            "district_id"=>$result["district"],
            "city_id"=>$result["city"],
            "state_id"=>$result["state"],
            "address"=>$result["address"],
            "full_address"=>$state_name.$city_name.$district_name.$result["address"],

            "state_name"=>$state_name,
            "city_name"=>$city_name,
            "district_name"=>$district_name,

            "receiver_name"=>$result["receiver_name"],
            "receiver_phone"=>$result["receiver_phone"]
        ));
    }

}

$addresslist["status"] = "success";

echo json_encode($addresslist);


?>