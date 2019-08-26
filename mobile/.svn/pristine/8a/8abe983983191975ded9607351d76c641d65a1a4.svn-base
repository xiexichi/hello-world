<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
//if(isset($_SESSION["user_id"])||$_SESSION["user_id"]!=""){
//    echo json_encode(array(
//        "status"=>"error"
//    ));
//    exit;
//}
$phone = isset($_POST["phone"]) ? trim($_POST["phone"]) : "";
$phone_code = isset($_POST["phone_code"]) ? trim($_POST["phone_code"]) : "";
$nickname = isset($_POST["nickname"]) ? trim($_POST["nickname"]) : "";
$password = isset($_POST["password"]) ? trim($_POST["password"]) : "";
$openid = isset($_POST["openid"]) ? trim($_POST["openid"]) : $_SESSION['openid'];

/*if(is_weixin()){
    if(!isset($openid) || $openid=="") {
        echo json_encode(array(
            "status"=>"error",
            "msg"=>"出错了，请刷新重试"
        ));
        exit;
    }
}*/

if($phone==""|| $phone_code==""||$password==""){
    echo json_encode(array(
        "status"=>"empty",
        "msg"=>'没有输入数据或不完整'
    ));
    exit;
}
if(empty($nickname)){
    $nickname = get_auto_nickname();
}

$now_time = time();
$expire_time = 10 * 60;

//判断验证码是否正确及，是否过了有效期
if(empty($_SESSION['phone_code_1'])||$phone_code!=$_SESSION['phone_code_1']||
    ($now_time - $_SESSION['apply_time_1'] > $expire_time)){
    echo json_encode(array(
        "status"=>"phone_code_x",
        "msg" => '短信验证码错误或已失效'
    ));
    exit;
}

if(!$Base->strCheck($nickname)){
    echo json_encode(array(
        "status"=>"dis_str",
        "msg"=>'昵称不能包含特殊符号，只允许中文英文数字划线'
    ));
    exit;
}
if($Base->strLenW2($nickname)>16 || $Base->strLenW2($nickname)<4){
    echo json_encode(array(
        "status"=>"len_nickname",
        'msg'=>'昵称长度不可少于4位，不可大于16位'
    ));
    exit;
}
if(in_array(strtolower($nickname), $SITECONFIGER["deny_nickname"])){
    echo json_encode(array(
        "status"=>"deny_nickname",
        'msg'=>'此昵称不允许注册'
    ));
    exit;
}
if($Base->strLenW2($password)<6){
    echo json_encode(array(
        "status"=>"len_password",
        "msg"=>'密码长度不能少于6位'
    ));
    exit;
}


$Table="users";
$Fileds = "user_id";

$row = $DB->GetRs($Table,$Fileds,"where nickname='".$nickname."'");
if(!empty($row)){
    echo json_encode(array(
        "status"=>"has_nickname",
        "msg"=>'此昵称已被注册'
    ));
    exit;
}
//最后要验证是否更改了手机号码
if($phone!=$_SESSION['phone_1']){
    echo json_encode(array(
        "status"=>"phone_x",
        "msg"=>'使用新号码，请重新获取验证码'
    ));
    exit;
}

$formdata = array(
    "phone"=>$phone,
    "nickname"=>$nickname,
    "password"=>$Base->pass_crypt($password),
    "openid"=>isset($openid)?$openid:NULL,
    "flag" => 1,
    "login_date"=>date('Y-m-d H:i:s'),
    "create_date"=>date('Y-m-d H:i:s'),
    'regip'=>$Base->clientIp(),
    "remark"=>$Common->getQrcodeFrom(),//注册来源备注
    "business_code"=>$Common->getBusinessCodeFrom(),//注册来源记录,
);

//判断电脑是否存有推广者信息，如果有，就记录到新用户
if(isset($_COOKIE['rememberMe'])) {
    $formdata['pid'] = base64_decode($_COOKIE['rememberMe']); 
}

$result = $DB->Add($Table,$formdata);


$lasid = $DB->insert_id();



//发放注册送20元代金券
if(REG_COUPON > 0) {
    $coupon = $Common->used_coupon(REG_COUPON,$lasid);
}

echo json_encode(array(
    "status"=>"success",
    "coupon"=>$coupon,
));

$_SESSION["user_id"] = $lasid;
$_SESSION['phone_code_1'] = '';

// 生成唯一昵称，数据库已有将重新生成
function get_auto_nickname() {
    global $DB;
    $nickname = '25boy_'.str_pad(mt_rand(100, 999999), 6, '0', STR_PAD_LEFT);
    $row = $DB->GetRs('users','nickname',"WHERE nickname='{$nickname}'");
    if(!empty($row['nickname'])){
        $nickname = get_auto_nickname();
    }
    return $nickname;
}