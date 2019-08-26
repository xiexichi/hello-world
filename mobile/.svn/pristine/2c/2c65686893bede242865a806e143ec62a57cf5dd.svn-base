<?php

$cid = isset($_GET["cid"]) ? intval($_GET["cid"]) : 0;
$brand_id = isset($_GET["brand_id"]) ? intval($_GET["brand_id"]) : 0;

$keys = array();
if($cid==0){
    $page_title = "全部商品";
    $page_sed_title = '全部商品';
}else{
    $CurrentCategoryArray = seekarr($cid);
    if(count($CurrentCategoryArray)>0){
        $page_sed_title = $CurrentCategoryArray["category_name"];
        $page_title = $CurrentCategoryArray["category_name"];
        $keys[] = $CurrentCategoryArray["category_name"];
    }else{
        $page_sed_title = '全部商品';
        $page_title = "商品列表";
    }
    //echo "array:".array_search((int)$cid,$category,true);
}

$brands = $Common->getBrands();
// print_r($brands);

if($brand_id > 0){
    $page_title = $brands[$brand_id]['brand_name'].' '.$page_title;
    $page_sed_title = $brands[$brand_id]['brand_name'];
    $keys[] = $brands[$brand_id]['brand_name'];
}
$seo_keyword = implode(',', $keys);


  /*------------------------------------- 记录推广信息 -------------------------------*/

  $category_id = $cid;
  $PI = isset($_GET["PI"])?$_GET["PI"]:0;
  if(!empty($PI)) {
    //判断推广者
    $promote_id = $Base->myDecode($PI);
    $promote = $DB->GetRs("promote","*","WHERE promote_id = ".$promote_id);

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
      $promote_category_list = $Common->get_beyond_category_list($promote_id);
      // print_r($promote_product_list);exit();
      if(!empty($promote_category_list[$category_id])) {
        $promote_category = $promote_category_list[$category_id];
        $pplan_id = isset($promote_category['pplan_id'])?$promote_category['pplan_id']:0;
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
        $promote_link = $Base->getPromoteLink(PROMOTE_HTTP,$promote['promote_id'],1,$category_id);
    }else {
        $promote = '';
    }
}


$sm->assign("promote", $promote, true);
$sm->assign("promote_link", $promote_link, true);
$sm->assign("category_name", $page_sed_title, true);
$sm->assign("brands", $brands, true);
$sm->assign("brand_id", $brand_id, true);
$sm->assign("new", $_GET['new'], true);
$sm->assign("sale", $_GET['sale'], true);
$sm->assign("hot", $_GET['hot'], true);
$sm->assign("cid", $cid, true);
$sm->assign("k", $_GET['k'], true);

// print_r($brands);exit();
function seekarr($category_id){
    global $category;

    $index_categorys = array();
    foreach ($category as $key => $val){
        $index_categorys[$val['category_id']]['category_id'] = $val['category_id'];
        $index_categorys[$val['category_id']]['category_name'] = $val['category_name'];
        $index_categorys[$val['category_id']]['img_url'] = $val['img_url'];
        foreach ($val['childrens'] as $v) {
            $index_categorys[$v['category_id']] = $v;
        }
    }

    return $index_categorys[$category_id];
}

// 隐藏底部导航栏
$site_nav_display = 'hide';