<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$had_account= isset($_POST["had_account"]) ? intval($_POST["had_account"]) : 0;
$account = isset($_POST["account"]) ? trim($_POST["account"]) : NULL;
$phone = isset($_POST["phone"]) ? trim($_POST["phone"]) : NULL;
$phone_code = isset($_POST["phone_code"]) ? trim($_POST["phone_code"]) : NULL;
$nickname = isset($_POST["nickname"]) ? trim($_POST["nickname"]) : NULL;
$type = isset($_POST["type"]) ? trim($_POST["type"]) : "qq";
$ssid = ($type == 'qq') ? 31 : 33;
$password = isset($_POST["password"]) ? trim($_POST["password"]) : "";
$avatar_hd= isset($_SESSION["avatar_hd"]) ? trim($_SESSION["avatar_hd"]) : NULL;
$social_id= isset($_SESSION["social_id"]) ? trim($_SESSION["social_id"]) : NULL;
$social_name= isset($_SESSION["social_name"]) ? trim($_SESSION["social_name"]) : NULL;

$errormsg = array(
    "icon" => "error",
    "title" => "操作失败",
    "remark" => "请刷新页面重试！"
);

if($type=='weixin'){
	$social_id = $_SESSION['openid'];
}
if(empty($password)) {
    $errormsg['remark'] = '请输入密码！';
    echo json_encode($errormsg);
    exit;
}
if($social_id=="") {
    $errormsg['remark'] = '请刷新页面重试！';
    echo json_encode($errormsg);
    exit;
}

if(($had_account==0 && empty($nickname) && $phone_code == "")){
    $errormsg['remark'] = '没有输入数据或不完整，请检查输入！';
    echo json_encode($errormsg);
    exit;
}

