<?php
$page_title = "我的二五";
$page_sed_title = '提现方式';
$module = 'promote'; //模块
$submodule = 'promote_withdrawal';

$Base->check_permission($is_promote);

$promote_id = $promote['promote_id'];

$sql = "SELECT * FROM promote_withdrawal WHERE promote_id = $promote_id";
$result = $DB->query($sql);

$promote_withdrawal = array();
while ($row = $DB->fetch_array($result)) {
    $row['withdrawal_type_cn'] = $row['withdrawal_type'] == 'alipay' ? '支付宝' : $row['withdrawal_type'];
    array_push($promote_withdrawal, $row);
}

$sm->assign("promote", $promote, true);
$sm->assign("is_promote", $is_promote, true);
$sm->assign("promote_withdrawal", $promote_withdrawal, true);
$sm->assign("page_sed_search", $page_sed_search, true);
$sm->assign("module", $module, true);
$sm->assign("submodule", $submodule, true);


