<?php
/*
* 必要参数
* 商户名称，商户代码
* store_name，store_code
*/
$store_name = '';
$store_code = isset($_GET['code']) ? htmlspecialchars($_GET['code']) : NULL;
$do = isset($_GET['do']) ? htmlspecialchars($_GET['do']) : NULL;

switch ($store_code) {
    case 'gz_huimei':
        $store_name = '广州汇美批发店';
        break;
    case 'bj_sanlitun':
        $store_name = '北京三里屯';
        break;
    default:
        # code...
        break;
}

/*
* 记录cookie有效期30天
* 25boy.cn 全域有效
*/
setcookie('store_code', $store_code, time()+86400*30, '/','25boy.cn');
setcookie('store_name', $store_name, time()+86400*30, '/','25boy.cn');

switch ($do) {
    case 'pay':
        $url = '/?m=account&a=payment';
        break;
    
    default:
        $url = '/';
        break;
}
header("location:".$url);