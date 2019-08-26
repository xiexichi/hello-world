<?php
/*
* 获取抽奖码
* @ param $activity string 当前活动
* @ return 返回当前活动唯一码
*/

// 未启用
return false;

// 当前抽奖活动设置
$activity = isset($_GET['activity']) ? htmlspecialchars($_GET['activity']) : '';
$start_date = '2016-07-29';
$end_date = '2016-08-10';


include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
$user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : 0;
if($user_id > 0) {

    $return = array(
        'msg' => '获取失败，请重试！ 如果多次失败请联系在线客服。',
        'code' => 'error'
    );

    if(empty($user_id)){
        $return['msg'] = '请登录后操作！';
        $return['code'] = 'nologin';
    }else{
        $exist = $Common->exist_user_lottery_code($user_id, $activity);
        if($exist){
            $return['msg'] = '您已经拥有抽奖码，请不要重复获取。';
            $return['code'] = 'exist';
        }else{
            $code = $Common->get_lottery_code($activity);
            $data = array(
                'user_id' => $user_id,
                'code' => $code,
                'activity' => $activity,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'add_date' => date('Y-m-d H:i:s',time())
            );
            $res = $Common->save_lottery_code($data);
            if($res){
                $return['msg'] = $code;
                $return['code'] = 'success';
            }
        }
    }

    echo json_encode($return);
}
