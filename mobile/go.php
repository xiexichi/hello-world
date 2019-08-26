<?php
/**
* 跳转程序
*/
$to = isset($_GET['to']) ? trim($_GET['to']) : 'http://m.25boy.cn/';

switch ($to) {
	// 2017年创建，佛山岭南天地店开业宣传
    case 'weixin-1209':
        $url = "http://mp.weixin.qq.com/s/_WXw_advIzEOnQrJ4MsArw";
        break;

    // 2018年创建，佛山岭南站店开业宣传
    case 'weixin-0418':
        $url = "http://mp.weixin.qq.com/s/hvqoOa3ye4Izdd5LeLatYA";
        break;

    // 2018年创建，广州太阳新天地开业宣传
    case 'weixin-0617':
        $url = "https://mp.weixin.qq.com/s/BSZtjRQS0bIsecwVHQYxcA";
        break;

    // 2018年创建，可口可乐合作集赞活动
    case 'weixin-0922':
        $url = "https://mp.weixin.qq.com/s/XPkq8nT5EGs7E3evNGbabw";
        break;

    // 2018年创建，烈山氏VR体验活动
    case 'weixin-0926':
        $url = "https://mp.weixin.qq.com/s/NfNF_bVyNeAatTXiqtea0A";
        break;

    // 2018年创建，双旦锦鲤抽奖活动
    case 'weixin1225':
        $url = "https://mp.weixin.qq.com/s/lRuf-UiXIxjzCBBvpRsZeQ";
        break;

    // 2019年创建，龙狮文创博物馆开张集赞图文
    case 'weixin0118':
        $url = "https://mp.weixin.qq.com/s/xL7i9RndAAQ-rIAMQ71vDw";
        break;
    
    default:
        $url = urldecode($to);
        break;
}


header("Location: {$url}");