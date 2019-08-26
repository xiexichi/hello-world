<?php
/*
* 交通银行申请信用卡引导页
* 2018-08-10
*/
if(!is_weixin()){
	echo '<h1>请在微信内访问</h1>';
	exit;
}

// 临时关闭，不要往下执行了，直接设置到办卡页面
header("location: https://creditcardapp.bankcomm.com/applynew/front/apply/mgm/account/wechatEntry.html?recomId=13044776&saleCode=371000100&entryRecomId=&trackCode=A090420182885&source=25boy");
exit();

$do = isset($_POST['do']) ? htmlspecialchars_decode($_POST['do']) : null;
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : '';
$_SESSION['qrcode_fr'] = base64_encode('business');
$_SESSION['qrcode_ch'] = base64_encode('bankcomm');
$business_code = 'bankcomm';

// 登录后回调，插入bag_combine记录
if($do == 'callback'){
	if(empty($user_id)){
		die(json_encode([
			'status' => 'nologin',
			'msg' => '未登录'
		]));
	}

	$combine = $DB->GetRs('bag_combine','count(*) as count',"WHERE business_code='{$business_code}' AND user_id={$user_id}");
    if(isset($combine['count']) && $combine['count'] == 0){
        $combineData = array(
            'user_id' => $user_id,
            'business_code' => $business_code,
            'status' => 0
        );
        $DB->Add('bag_combine', $combineData);
        if($DB->insert_id()){
        	die(json_encode([
				'status' => 'success',
				'msg' => '操作成功'
			]));
        }else{
        	die(json_encode([
				'status' => 'error',
				'msg' => '操作失败，请刷新页面重试'
			]));
        }
    }else{
    	die(json_encode([
			'status' => 'success',
			'msg' => '操作成功'
		]));
    }
	exit;
}


// 页面内容
if($user_id){
	$user = $DB->GetRs('users','phone,nickname',"WHERE user_id={$user_id}");
}

// 不显示导航栏
$site_nav_display = false;

// 页面seo
$page_title = '25boy潮牌联合交通银行校园卡';
$page_sed_title = '25BOY联合交通银行';
$sm->assign("user_id", $user_id, true);
$sm->assign("user", $user, true);