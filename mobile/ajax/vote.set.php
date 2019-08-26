<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
if(!isset($_SESSION["user_id"])||$_SESSION["user_id"]=="") {
    echo json_encode(array(
        "status"=>"nologin"
    ));
    exit;
}

// 截止时间
/*if(time() >= strtotime("2017-08-24 23:59:59")){
    echo json_encode(array(
        "status"=>"timeout"
    ));
    exit;
}*/

// 微信判断
/*if(is_weixin()){
    if(!isset($_SESSION["openid"])||empty($_SESSION["openid"])) {
        echo json_encode(
            array("status"=>"noweixin")
        );
        exit;
    }
}else{
    echo json_encode(array(
        "status"=>"noweixin"
    ));
    exit;
}*/

$item_id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$vote_type = trim($_GET["type"]) ? $_GET["type"] : '';
if($item_id==0||empty($vote_type)){
    exit;
}

$data = array();
$data['item_id'] = $item_id;
$data['user_id'] = $_SESSION['user_id'];
$data['vote'] = 1;
$data['vote_type'] = $vote_type;
$data['channel'] = 'wap';
$data['openid'] = isset($_SESSION["openid"])?$_SESSION["openid"]:'';
$data['create_date'] = date('Y-m-d H:i:s',time());
$data['ip'] = $Base->clientIp();
$data['user_agent'] = @$_SERVER['HTTP_USER_AGENT'];

$Table = 'vote';

/*投票前，判断手机是否已经过验证*/
$row = $DB->Get('users','user_id',"WHERE user_id=".$data['user_id']." AND flag = 1");
$row = $DB->result;
$num = $DB->num_rows($row);
if(!$num){
    echo json_encode(
        array("status"=>"phone_unvalidate")
    );
    exit;
}

// 一个IP最多3票
$ipRow = $DB->Get($Table,'vote_id',"WHERE item_id=".$item_id." AND vote_type='share' AND ip='".$data['ip']."' LIMIT 8");
$ipRow = $DB->result;
$ipRowCount = $DB->num_rows($ipRow);
if($ipRowCount >= 5){
    echo json_encode(
        array("status"=>"iplimit")
    );
    exit;
}

// 查询是否已经投票
$vote = $DB->GetRs($Table,'vote_id,create_date',"WHERE item_id=".$item_id." AND vote_type='share' AND user_id='".$_SESSION['user_id']."' ORDER BY create_date DESC");
if(isset($vote['vote_id'])){
    if($SITECONFIGER['vote']['spac_time'] < 0 ){
        // 只能投一票
        echo json_encode(
            array("status"=>"voteed")
        );
        exit;
    }else{
         // 规定时间内只能投一票
        $time = strtotime($vote['create_date'])+$SITECONFIGER['vote']['spac_time']*60;
        if($time >= time()){
            echo json_encode(
                array("status"=>"voteed")
            );
            exit;
        }
    }
}

// 添加投票
$result = $DB->Add($Table,$data);
if($result){

	$Fileds = "*";
	$Condition .= "where item_id=".$item_id;
	$Row = $DB->Get($Table,$Fileds,$Condition);
	$Row = $DB->result;
	$RowCount = $DB->num_rows($Row);

	echo json_encode(array(
        "status"=>"success",
        'count'=>(int)$RowCount,
    ));
    exit;
}
