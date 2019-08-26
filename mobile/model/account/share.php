<?php
$page_title = "我的二五";
$page_sed_title = '我的晒图';
$share = array();

if($_SESSION["user_id"] != "" && $_SESSION["user_id"]!=0){

    $Table = "v_share";
    $Fileds = "*";
    $Condition = "where user_id=" . $_SESSION["user_id"]." order by date_added desc";
    $Row = $DB->Get($Table, $Fileds, $Condition, 0);
    $RowCount = $DB->num_rows($Row);
    if ($RowCount != 0) {
        while($result = $DB->fetch_assoc($Row)){
            $result['photos'] = unserialize($result['photos']);
            $result['img_url'] = $Base->site_img($result['photos'][0]);
            $result['userimg'] = $Base->site_img($result['userimg']);
            $share[] = $result;
        }
    }

}

$sm->assign("share", $share, true);
