<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$share_id = isset($_GET['id'])?intval($_GET['id']):0;
$type = isset($_GET['type'])?htmlspecialchars($_GET['type']):NULL;
$activity = '新品卫衣';

//检查登录
if(!isset($_SESSION["user_id"])||empty($_SESSION["user_id"])) {
    echo json_encode(array(
        "ms_code"=>'nologin',
        'ms_msg'=>'分享成功，请登录后再次分享获取抽奖码！',
    ));
    exit;
}

// 活动时间，每期需要手动设置
$start_date = "2016-09-20";
$end_date = "2016-09-29";
$type = 'lottery';   // 晒图活动时请设置为空。

if(!is_weixin() && $type=='lottery'){
    echo json_encode(array(
        "ms_code"=>'noweixin',
        'ms_msg'=>'请在微信内分享获取',
    ));
    exit;
}

// 判断是否自己的晒图
$share = $DB->GetRs('share','share_id','WHERE user_id='.(int)$_SESSION['user_id'].' AND share_id='.(int)$share_id);
if((!empty($share['share_id']) || $type=='lottery') && strtotime($start_date)<=time() && strtotime($end_date)+86400>=time()){
    $exist = $Common->exist_user_lottery_code($_SESSION['user_id'], $activity);
    if(!$exist){
        // 生成唯一抽奖码，数据库已有将重新生成
        $code = $Common->get_lottery_code($activity,4);
        $formdata = array(
            "code"=>$code,
            "user_id"=>$_SESSION["user_id"],
            'activity'=>$activity,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'add_date'=>date('Y-m-d H:i:s',time()),
        );
        $code_id = $Common->save_lottery_code($formdata);
        if($code_id){
            $resmsg = array(
                "ms_code"=>'success',
                'ms_msg' => '<p>恭喜您，获得'.$activity.'抽奖机会！</p><p style="font-size:1.6em;padding:5px 0;color:orange;">抽奖码：'.$code.'</p><p style="font-size:12px;">(已保存至-我的卡券)</p>',
            );
            if($type != 'lottery'){
                $resmsg['ms_msg'] .= "<p>分享给朋友一起参与抽奖吧！</p>";
            }
            echo json_encode($resmsg);
            exit;
        }
    }else{
        echo json_encode(array(
            "ms_code"=>'repeat',
            'ms_msg'=>'分享成功，感谢您的支持！',
        ));
        exit;
    }
}else{
    echo json_encode(array(
        "ms_code"=>'nome',
        'ms_msg'=>'分享成功，感谢您的支持！',
    ));
    exit;
}