<?php
$page_title = "我的二五";
$page_sed_title = '我的积分';


//$ssql = ' WHERE `user_id` = '.$data['user_id'].' AND `method` IN (2,3,4) ORDER BY `create_date` DESC';
$integral_total = 0;


if(isset($_SESSION["user_id"])&&!empty($_SESSION["user_id"])) {
    $Condition = "where user_id=" . $_SESSION["user_id"];
    $row = $DB->GetRs("users", "integral_total", $Condition);
    $integral_total = empty($row) ? 0 : $row["integral_total"];

}


$sm->assign("integral_total", $integral_total, true);