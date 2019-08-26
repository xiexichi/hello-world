<?php
error_reporting(E_ALL^E_NOTICE);
set_error_handler('displayErrorHandler');
//捕获Warring错误
function displayErrorHandler($error, $error_string, $filename=NULL, $line=NULL, $symbols=NULL)
{
    //var_dump($error);
    $error_no_arr = array(1=>'ERROR', 2=>'WARNING', 4=>'PARSE', 8=>'NOTICE', 16=>'CORE_ERROR', 32=>'CORE_WARNING', 64=>'COMPILE_ERROR', 128=>'COMPILE_WARNING', 256=>'USER_ERROR', 512=>'USER_WARNING', 1024=>'USER_NOTICE', 2047=>'ALL', 2048=>'STRICT');
    if(in_array($error,array(1,2,4,16)))
    {       
        //$tempfilename = $filename.str_split('\\');
        //var_dump($tempfilename);
       // $line . "-" . $error . "-" . $filename.str_split("\/");
        $errorArray = array(
            "title"=>$error_no_arr[$error],
            "description"=>$error_string,
            "footer"=>"发生时间：".date("Y-m-d H:i:s").", 请联系系统管理员解决问题，Email:info@25boy.com."
        );
        dieByError($errorArray);
    }
}
//显示错误信息
function dieByError($errorArray){
    global $Error;
    $Error->OutputError($errorArray);
    exit();
}
// 404页面
function show404($errorArray){
    global $sm;
    $sm->assign("error", $errorArray, true);
    $sm->display("public/404.tpl");
    exit();
}
?>