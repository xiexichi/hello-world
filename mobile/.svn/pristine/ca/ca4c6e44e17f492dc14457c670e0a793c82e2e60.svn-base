<?php
/*
* 领券二级页面
* 2016-10-19
*/
$channel = isset($_GET['channel']) ? trim($_GET['channel']) : '';


switch ($channel) {
	// 1626买物教室
	case 'buyroom':
		$sm->assign("adset", $Common->get_picshow(43), true);	// 页面广告
		$prolist = $Common->get_picshow(44);					// 商品列表
		break;

	// 1626买物教室，第二期
	case 'buyroom2':
		$sm->assign("adset", $Common->get_picshow(45), true);	// 页面广告
		$prolist = $Common->get_picshow(46);					// 商品列表
		break;
	
	default:
		$sm->assign("adset", $Common->get_picshow(41), true);	// 页面广告
		$prolist = $Common->get_picshow(42);					// 商品列表
		break;
}


$sm->assign("prolist", $prolist, true);
$sm->assign("channel", $channel, true);
$page_title = '领取优惠券';
$page_sed_title = '领取优惠券';