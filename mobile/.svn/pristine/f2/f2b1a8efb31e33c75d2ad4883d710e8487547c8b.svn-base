<?php
require "config.php";
// require "cs.php"; // 类似有强制广告，取消试下

	$model = isset($_GET["m"]) ? $_GET["m"]  : "home";
	$action = isset($_GET["a"]) ? $_GET["a"]  : "index";
	$histroy_url = empty($_SERVER['HTTP_REFERER']) ? $_SERVER['REQUEST_URI'] : $_SERVER['HTTP_REFERER'];


/**
 * [updateUnionid 更新用户的unionid]
 * @param  [string] $openid  [微信openid]
 * @param  [int] 	$user_id [用户id]
 */
function updateUnionid($openid,$user_id){
	if(empty($openid))return;
	if(isset($_SESSION['unionid'])){
		$unionid = $_SESSION['unionid'];
	}else{
		require_once "pay/wx/WxPay.Api.php";
		require_once "pay/wx/WxPay.JsApiPay.php";
		$tools = new JsApiPay();//实例微信的jsApi
		$unionid = $tools->getUnionid($openid);//获取用户的unionid

		file_put_contents('./temp/index_unionid.json', $unionid);
	}
	if(!empty($unionid)){
		//新增unionid修改语句
		$result = $DB->Set('users',array("unionid"=>$unionid),"where user_id='".$user_id."'");	
	}
}


//测试
// $_SESSION["user_id"] = 23;
// $_SESSION["openid"] = 'oXNwpuLQZmwmjh7bjWBekV1SCHyw';
// $_SESSION["unionid"] = 'oRazyt55028RC7vqPWRynymrIvi4';

/*
* 微信api
* 只在使用会员功能模型下使用
* 最后更新 2017-01-09
* 原openid自动登录更新为unionid自动登录
*/
if(is_weixin() && in_array($model, array('account','login','cart','order','share','category','hd'))){

	// 2016-06-02 杰：修改第一次打开空白情况
	require_once "pay/wx/WxPay.Api.php";
	require_once "pay/wx/WxPay.JsApiPay.php";
	$tools = new JsApiPay(); //实例微信的jsApi

	if(empty($_SESSION['openid'])){
		// 如果openid为空，获取openid
		$return_url = "https://".$base_url."/index.php?".$_SERVER['QUERY_STRING'];
		$wxUserInfo = $tools->GetOpenid($return_url,NULL);
		$_SESSION["openid"] = isset($wxUserInfo['openid'])?$wxUserInfo['openid']:'';
		$_SESSION["unionid"] = isset($wxUserInfo['unionid'])?$wxUserInfo['unionid']:'';
		$_SESSION["wx_nickname"] = isset($wxUserInfo['nickname'])?$wxUserInfo['nickname']:'';
		$_SESSION["wxUserInfo"] = $wxUserInfo;
	}else{
		if(empty($_SESSION["user_id"])){
			// 未登录，通过unionid自动登录
			$Table="users";
			$Fileds = "user_id,nickname,openid,unionid";
			// $Condition = "where unionid='".$_SESSION["unionid"]."'";
			$Condition = "where openid= '".$_SESSION["openid"]."' and unionid='".$_SESSION["unionid"]."'";
			$row = $DB->GetRs($Table,$Fileds,$Condition);
			if(!empty($row['user_id'])){
				$_SESSION["user_id"]=$row["user_id"];
				$_SESSION["nickname"]=$row["nickname"];
			}
		}else{
			// 已登录，绑定openid和unionid
			$Table="users";
			$Fileds = "user_id,nickname,openid,unionid";
			$Condition = "where user_id='".$_SESSION["user_id"]."'";
			$row = $DB->GetRs($Table,$Fileds,$Condition);
			if(empty($row['openid']) || empty($row['unionid'])){
				$unionid = isset($_SESSION['unionid'])?$_SESSION['unionid']:NULL;
				if(empty($unionid)){
					$unionid = $_SESSION['unionid'] = $tools->getUnionid($_SESSION["openid"]);
				}
				// 更新绑定
				$result = $DB->Set('users',array("openid"=>$_SESSION["openid"],"unionid"=>$unionid),"where user_id='".(int)$row["user_id"]."'");
				if($result){
					$DB->Del("social","","","type='weixin' AND user_id=".$_SESSION["user_id"]);
			        $DB->Add("social",array(
			            "type"=>'weixin',
			            "is_bind"=>1,
			            "social_id"=>$_SESSION["openid"],
			            "bind_date"=>date('Y-m-d H:i:s'),
			            "user_id"=>$_SESSION["user_id"],
			            "social_name"=>isset($_SESSION["wx_nickname"])?$_SESSION["wx_nickname"]:NULL
			        ));
			    }
			}
		}
	}
}


