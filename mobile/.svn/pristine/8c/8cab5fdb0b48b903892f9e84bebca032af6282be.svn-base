<?php
include_once($_SERVER['DOCUMENT_ROOT']."/socailclass/Qqclass.php");
$qq_class = new Qqclass();
$uri = isset($_GET["uri"]) ? empty($_GET["uri"])?"/":$_GET["uri"] : "/";

//申请到的appid
$qq_params["qq_appid"]    = "101179648";
//申请到的appkey
$qq_params["qq_appkey"]   = "f7f278f9f3cb263580f20da21a0facf8";
//QQ登录成功后跳转的地址,请确保地址真实可用，否则会导致登录失败。
$qq_params["qq_callback"] = "http://www.25boy.cn/qq.callback.php?uri=".$uri;
//QQ授权api接口.按需调用
$qq_params["qq_scope"] = "get_user_info";
/* End of file config.php */
/* Location: ./application/config/config.php */


$qq_state = md5(uniqid(rand(), TRUE)); //CSRF protection
$qq_class->qq_login($qq_state, $qq_params["qq_appid"], $qq_params["qq_scope"], $qq_params["qq_callback"],"login");	//用户点击qq登录按钮调用此函数