if($had_account==0){
    if(!$Base->is_phone_number($phone)){
        $errormsg['title'] = '手机号码无效';
        $errormsg['remark'] = '请输入有效的手机号码';
        echo json_encode($errormsg);
        exit;
    }


    $now_time = time();
    $expire_time = 10 * 60;
    $session_phone_code = 'phone_code_'.$ssid;
    $session_apply_time = 'apply_time_'.$ssid;
    $session_phone = 'phone_'.$ssid;

    //判断验证码是否正确及，是否过了有效期
    if(empty($_SESSION[$session_phone_code])||$phone_code!=$_SESSION[$session_phone_code]||
        ($now_time - $_SESSION[$session_apply_time] > $expire_time)){

        $errormsg['title'] = '验证码无效';
        $errormsg['remark'] = '验证码有误或者已过了有效期';
        echo json_encode($errormsg);
        exit;
    }

    //最后要验证是否更改了手机号码
    if($phone!=$_SESSION[$session_phone]){
        $errormsg['title'] = '验证码无效';
        $errormsg['remark'] = '请使用你申请验证码的手机号';
        echo json_encode($errormsg);
        exit;
    }


    if(!$Base->strCheck($nickname)){
        $errormsg['title'] = '昵称不符合';
        $errormsg['remark'] = '昵称不能包含特殊符号，只允许中文英文数字划线！';
        echo json_encode($errormsg);
        exit;
    }
    if(in_array(strtolower($nickname), $SITECONFIGER["deny_nickname"])){
        $errormsg['title'] = '昵称禁止';
        $errormsg['remark'] = '此昵称不允许注册，换一个吧亲！';
        echo json_encode($errormsg);
        exit;
    }
    if($Base->strLenW2($nickname)>16 || $Base->strLenW2($nickname)<4){
        $errormsg['title'] = '昵称长度不符合';
        $errormsg['remark'] = '昵称长度不符合4-16字符.';
        echo json_encode($errormsg);
        exit;
    }
    if($Base->strLenW2($password)<6){
        $errormsg['title'] = '密码长度不符合';
        $errormsg['remark'] = '密码长度不能少于6位.';
        echo json_encode($errormsg);
        exit;
    }


    $Table="users";
    $Fileds = "nickname";
    $Condition = "where nickname='".$nickname."'";
    $Row = $DB->GetRs($Table,$Fileds,$Condition);
    if(!empty($Row)){
        $errormsg['title'] = '昵称已占用';
        $errormsg['remark'] = '昵称已被注册了，换一个吧亲！';
        echo json_encode($errormsg);
        exit;
    }

    if($type=='weixin'){
    	$Table="users";
	    $Fileds = "openid";
	    $Condition = "where openid='".$social_id."'";
	    $Row = $DB->GetRs($Table,$Fileds,$Condition);
	    if(!empty($Row)){
            $errormsg['title'] = '重复绑定';
            $errormsg['remark'] = '该微信帐号已绑定过二五用户！！';
            echo json_encode($errormsg);
            exit;
	    }
    }else{
    	$Table="social";
	    $Fileds = "social_id";
	    $Condition = "where is_bind=1 and type='".$type."' and social_id='".$social_id."'";
	    $Row = $DB->GetRs($Table,$Fileds,$Condition);
	    if(!empty($Row)){
            $errormsg['title'] = '重复绑定';
            $errormsg['remark'] = '该'.$type.'帐号已绑定过二五用户！';
            echo json_encode($errormsg);
            exit;
	    }
    }

    $Table="users";
    $Fileds = "phone,email";
    $Row = $DB->GetRs($Table,$Fileds,"where phone='".$phone."'");
    if(!empty($Row)){
        $errormsg['title'] = '手机号码已占用';
        $errormsg['remark'] = '手机号码已被注册了，无法登录请找回密码。';
        echo json_encode($errormsg);
        exit;
    }
    $formdata = array(
        "phone"=>$phone,
        "nickname"=>$nickname,
        "password"=>$Base->pass_crypt($password),
        "flag" => 1,
        "image_url"=>$avatar_hd,
        "login_date"=>date('Y-m-d H:i:s'),
        "create_date"=>date('Y-m-d H:i:s'),
        "openid"=>isset($_SESSION['openid'])?$_SESSION['openid']:NULL,
        'regip'=>$Base->clientIp(),
        "remark"=>$Common->getQrcodeFrom(),//注册来源备注,
        "business_code"=>$Common->getBusinessCodeFrom(),//注册来源记录,
    );
    //判断电脑是否存有推广者信息，如果有，就记录到新用户
    if(isset($_COOKIE['rememberMe'])) {
        $formdata['pid'] = base64_decode($_COOKIE['rememberMe']); 
    }

    $DB->Add("users",$formdata);
    $user_id = $DB->insert_id();
    $DB->Add("social",array(
        "type"=>$type,
        "is_bind"=>1,
        "social_id"=>$social_id,
        "bind_date"=>date('Y-m-d H:i:s'),
        "user_id"=>$user_id,
        "social_name"=>$social_name
    ));
    $_SESSION["login_time"] = date('Y-m-d H:i:s');
    $_SESSION["user_id"]=$user_id;
    $_SESSION["nickname"] = $nickname;

    //发放注册送20元代金券
    if(REG_COUPON > 0) {
        $coupon = $Common->used_coupon(REG_COUPON,$user_id);
    }

    echo json_encode(array(
        'nickname' => $nickname,
        "had_account"=>0,
        "status"=>"success",
        "coupon"=>$coupon,
    ));
    exit;

}else{

    // 检查帐户名
    if(empty($account)){
        $errormsg['title'] = '帐户名不能为空';
        $errormsg['remark'] = '请输入帐户名，手机号码或者E-mail';
        echo json_encode($errormsg);
        exit;
    }else{
        if(strstr($account,'@') === false){
            if($Base->is_phone_number($account) == false){
                $errormsg['title'] = '登录帐号不正确';
                $errormsg['remark'] = '请使用正确的手机或者邮箱登录';
                echo json_encode($errormsg);
                exit;
            }else{
                $data['phone'] = $account;
            }
        }else{
            if(!$Base->is_email($account)){
                $errormsg['title'] = '登录帐号不正确';
                $errormsg['remark'] = '请使用正确的手机或者邮箱登录';
                echo json_encode($errormsg);
                exit;
            }else{
                $data['email'] = $account;
            }
        }
    }


    $Table="users";
    $Fileds = "user_id,password,nickname,openid";
    if(isset($data['phone'])){
        $account_condition = "where phone='".$account."'";
    }else{
        $account_condition = "where email='".$account."'";
    }
    //这个是待绑定登录的用户
    $row = $DB->GetRs($Table,$Fileds,$account_condition);
    if(empty($row['user_id'])){
        $errormsg['title'] = '帐户不存在';
        $errormsg['remark'] = '帐户不存在！';
        echo json_encode($errormsg);
        exit;
    }
    if(md5($password)!=$row["password"] && ($Base->pass_crypt($password) != $row['password'])){
            $errormsg['title'] = '密码错误';
            $errormsg['remark'] = '密码错误';
            echo json_encode($errormsg);
            exit;
    }
    if($type == 'qq' || $type == 'weibo') {
            $Table="social";
            $Fileds = "social_id,user_id";
            $Condition = "where is_bind=1 and type='".$type."' and social_id='".$social_id."'";
            $Row = $DB->GetRs($Table,$Fileds,$Condition);
            if(!empty($Row)){
                $errormsg['title'] = '重复绑定';
                $errormsg['remark'] = '该'.$type.'帐号已绑定过二五用户！';
                echo json_encode($errormsg);
                exit;
            }
            $Condition = "where is_bind=1 and type='".$type."' and user_id='".$row['user_id']."'";
            $user = $DB->GetRs($Table,'social_name',$Condition);
            if(!empty($user)){
                $errormsg['remark'] = '该帐户已绑定了'.$type.'：'.$user['social_name'];
                $errormsg['time'] = 2;
                echo json_encode($errormsg);
                exit;
            }

    }

    if($type == 'weixin') {
        if($social_id==$row["openid"]) {
            $errormsg['title'] = '重复绑定';
            $errormsg['remark'] = '该微信帐号已绑定过二五用户！';
            echo json_encode($errormsg);
            exit;
        }

        $Condition = "where is_bind=1 and type='".$type."' and user_id='".$row['user_id']."'";
        $user = $DB->GetRs('social','social_name',$Condition);
        if(!empty($user)){
            $errormsg['remark'] = '该帐户已绑定了微信：'.$user['social_name'];
            $errormsg['time'] = 2;
            echo json_encode($errormsg);
            exit;
        } 

    }

    //微信绑定需要更改两个数据，一个是user.openid 一个是social.social_id
    if($type == 'weixin' && !empty($_SESSION["openid"])){
        $result = $DB->Set('users',array("openid"=>$_SESSION["openid"]),"where user_id='".$row['user_id']."'");
    }

    $DB->Del("social","","","social_id='".$social_id."'");
    $DB->Add("social",array(
        "type"=>$type,
        "is_bind"=>1,
        "social_id"=>$social_id,
        "bind_date"=>date('Y-m-d H:i:s'),
        "user_id"=>$row["user_id"],
        "social_name"=>$social_name
    ));

    $_SESSION["user_id"] = $row["user_id"];
    $_SESSION["nickname"] = $row["nickname"];
    $_SESSION["login_time"] = date('Y-m-d H:i:s');
    echo json_encode(array(
        "had_account"=>1,
        "status"=>"success",
        "nickname"=>$row["nickname"]
    ));
    exit;
 

}



