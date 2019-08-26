<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$account = isset($_POST["account"]) ? trim($_POST["account"]) : "";
$password = isset($_POST["password"]) ? trim($_POST["password"]) : "";
$login_phone_code = isset($_POST["login_phone_code"]) ? trim($_POST["login_phone_code"]) : "";
$way = isset($_POST["way"]) ? trim($_POST["way"]) : "";
$histroy_url = empty($_SESSION['gourl']) ? $_SERVER['HTTP_REFERER'] : $_SESSION['gourl'];
$openid = isset($_POST["openid"]) ? trim($_POST["openid"]) : $_SESSION['openid'];

function echo_empty() {
    echo json_encode(array(
        "status"=>"empty",
        "msg"=>'没有输入数据或不完整'
    ));
    exit;
}
function echo_phone_code_x() {
    echo json_encode(array(
        "status"=>"phone-code-x",
        "msg" => '短信验证码错误或已失效'
    ));
    exit;
}
//服务器判断数据是否为空
if($account==""){echo_empty();}
if($way=="pass" && $password == ''){echo_empty();}
if($way=="msg" && $login_phone_code == ''){echo_empty();}
if($way=='msg' && empty($_SESSION['phone_code_2'])) {echo_phone_code_x();}

// 检查帐户名
$account_type = '';
if(strstr($account,'@') === false){
    if($Base->is_phone_number($account) == false){
        echo json_encode(array(
            "status"=>"account",
            "msg"=>'请输入正确的手机号码'
        ));
        exit;
    }else{
        $account_type = 'phone';
    }
}else{
    if(!$Base->is_email($account)){
        echo json_encode(array(
            "status"=>"account"
        ));
        exit;
    }else{
        $account_type = 'email';
    }
}



// 微信判断
if(is_weixin() && !empty($openid)){
    $Table="users";
    $Fileds = "email,phone";
    $Condition = "where openid='".$openid."'";
    $Row = $DB->Get($Table,$Fileds,$Condition,0);
    $Row = $DB->result;
    $RowCount = $DB->num_rows($Row);
    if($RowCount!=0){
        while($result = $DB->fetch_assoc($Row)) {
            if(($account_type=='email' && $result['email']==$account) || ($account_type=='phone' && $result['phone']==$account)){
                echo json_encode(array(
                    "status"=>"openid",
                    "account"=>$account,
                    'msg'=>'该微信号已经绑定了帐号'
                ));
                exit;
            }
        }
    }
}

//判断账号是否存在
$Table="users";
$Fileds = "user_id,password,nickname,openid";
if($account_type=='phone'){
    $Condition = "where phone='".$account."'";
}else{
    $Condition = "where email='".$account."'";
}
$row = $DB->GetRs($Table,$Fileds,$Condition);
if(empty($row)){
    echo json_encode(array(
        "status"=>"nouser",
        "msg"=>'找不到此帐户'
    ));
    exit;
}

//密码登录，判断密码是否正确
if($way == 'pass' && md5($password)!=$row["password"] && $Base->pass_crypt($password)!=$row["password"]) {
    echo json_encode(array(
        "status"=>"nopassword",
        "msg"=>'请输入正确的密码'
    ));
    exit;
}

//短信登录，判断验证码是否正确
$now_time = time();
$expire_time = 10 * 60;

if($way == 'msg' && ($login_phone_code!=$_SESSION['phone_code_2']||
    ($now_time - $_SESSION['apply_time_2'] > $expire_time))) {
    echo_phone_code_x();
}

//绑定微信
if(!empty($openid) && empty($row['openid'])){
    $result = $DB->Set($Table,array("openid"=>$openid),"where user_id='".$row['user_id']."'");
}
$_SESSION["user_id"] = $row["user_id"];
$_SESSION["nickname"] = $row["nickname"];
$_SESSION['phone_code_2'] = '';
unset($_SESSION['gourl']);
echo json_encode(array(
    "status"=>"success",
    "nickname"=>$row["nickname"],
    'gourl' => $histroy_url
));





