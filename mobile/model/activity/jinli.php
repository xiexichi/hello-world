<?php


// SELECT * FROM `lottery_code` where activity='锦鲤' and result = 1;

// 查找锦鲤中奖码
$Table="lottery_code";
$Fileds = "*";
$Condition = "where activity='锦鲤' and result = 1";

$data = $DB->GetRs($Table,$Fileds,$Condition);

// print_r($data);
// exit;

// 设置默认code，避免出错
$code = '9758321935';

if ($data) {
	$code = $data['code'];
}

// 设置视图模板数据
$sm->assign("code", $code, true);