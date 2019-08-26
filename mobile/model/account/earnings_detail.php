<?php
$page_title = "我的二五";
$page_sed_title = '收益明细';
$module = 'promote'; //模块
$submodule = 'promote_earnings_detail';
$GLOBALS['DB'] = $DB;

$Base->check_permission($is_promote);

if(isset($_SESSION["user_id"])&&!empty($_SESSION["user_id"])) {

    $promote_id = $promote['promote_id'];

    //收益明细
    $sql = "SELECT pe.earnings_type,pe.re_price,pe.commission_rate,pe.earnings,pe.is_get,pe.re_price,pe.product_num,pe.received_time,p.product_name,b.method FROM promote_earnings pe
        LEFT JOIN products p  ON pe.product_id = p.product_id
        LEFT JOIN bag b ON pe.pay_sn = b.pay_sn
        WHERE pe.promote_id = $promote_id
        ORDER BY received_time DESC LIMIT 0,10";
    $result = $DB->query($sql);
    $earnings_detail = array();
    while($row = $DB->fetch_array($result)) {
        array_push($earnings_detail, $row);
    }
    // print_r($earnings_detail);exit();

}

$sm->assign("promote", $promote, true);
$sm->assign("earnings_detail_num", count($earnings_detail), true);
$sm->assign("earnings_detail", $earnings_detail, true);
$sm->assign("is_promote", $is_promote, true);
$sm->assign("page_sed_search", $page_sed_search, true);
$sm->assign("module", $module, true);
$sm->assign("submodule", $submodule, true);
