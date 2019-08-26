<?php
$page_title = "我的二五";
$page_sed_title = '返佣项目';
$page_sed_search = 'search_promotion';
$module = 'promote'; //模块
$submodule = 'promote_promotion'; //哪个模块

$Base->check_permission($is_promote);

if(isset($_SESSION["user_id"])&&!empty($_SESSION["user_id"])) {

    $when          =  empty($_GET['when']) ? 'today' : htmlspecialchars($_GET["when"]);
    $all_category  =  $category;  //未经处理的全部类目
    $secondly_category = array();   //经过处理后的全部类目
    $promote_id    =  $promote['promote_id'];
    $c             =  empty($_GET["c"]) ? 'pp' : htmlspecialchars($_GET["c"]);
    $searchCategory=  empty($_GET['category']) ? '' : intval($_GET["category"]);
    $searchKeywords=  empty($_GET['keywords']) ? '' : htmlspecialchars($_GET["keywords"]);


    switch ($when) {
        case 'yesterday':
            $when_cn = '昨日';
            break;
        case 'this_week':
            $when_cn = '本周';
            break;
        case 'last_week':
            $when_cn = '上周';
            break;
        case 'this_month':
            $when_cn = '本月';
            break; 
        case 'last_month':
            $when_cn = '上月';
            break;
        default:
            $when_cn = '今日';
            break;
    }

    switch ($c) {
        
        /****************************************** 单品推广 ******************************************/
        case 'pp':
            $searchArr = array(
                'searchCategory' => $searchCategory,
                'searchKeywords' => $searchKeywords
            );

            //可推广项目
            $promote_product_list = $Common->get_beyond_product_list($promote_id,$searchArr);
            //可推广项目
            $promote_first = $Common->get_beyond_first($promote_id,4);

            //已推广项目
            $sql = "SELECT *,(SELECT i.url FROM product_img i WHERE pi.item_id = i.product_id LIMIT 1) as url
                    FROM promote_item pi
                    JOIN products p ON pi.item_id = p.product_id";

            if(!empty($searchCategory)) $sql .= " LEFT JOIN product_to_category pc ON pi.item_id = pc.product_id";

            $sql .= " WHERE pi.promote_id = {$promote_id} AND type = 0";

            if(!empty($searchCategory)) $sql .= "  AND pc.category_id = {$searchCategory}";

            if(!empty($searchKeywords)) $sql .= "  AND (p.product_name LIKE '%{$searchKeywords}%' OR p.sku_sn LIKE '%{$searchKeywords}%')";

            $sql .= " ORDER BY pi.create_time DESC,p.product_id DESC ";

            $sql .= " LIMIT 0,10";

            $result = $DB->query($sql);
            $promote_product = array();
            while ($rows = $DB->fetch_array($result)) {
                $arr = array();
                $arr['pitem_id']    = $rows['pitem_id'];
                $arr['url']         = $rows['url'];
                $arr['product_id']  = $rows['item_id'];
                $arr['link']        = empty($rows['link']) ? '' : $rows['link'];
                $arr['product_name']= $rows['product_name'];
                array_push($promote_product, $arr);
            }
            foreach ($promote_product as $key => $value) {
                //推广链接
                $promote_product[$key]['link'] = $Base->getPromoteLink(PROMOTE_HTTP,$promote_id,0,$value['product_id']);

                //是否有效
                $promote_product[$key]['is_valid'] = (empty($promote_product_list[$value['product_id']]) && empty($promote_first)) ? 0 : 1;
                //点击数
                $whenSql = $Common->getWhenSql('click_time',$when);
                $row = $DB->GetRs('promote_click','count(pitem_id) AS total',"WHERE pitem_id = {$value['pitem_id']} AND $whenSql");
                $promote_product[$key]['click_num'] = $row['total'];
            
                //付款笔数
                $whenSql = $Common->getWhenSql('o.pay_date',$when);
                $sql   = "SELECT count(DISTINCT po.order_id)AS total FROM promote_order po
                          LEFT JOIN orders o ON po.order_id = o.order_id
                          WHERE o.pay_status = 1 AND o.pay_method <> 2 AND po.pitem_id = {$value['pitem_id']} AND $whenSql";
                $query = $DB->query($sql);
                $promote_product[$key]['paid_order_num'] = $DB->fetch_array()['total'];

                //效果预估
                $whenSql = $Common->getWhenSql('o.pay_date',$when);
                $sql   = "SELECT sum(po.commission) AS total FROM promote_order po
                          LEFT JOIN orders o ON po.order_id = o.order_id
                          WHERE o.pay_status = 1 AND o.pay_method <> 2 AND po.pitem_id = {$value['pitem_id']} AND $whenSql";
                $query = $DB->query($sql);
                $row   = $DB->fetch_array();
                $promote_product[$key]['paid_order_total'] = empty($row['total']) ? '0.00' : $row['total'];

                //预估收入
                $whenSql = $Common->getWhenSql('received_time',$when);
                $sql   = "SELECT sum(earnings) AS total FROM promote_earnings
                         WHERE pitem_id = {$value['pitem_id']} AND $whenSql";
                $query = $DB->query($sql);
                $row   = $DB->fetch_array();
                $promote_product[$key]['received_order_total'] = empty($row['total']) ? '0.00' : $row['total'];
            }

            //处理全部类目
            foreach ($all_category as $key => $value) {
                foreach ($value['childrens'] as $k => $v) {
                    array_push($secondly_category, $v);
                }
            }
            $sm->assign("page_sed_search", $page_sed_search, true);
            $sm->assign("promote_product", $promote_product, true);
            break;
        case 'pw':
/*            //可推广项目-网站
            $beyond_website = $Common->get_beyond_website($promote_id);

            //查找项目
            $sql = "SELECT * FROM promote_item WHERE promote_id = $promote_id AND type = 2 LIMIT 1";
            $result = $DB->query($sql);
            $promote_website = $DB->fetch_array($result);
            $is_promote_website = empty($promote_website) ? 0 : 1;

            if(isset($promote_website['pitem_id'])) {

                //是否有效
                $promote_website['is_valid'] = empty($beyond_website) ? 0 : 1;

                //点击数
                $whenSql = $Common->getWhenSql('click_time',$when);
                $sql   = "SELECT count(pitem_id) AS total FROM promote_click WHERE pitem_id = {$promote_website['pitem_id']} AND $whenSql";
                $query = $DB->query($sql);
                $promote_website['click_num'] = $DB->fetch_array()['total'];
                  // print_r($promote_website);exit();
              
            
                //付款笔数
                $whenSql = $Common->getWhenSql('o.pay_date',$when);
                $sql   = "SELECT count(DISTINCT po.order_id)AS total FROM promote_order po
                          LEFT JOIN orders o ON po.order_id = o.order_id
                          WHERE o.pay_status = 1 AND o.pay_method <> 2 AND po.pitem_id = {$promote_website['pitem_id']} AND $whenSql";
                $query = $DB->query($sql);
                $promote_website['paid_order_num'] = $DB->fetch_array()['total'];

                //效果预估
                $whenSql = $Common->getWhenSql('o.pay_date',$when);
                $sql   = "SELECT sum(po.commission) AS total FROM promote_order po
                          LEFT JOIN orders o ON po.order_id = o.order_id
                          WHERE o.pay_status = 1 AND o.pay_method <> 2 AND po.pitem_id = {$promote_website['pitem_id']} AND $whenSql";
                $query = $DB->query($sql);
                $row   = $DB->fetch_array();
                $promote_website['paid_order_total'] = empty($row['total']) ? '0.00' : $row['total'];

                //预估收入
                $whenSql = $Common->getWhenSql('received_time',$when);
                $sql   = "SELECT sum(earnings) AS total FROM promote_earnings
                        WHERE pitem_id = {$promote_website['pitem_id']} AND $whenSql";
                $query = $DB->query($sql);
                $row   = $DB->fetch_array();
                $promote_website['received_order_total'] = empty($row['total']) ? '0.00' : $row['total'];
            }

            // $promote_website['commission_rate'] = number_format($promote_config['promote_website']['commission_rate'],2);
            $sm->assign("is_promote_website", $is_promote_website, true);
            $sm->assign("promote_website", $promote_website, true);
            break;*/
        case 're':
            //判断推广计划-充值返
            $promote_recharge = $Common->get_beyond_recharge($promote_id);

            if(!empty($promote_recharge)) {
                
                //充值笔数
                $whenSql = $Common->getWhenSql('received_time',$when);
                $sql = "SELECT count(*) AS total FROM promote_earnings
                        WHERE earnings_type = 're_recharge' AND promote_id = $promote_id AND $whenSql";
                $query = $DB->query($sql);
                $promote_recharge['recharge_num'] = $DB->fetch_array()['total'];

                //充值总额
                $whenSql = $Common->getWhenSql('received_time',$when);
                $sql = "SELECT sum(re_price) AS total FROM promote_earnings
                        WHERE earnings_type = 're_recharge' AND promote_id = $promote_id AND $whenSql";
                $query = $DB->query($sql);
                $row   = $DB->fetch_array();
                $promote_recharge['recharge_total'] = empty($row['total']) ? '0.00' : $row['total'];

               
                //预估收入
                $whenSql = $Common->getWhenSql('received_time',$when);
                $sql = "SELECT sum(earnings) AS total FROM promote_earnings
                        WHERE earnings_type = 're_recharge' AND promote_id = $promote_id AND $whenSql";
                $query = $DB->query($sql);
                $row   = $DB->fetch_array();
                $promote_recharge['recharge_earnings_total'] = empty($row['total']) ? '0.00' : $row['total'];

                //平均佣金比率
                $promote_recharge['recharge_average_rate'] = $promote_recharge['recharge_total'] > 0 ? round($promote_recharge['recharge_earnings_total'] / $promote_recharge['recharge_total'] * 100,2) : '0.00';
            }
            // print_r($promote_recharge);exit();
            $sm->assign("is_promote_recharge", empty($promote_recharge) ? 0 : 1, true);
            $sm->assign("promote_recharge", $promote_recharge, true);
            break;
        default:

            break;
    }


}


//查找网站链接
$promote_website['link'] = $Base->getPromoteLink(PROMOTE_HTTP,$promote_id,2);

$sm->assign("promote_website", $promote_website, true);
$sm->assign("c", $c, true);
$sm->assign("when", $when, true);
$sm->assign("when_cn", empty($when_cn) ? '昨日' : $when_cn, true);
$sm->assign("secondly_category", json_encode(array('cs'=>$secondly_category)), true);
$sm->assign("searchCategory", $searchCategory, true);
$sm->assign("searchKeywords", $searchKeywords, true);
$sm->assign("is_promote", $is_promote, true);
$sm->assign("is_weixin", is_weixin(), true);
$sm->assign('promote',$promote,true);
$sm->assign("module", $module, true);
$sm->assign("submodule", $submodule, true);

