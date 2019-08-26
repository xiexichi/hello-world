<?php
$page_title = "一次分享，终身收益";
$page_sed_title = '分享教程';


  //判断推广者
$promote = $promote_link = '';

if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] != 0) {
    $promote = $DB->GetRs("promote","*","WHERE user_id = ".$_SESSION["user_id"]);

    if(!empty($promote)) {
        $promote_link = $Base->getPromoteLink(PROMOTE_HTTP,$promote['promote_id'],'redpack');
    }else {
        $promote = '';
    }
}


$sm->assign("user_id", $_SESSION["user_id"], true);
$sm->assign("promote", $promote, true);
$sm->assign("promote_link", $promote_link, true);
