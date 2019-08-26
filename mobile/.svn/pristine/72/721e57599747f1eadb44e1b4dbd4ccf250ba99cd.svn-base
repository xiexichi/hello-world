<?php
$page_title = "我的二五";
$page_sed_title = '浏览历史';
$history = array();

if($_SESSION["user_id"]!=""&&$_SESSION["user_id"]!=0){

    $Table = "browse_history";
    $Fileds = "*";
    $Condition = "where user_id=" . $_SESSION["user_id"]." order by create_time desc";
    $Row = $DB->Get($Table, $Fileds, $Condition, 0);
    $RowCount = $DB->num_rows($Row);
    if ($RowCount != 0) {

        while($result = $DB->fetch_assoc($Row)){

            array_push($history,array(
                "product_id"=>$result["product_id"],
                "product_name"=>$result["product_name"],
                "product_image"=>$result["product_image"],
                "product_price"=>$result["product_price"],
                "click"=>$result["click"],
                "create_time"=>$Base->FormatTime($result["create_time"],"","ai")
            ));
        }
    }

}

$sm->assign("history", $history, true);