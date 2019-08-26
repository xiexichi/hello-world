<?php
$data = $_GET;
$data['social_id'] = $data['openid'];
$data['qq_social']='QSL';
$data["uri"] = substr($data["uri"],0,strlen($data["uri"])-1);

$result = $DB->GetRs("social","*","where is_bind=1 and type='qq' and social_id='".$data["social_id"]."'");

if(!empty($result['user_id'])){
    //如果已有账号，则自动登录
    session_start();
    $_SESSION["user_id"] = $result['user_id'];
    $_SESSION["login_time"] = date('Y-m-d H:i:s');

    if(isset($_SESSION["openid"]) && !empty($_SESSION["openid"])){
        $res = $DB->Set('users',array("openid"=>$_SESSION["openid"]),"where user_id='".$result['user_id']."'");
    }
    // echo "<script>window.parent.location='".$data["uri"]."'</script>";
    header('location:'.$data["uri"]);
}else{
    //还没有账号，注册一个

    //获取QQ信息
    $_SESSION['profile_image_url'] = $data['information']['figureurl_qq_1'];
    $_SESSION['avatar_hd'] = $data['information']['figureurl_qq_2'];
    $_SESSION['social_id'] = $data['social_id'];
    $_SESSION['social_name'] = $data['information']['nickname'];
    
    header('location:/?m=login&a=qq.bind&uri='.urlencode($data["uri"]));
}
?>