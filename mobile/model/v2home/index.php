<?php
// 图片轮播
$sm->assign("banner_slide", $Common->get_picshow(10), true);
// 首页活动广告区
$sm->assign("activity_banner", $Common->get_picshow(73), true);
// 首页新品栏目图
$sm->assign("new_banner", $Common->get_picshow(27), true);
// 首页合作联名区
$jointName =  $Common->get_picshow(76);
$joint_name[] = array($jointName[0],$jointName[1],$jointName[3]);
$joint_name[] = array($jointName[2],$jointName[4]);
$sm->assign("joint_name", $joint_name, true);
// 新品上架商品列表
$sm->assign("product_new_list", $Common->get_picshow(5,9), true);
// 首页类目栏目图
$sm->assign("category_banner", $Common->get_picshow(14,1), true);
// 首页类目栏目图
$sm->assign("index_categorys_items", $Common->get_picshow(77), true);


// 首页类目栏目图
$sm->assign("brand_banner", $Common->get_picshow(75,1), true);
// 首页品牌展示
$sm->assign("brands_list", $Common->get_picshow(29), true);
// 首页单品图区
$sm->assign("banner_item", $Common->get_picshow(13), true);
// 热销商品
$sm->assign("productlist", $Common->get_picshow(7), true);


// 精选晒图
$recommend_share = array();
$Row = $DB->Get("share","*","where is_recommend=1 AND status=1 order by share_sort DESC,sort DESC, date_added DESC limit 10");
$Row = $DB->result;
while($r = $DB->fetch_assoc($Row)){
  $photos = unserialize($r['photos']);
  $r['image'] = isset($photos[0]) ? trim($photos[0]) : '';
  $r['userimg'] = $Base->site_img($r['userimg']);
  if(!empty($r['image'])) $recommend_share[] = $r;
}
$sm->assign("recommend_share", $recommend_share, true);


// 充值入口样式
$prepaid_style = ''; // red
$sm->assign("prepaid_style", $prepaid_style, true);


// 显示主导航
$site_nav_display = 'show';

// 不显示全局顶部广告
$sm->assign("hide_site_top_banner", true, true);

// 微信分享
$wxconfigarray = array(
    'title' => '25BOY国潮男装',
    'desc' => '原创潮牌'
);

  /*------------------------------------- 记录推广信息 -------------------------------*/

  $PI = isset($_GET["PI"])?$_GET["PI"]:0;
  if(!empty($PI)) {
    //判断推广者
    $promote_id = $Base->myDecode($PI);
    $promote = $DB->GetRs("promote","*","WHERE promote_id = ".$promote_id);
    // print_r($promote);
    //存在未被冻结的推广者
    if(!empty($promote) && !$promote['is_frozen']) {
      $promote_id = $promote['promote_id'];
      //记录cookie
      $default_expire = time()+3600*24*30; //默认有效期
      $promote_save = $promote_config['promote_save']; //保存天数
      $promote_expire = empty($promote_save) ? $default_expire : time()+3600*24*$promote_save;
      if(!isset($_COOKIE['rememberMe'])) {
        setcookie('rememberMe',base64_encode($promote_id),$promote_expire,'/','25boy.cn');  
      }
      // exit();

      //获取计划(后续购物)
      $pplan_id = 0;
      $promote_website = $Common->get_beyond_website($promote_id);
      // print_r($promote_product_list);exit();
      if(!empty($promote_website)) {
        $pplan_id = isset($promote_website['pplan_id'])?$promote_website['pplan_id']:0;
        if(!empty($pplan_id)) {
        //记录点击(首次/后续购物)
        $DB->Add("promote_click",array(
            "pitem_id"  => 0,
            "pplan_id"  => $pplan_id,
            "source"    => 'mobile',
            "click_time"=> date("Y-m-d H:i:s"),
        ));
        }
      }

    }
  }

/*------------------------------------- 判断推广者 -------------------------------*/

  //判断推广者
$promote = $promote_link = '';

if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] != 0) {
    $promote = $DB->GetRs("promote","*","WHERE user_id = ".$_SESSION["user_id"]);

    if(!empty($promote)) {
        $promote_link = $Base->getPromoteLink(PROMOTE_HTTP,$promote['promote_id'],2);
    }else {
        $promote = '';
    }
}

/*------------------------------------- 放假通知 -------------------------------*/
$is_holiday = 0; //是否有通知
$holiday_pic = "";//通知图片

//寻找正在进行中的放假通知，取一个
$holiday = $DB->GetRs("holiday","*","WHERE NOW() >= start_date AND NOW() <= end_date ORDER BY holiday_id DESC");
if(!empty($holiday)) {
  $is_holiday = 1;
  $holiday_pic = $holiday['holiday_pic'];
}

$sm->assign("promote", $promote, true);
$sm->assign("promote_link", $promote_link, true);
$sm->assign("is_holiday", $is_holiday, true);
$sm->assign("holiday_pic", $holiday_pic, true);