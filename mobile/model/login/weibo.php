<?php
include_once($_SERVER['DOCUMENT_ROOT']."/socailclass/Weibo_oauth.php");

$weibo_class = new Weibo_oauth();

$uri = isset($_GET["uri"]) ? empty($_GET["uri"])?"/":$_GET["uri"] : "/";
$callback_url = urlencode("http://".$base_url."/?m=login&a=weibo.callback");
$back_url = urlencode("/?m=login&a=weibo.bind");
$return_url = $callback_url."&back_page=".urlencode($uri)."&back_url=".$back_url;
$code_url = $weibo_class->getAuthorizeURL($return_url);

header("location:$code_url");
exit;
?>