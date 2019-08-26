<?php
$page_title = "我的二五";
$page_sed_title = '推广返佣';
$page_sed_search = '';
$module = 'promote'; //模块
$submodule = 'promote_index';

$Base->check_permission($is_promote);
if(isset($_SESSION["user_id"])&&!empty($_SESSION["user_id"])) {

    $all_category  =  $category;  //未经处理的全部类目
    $secondly_category = array();   //经过处理后的全部类目
    $promote_id    =  $promote['promote_id'];
    $c             =  empty($_GET["c"]) ? 'pp' : htmlspecialchars($_GET["c"]);
    $searchCategory=  empty($_GET['category']) ? '' : intval($_GET["category"]);
    $searchKeywords=  empty($_GET['keywords']) ? '' : htmlspecialchars($_GET["keywords"]);

    switch ($c) {

        /****************************************** 单品推广 ******************************************/
        case 'pp':
            $searchArr = array(
                'searchCategory' => $searchCategory,
                'searchKeywords' => $searchKeywords
            );

            //推广单品
            $promote_product = $Common->get_beyond_product_list($promote_id,$searchArr);
            $rows = $new_promote_product = array();
            //处理数组，计算月销量
            foreach ($promote_product as $key => $value) {
                $monthSale = @ceil(abs($value['sale'])*30/$value['datediff']);
                $rows[$key] = $monthSale;
                $promote_product[$key]['monthSale'] = $monthSale;
                // $promote_product[$key]['sale'] = abs($value['sale']);
            }
            //排序
            arsort($rows);
            foreach ($rows as $key => $value) {
                $new_promote_product[$key] = $promote_product[$key];
            }
            $promote_product = $new_promote_product;
            $promote_product = array_slice($promote_product, 0,10);
            // print_r($promote_product);exit();

            //查找链接
            foreach ($promote_product as $key => $value) {
                $promote_product[$key]['link'] = $Base->getPromoteLink(PROMOTE_HTTP,$promote_id,0,$value['product_id']);
            }

            //处理全部类目
            foreach ($all_category as $key => $value) {
                foreach ($value['childrens'] as $k => $v) {
                    array_push($secondly_category, $v);
                }
            }

            //查找网站链接
            $promote_website['link'] = $Base->getPromoteLink(PROMOTE_HTTP,$promote_id,2);
            // print_r($promote_website);
            $page_sed_search = 'search_promote';
            $sm->assign("promote_product", $promote_product, true);
            $sm->assign("promote_website", $promote_website, true);
            break;
        case 'pc':
            //推广类目
            $promote_category = $Common->get_beyond_category_list($promote_id);
            //搜索类目
            if(!empty($searchKeywords)) {
                foreach ($promote_category as $key => $value) {
                    if(strpos($value['category_name'],$searchKeywords) === false && strpos($value['category_name'],strtoupper($searchKeywords)) === false) {
                       unset($promote_category[$key]); 
                    }
                }
            }

            $color = array('#e3713f','#e65349','#6c6bc8','#25bc91','#e9a63b','#4f7dd3');
            //查找数量,链接
            foreach ($promote_category as $key => $value) {
                $sql = "SELECT COUNT(p.product_id) AS total FROM product_to_category pc
                        LEFT JOIN products p ON pc.product_id = p.product_id
                        WHERE pc.category_id = {$value['category_id']} AND p.stock = 1";
                $result = $DB->query($sql);
                $promote_category[$key]['product_num'] = $DB->fetch_array($result)['total'];
                $promote_category[$key]['link'] = $Base->getPromoteLink(PROMOTE_HTTP,$promote_id,1,$value['category_id']);
                $promote_category[$key]['bgColor']= $color[rand(0,5)];
                $promote_category[$key]['imgFont']= mb_substr($value['category_name'],0,2,'utf-8');                
            }
            // print_r($promote_category);exit();

            //查找网站链接
            $promote_website['link'] = $Base->getPromoteLink(PROMOTE_HTTP,$promote_id,2);

            $page_sed_search = 'search_promote_category';
            $sm->assign("promote_category", $promote_category, true);
            $sm->assign("promote_website", $promote_website, true);
            break;
        case 'pw':
            //推广网站
            $promote_website = $Common->get_beyond_website($promote_id);

            //查找链接

            $promote_website['link'] = $Base->getPromoteLink(PROMOTE_HTTP,$promote_id,2);

            //宝贝数
            $row = $DB->GetRs('products','count(product_id) AS total',"WHERE stock = 1");
            $promote_website['stock_product_num'] = $row['total'];

            $sm->assign("promote_website", $promote_website, true);
            // $sm->assign("page_sed_search", '', true);
            break;
        default:
            break;
    }


}

$sm->assign("c", $c, true);
$sm->assign("secondly_category", json_encode(array('cs'=>$secondly_category)), true);
$sm->assign("searchCategory", $searchCategory, true);
$sm->assign("searchKeywords", $searchKeywords, true);
$sm->assign("is_promote", $is_promote, true);
$sm->assign("is_weixin", is_weixin(), true);
$sm->assign('promote',$promote,true);
$sm->assign("module", $module, true);
$sm->assign("submodule", $submodule, true);
$sm->assign("page_sed_search", $page_sed_search, true);
