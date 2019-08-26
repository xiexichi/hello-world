<?php
$total_cart = 0;
$emptycart = false;
$user_id = isset($_SESSION["user_id"]) ? intval($_SESSION["user_id"]) : 0;

// 判断登录
if(isset($user_id)&&!empty($user_id)) {
    $Table = "cart";
    $Fileds = "cart_id";
    $Condition = "where user_id=" . $user_id;
    $Row = $DB->Get($Table, $Fileds, $Condition, 0);
    $RowCount = $DB->num_rows($Row);
    if ($RowCount != 0) {
        $total_cart = $RowCount;
    } else {
        $emptycart = true;
    }
}


$page_title = "购物车";
$page_sed_title = '购物车 (<strong>'.$total_cart.'</strong>)';

$sm->assign("n", time(), true);
$sm->assign("emptycart", $emptycart, true);
$sm->assign("total_cart", $total_cart, true);

// 隐藏底部导航栏
$site_nav_display = 'hide';