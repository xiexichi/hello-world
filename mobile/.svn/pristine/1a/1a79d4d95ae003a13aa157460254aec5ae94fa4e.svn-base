<?php
$page_title = "我的二五";
$page_sed_title = '关注的产品';
$favorite = array();

if($_SESSION["user_id"]!=""&&$_SESSION["user_id"]!=0){

    $Table = "v_favorites";
    $Fileds = "*";
    $Condition = "where user_id=" . $_SESSION["user_id"]." order by create_date desc";
    $Row = $DB->Get($Table, $Fileds, $Condition, 0);
    $RowCount = $DB->num_rows($Row);
    if ($RowCount != 0) {

        while($result = $DB->fetch_assoc($Row)){

            array_push($favorite,array(
                "product_id"=>$result["product_id"],
                "product_name"=>$result["product_name"],
                "product_image"=>$result["url"],
                "product_price"=>$result["product_price"],
                "market_price"=>$result["market_price"],
                "ship_free"=>$result["ship_free"],
                "total_quantity"=>$result["total_quantity"],
                "stock"=>$result["stock"],
                "create_time"=>$Base->FormatTime($result["create_date"],"","ai")
            ));
        }
    }

}

$sm->assign("favorite", $favorite, true);