/*------------------------ 识别线下来源记录session ------------------------*/
//注册来源备注
if(!empty($_GET['fr'])) {
    $fr = $_GET['fr'];
    $ch = !empty($_GET['ch'])?$_GET['ch']:'';
    $qrcode = $DB->GetRs("qrcode","id","WHERE code = '{$fr}'");
    if(!empty($qrcode)) {
        $_SESSION['qrcode_fr'] = base64_encode($fr);
        $_SESSION['qrcode_ch'] = base64_encode($ch);
    }
}
// print_r($_SESSION);
/*
* 短信标识 ?sms=code
* 用户数据统计分析
* 记录session后续(v.php)使用
*/
if(!empty($_GET['sms'])){
	$smsRow = $DB->GetRs('sms_ser','sms_code,send_date,gourl',"WHERE sms_code='".trim(htmlspecialchars($_GET['sms']))."'");
	$_SESSION['stats_sms_code'] = isset($smsRow['sms_code'])?$smsRow['sms_code']:NULL;
	$_SESSION['stats_sms_sendtime'] = isset($smsRow['send_date'])?$smsRow['send_date']:NULL;
}


/*------------------------------------- 记录推广信息 -------------------------------*/
$PI = isset($_GET["PI"])?$_GET["PI"]:0;
if(!empty($PI)) {
	//判断推广者
	$pid = $Base->myDecode($PI);
	$promote_temp = $DB->GetRs("promote","*","WHERE promote_id = ".$pid);
	// print_r($promote_temp);
	//存在未被冻结的推广者
	if(!empty($promote_temp) && !$promote_temp['is_frozen']) {
	  $pid = $promote_temp['promote_id'];
	  //记录cookie
	  $default_expire = time()+3600*24*30; //默认有效期
	  $promote_save = $promote_config['promote_save']; //保存天数
	  $promote_expire = empty($promote_save) ? $default_expire : time()+3600*24*$promote_save;
	  if(!isset($_COOKIE['rememberMe'])) {
	    setcookie('rememberMe',base64_encode($pid),$promote_expire,'/','25boy.cn');  
	  }
	}
}



	$uid = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : 0;

	$user = $DB->GetRs('users','user_id,nickname,email,phone,realname',"WHERE user_id=$uid");
	/*$user=array(
		"nickname"=>"",
		"email"=>"",
		"mobile"=>"",
		"sex"=>"",
		"location"=>"",
		"born"=>"",
		"realname"=>"",
		"icon"=>""
	);*/

	//微信
	$openid = isset($_SESSION["openid"]) ? $_SESSION["openid"] : 0;
	$unionid = isset($_SESSION["unionid"]) ? $_SESSION["unionid"] : 0;
	$sm->assign("appId", $signPackage["appId"], true);
	$sm->assign("timestamp", $signPackage["timestamp"], true);
	$sm->assign("nonceStr", $signPackage["nonceStr"], true);
	$sm->assign("signature", $signPackage["signature"], true);
	$sm->assign("access_token", $signPackage["access_token"], true);
	$sm->assign("openid",$openid,true);
	$sm->assign("unionid",$unionid,true);
	$sm->assign("signPackage",$signPackage, true);
	// 全局
	$sm->assign("histroy_url",$histroy_url,true);
	$sm->assign("user",$user,true);
	$sm->assign("session_uid",$uid,true);
	$category = $Common->get_category();
	$sm->assign("category", $category, true);
	$sm->assign("is_weixin",is_weixin(),true);
	// 系统
	$sm->assign("model", $model, true);
	$sm->assign("action", $action, true);

	$sm->assign("account_balance", $account_balance, true);
	$sm->assign("is_seller", $is_seller, true);
	$sm->assign("is_promote", $is_promote, true);

	$includefile = $model_path."/".$model."/".$action.".php";
	if(file_exists($includefile))
		require $includefile;

	// 页面
	// if($model!='home'){
	// 	$page_title = $page_title . ' - 25BOY!二五仔';
	// }
	$sm->assign("page_title", $page_title, true);
	$sm->assign("page_sed_title", $page_sed_title, true);
	$sm->assign("seo_keyword", trim($seo_keyword,','), true);
	$sm->assign("seo_desc", $seo_desc, true);
	$sm->assign("wxconfigarray", empty($wxconfigarray)?'{}':json_encode($wxconfigarray), true);
	$sm->assign("version",$sysinfo['version'],true); //版本号js,css用于更新缓存

	// 全站顶部图片
	$sm->assign("site_top_banner", $Common->get_picshow(12,1), true);

	// 是否显示主导航 APP不显示底部导航条
	if(isset($_GET['isapp']) || isset($_SESSION['isapp'])){
		$_SESSION['isapp'] = true;
		$site_nav_display = 'hide';
		$sm->assign("isapp", true, true);
	}
	$sm->assign("site_nav_display", isset($site_nav_display)?$site_nav_display:'show', true);

	// 检查是否完善资料
	$check_user_info = '';
	if($uid>0 && in_array($model, array('account','cart','order','share')) && $action!='setting'){
		if($Common->check_user_info($uid,array('phone','nickname','flag')) == false){
			$check_user_info = 'unok';
		}
	}
	$sm->assign("check_user_info", $check_user_info, true);

	/*
	live800信任参数
	hashCode = md5(urlencode(userId+name+memo+timestamp+key))
	*/
	/*$timestamp = time();
	$rand = rand(10,99);
	$hashCode = md5(urlencode($rand.$uid.$user['nickname'].$timestamp.'msmmym9lezbynmeg9wkmix6dkx45z8cd'));
	$live800_infoValue = urlencode('userId='.$rand.$uid.'&name='.$user['nickname'].'&memo=&hashCode='.$hashCode.'&timestamp='.$timestamp);
	$sm->assign("live800_infoValue", $live800_infoValue, true);*/


	// 合并css文件
	$cssfiles = 'global.css,page.css,font.css,animate.css,user.pannel.css,20151106.css';
	$head_css_file = '/cache/css/'.md5($cssfiles).'.css';
	$pathfile = $_SERVER['DOCUMENT_ROOT'].$head_css_file;
	if(!is_file($pathfile)){
	    $files = explode(',', $cssfiles);
	    $css_content = '';
	    foreach ($files as $val) {
	        $css_content.= @file_get_contents($_SERVER['DOCUMENT_ROOT'].'/statics/css/'.$val);
	    }
	    @file_put_contents($pathfile, str_replace("\r\n", "\n", $css_content));
	}
	$sm->assign("head_css_file", $head_css_file, true);

	$includetplfile = $view_path."/".$model."/".$action.".tpl";
	if($model == 'home'){
			require $includetplfile;
	}else{
		if(file_exists($includetplfile)){
			$sm->display($model."/".$action.".tpl");
		}else{
			require $model_path."/public/404.php";
		 	$sm->display("public/404.tpl");
		}
	}
