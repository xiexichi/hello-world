<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");


if(!isset($_SESSION["user_id"]) || $_SESSION["user_id"]==0) {
    echo json_encode(array(
        "status"=>"nologin"
    ));
    exit;
}

if(is_weixin()){
    if(!isset($_SESSION["openid"]) || empty($_SESSION["openid"])) {
        echo json_encode(array(
            "status"=>"nologin"
        ));
        exit;
    }
}

$field = isset($_POST["field"]) ? trim($_POST["field"]) : "";
$val = isset($_POST["val"]) ? trim($_POST["val"]) : "";

//取出用户资料
$Table="users";
$Fileds = "user_id,nickname,phone,flag";
$Condition = "where user_id=".$_SESSION["user_id"];
$userrow = $DB->GetRs($Table,$Fileds,$Condition);
if(empty($userrow)){
    echo json_encode(array(
        "status"=>"nouser",
        "field"=>$field,
        "val"=>$val
    ));
    exit;
}

if(!in_array($field, array('email','nickname','realname','phone','gender','birthday'))){
    echo json_encode(array(
        "status"=>"error"
    ));
    exit;
}

if($field==""||$val==""){
    echo json_encode(array(
        "status"=>"empty",
        "field"=>$field,
        "val"=>$val
    ));
    exit;
}

// 检查昵称是否重复
if($field == 'nickname'){
    if(trim($userrow['nickname']) != ""){
        echo json_encode(array(
            "status"=>"noeditnickname",
        ));
        exit;
    }

    if(!$Base->strCheck($val)){
        echo json_encode(array(
            "status"=>"dis_str",
        ));
        exit;
    }

    if($Base->strLenW2($val)>16 || $Base->strLenW2($val)<5){
        echo json_encode(array(
            "status"=>"len_nickname",
        ));
        exit;
    }
    
    if(in_array(strtolower($val), $SITECONFIGER['deny_nickname'])){
        echo json_encode(array(
            "status"=>"deny_nickname",
        ));
        exit;
    }

    $Table="users";
    $Fileds = "user_id,nickname";
    $Condition = "where nickname='".$val."'";
    $row = $DB->GetRs($Table,$Fileds,$Condition);
    if(isset($row['user_id'])){
        echo json_encode(array(
            "status"=>"nickname",
        ));
        exit;
    }
}

// 检查邮箱是否重复
if($field == 'email'){
    $pattern  =  '/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/';
    if(!preg_match($pattern,$val)){
        echo json_encode(array(
            "status"=>"is_email",
        ));
        exit;
    }

    $Table="users";
    $Fileds = "user_id";
    $Condition = "where email='".$val."'";
    $row = $DB->GetRs($Table,$Fileds,$Condition);
    if(isset($row['user_id'])){
        echo json_encode(array(
            "status"=>"has_email",
        ));
        exit;
    }
}

//检查手机
if($field=='phone'){
    //如果手机为已验证状态，不允许重新更改
    if($userrow['flag'] == 1 && !empty($userrow['phone'])){
        echo json_encode(array(
            "status"=>"noeditphone",
        ));
        exit;
    }
    //判断手机格式
    if(!$Base->is_phone_number($val)){
        echo json_encode(array(
            "status"=>"format",
            "field"=>$field,
            "val"=>$val
        ));
        exit;
    }
/*    //判断是否重复自身
    if($userrow['phone'] == $val){
        echo json_encode(array(
            "status"=>"copy",
            "field"=>$field,
            "val"=>$val
        ));
        exit;
    }*/

    //判断手机号码是否存在
    $Table="users";
    $Fileds = "user_id,phone";
    $Condition = "where phone='".$val."' AND flag = 1";
    $row = $DB->GetRs($Table,$Fileds,$Condition);
    if(isset($row['user_id'])){
        echo json_encode(array(
            "status"=>"has_phone",
            "field"=>$field,
            "val"=>$val
        ));
        exit;
    }


    //当手机号码检验合格后返回
     echo json_encode(array(
        "status"=>"is_real_phone",
        "field"=>$field,
        "val"=>$val
     ));

    exit();

}

//检查成功后，修改资料
if(!empty($userrow)){
    
    $result = $DB->Set($Table,array($field=>$val),"where user_id=".$_SESSION["user_id"]);

    if($field=="gender"){
        $val = $val=="male" ? "先生" : "女士";
    }

    if($field=="birthday"){
        $val = $Base->FormatTime($val,"ymd_sign",false);
    }
    //返回成功
    echo json_encode(array(
        "status"=>"success",
        "field"=>$field,
        "val"=>$val
    ));
    exit;
}
