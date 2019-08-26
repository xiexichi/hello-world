<?php
$page_title = "我的二五";
$page_sed_title = '我的余额';

$total_bag = 0;
$empty_bag = false;

if(isset($_SESSION["user_id"])&&!empty($_SESSION["user_id"])) {
    $Condition = "where user_id=" . $_SESSION["user_id"] . " ORDER BY create_date DESC";
    $Row = $DB->Get("bag", "*", $Condition, 0);
    $RowCount = $DB->num_rows($Row);
    if ($RowCount != 0) {
        $total_bag = $RowCount;
    } else {
        $empty_bag = true;
    }

    $Condition = "where user_id=" . $_SESSION["user_id"];
    $row = $DB->GetRs("users", "bag_total", $Condition);
    $balance = empty($row) ? "0.00" : $row["bag_total"];

    $c = isset($_GET["c"]) ? htmlspecialchars($_GET["c"]) : 'xf';
}

$sm->assign("c", $c, true);
$sm->assign("balance", $balance, true);
$sm->assign("empty_bag", $empty_bag, true);
$sm->assign("total_bag", $total_bag, true);
