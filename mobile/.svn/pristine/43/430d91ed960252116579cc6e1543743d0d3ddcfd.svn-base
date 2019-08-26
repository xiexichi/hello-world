<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
//检查登录
if(!isset($_SESSION["user_id"])||empty($_SESSION["user_id"])) {
    echo json_encode(array(
        "ms_code"=>'nologin',
        'ms_msg'=>'你还没有登录，请登录后操作',
    ));
    exit;
}

$share_id = isset($_POST['share_id'])?(int)$_POST['share_id']:0;

$table = 'share';
$row = $DB->GetRs($table,'share_id,user_id','WHERE user_id='.(int)$_SESSION['user_id'].' AND share_id='.$share_id);
if(isset($row['user_id']) && $row['user_id']==$_SESSION['user_id']){
    $DB->Del($table,"","","share_id=".$share_id);
    $DB->Del('vote',"","","vote_type='share' AND user_id=".(int)$_SESSION['user_id']." AND item_id=".$share_id);
    echo json_encode(array(
        "ms_code"=>1,
        'ms_msg'=>'删除成功！',
    ));
}else{
    echo json_encode(array(
        "ms_code"=>0,
        'ms_msg'=>'删除失败，刷新重试。',
    ));
}
exit;
