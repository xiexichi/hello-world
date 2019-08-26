<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

if(!isset($_SESSION["user_id"])) {
    echo json_encode(array(
        "status"=>"nologin"
    ));
    exit;
}

if($_SESSION["user_id"]=="") {
    echo json_encode(array(
        "status"=>"nologin"
    ));
    exit;
}

$pageSize = $_GET["pagesize"];
$pagecurrent =  $_GET["page"];

$integral_list = array("status"=>"success","list"=>array());
//$ssql = ' WHERE `user_id` = '.$data['user_id'].' AND `method` IN (2,3,4) ORDER BY `create_date` DESC';
if($_SESSION["user_id"]!=""&&$_SESSION["user_id"]!=0){


    $Condition = "where user_id=" . $_SESSION["user_id"] . " ORDER BY create_date DESC";



    $Row = $DB->getPage("integral", "*", $Condition, $pageSize);
    $Row = $DB->result;
    $RowCount = $DB->num_rows($Row);
    $pageAll = $DB->pageAll;
    if ($RowCount != 0) {
        while($result = $DB->fetch_assoc($Row)){
            array_push($integral_list["list"], array(
                "integral_id"=>$result["integral_id"],
                "integral_value"=>$result["integral_value"],
                "context"=>$result["context"],
                "create_date"=>$result["create_date"]
            ));

        }
    }
}
if($pagecurrent>$pageAll){
    echo json_encode(array("status"=>"nomore"));
}else{
    echo json_encode($integral_list);
}