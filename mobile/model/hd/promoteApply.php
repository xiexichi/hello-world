<?php
$page_title = "一次分享，终身收益";
$page_sed_title = '分享返佣';
$user_id = isset($_SESSION["user_id"]) ? (int)$_SESSION["user_id"] : 0;
$do = isset($_POST['do']) ? htmlspecialchars_decode($_POST['do']) : '';
$promote_id = isset($promote['promote_id']) ? $promote['promote_id'] : 0;

// 检查登录
if($do == 'checkLogin'){
    if(empty($user_id)){
        echo json_encode(array("status"=>"nologin",'msg'=>'请登录后获取您的专属返佣链接。'));
    }else{
        echo json_encode(array("status"=>"success",'res'=>array('user_id'=>$user_id,'pid'=>$promote_id)));
    }
    exit;
}

// 自动成为推广者
if(!empty($user_id) && empty($promote_id)){
    // 加入推广者表
    $DB->Add('promote',array(
            'user_id'       => $user_id,
            'create_time'   => date("Y-m-d H:i:s"),
    ));
    $promote_id = $DB->insert_id();
}

// 已经邀请人数
$myProer = array();
if(!empty($promote_id))
{
    $Fileds = "user_id,nickname,phone,create_date";
    $Condition = "where pid={$promote_id} ORDER BY create_date DESC LIMIT 100";
    $Row = $DB->Get('users', $Fileds, $Condition);
    $Row = $DB->result;
    while($rs = $DB->fetch_assoc($Row)){
        // 统计单个会员带来收益
        $result = $DB->query("SELECT sum(p.earnings) as total FROM `promote_earnings` p left join bag b on p.pay_sn=b.pay_sn where p.promote_id={$promote_id} and b.user_id={$rs['user_id']}");
        $man = $DB->fetch_array($result);
        $rs['total'] = isset($man['total']) ? floatval($man['total']) : 0;
        $rs['nickname']= mb_substr($rs['nickname'], 0, 1, 'utf-8').'***'. mb_substr($rs['nickname'], -1, 1, 'utf-8');
        $myProer[] = $rs;
    }
}


// 真实-佣金累计发放排行榜
$ranking = array();
$give_total = 0;
$result = $DB->query("SELECT p.promote_id,p.earnings_total,u.user_id,u.nickname FROM `promote` p left join users u on p.user_id=u.user_id order by p.earnings_total desc limit 10");
while ($rs = $DB->fetch_array($result)) {
    $give_total += $rs['earnings_total'];
    $rs['nickname']= mb_substr($rs['nickname'], 0, 1, 'utf-8').'***'. mb_substr($rs['nickname'], -1, 1, 'utf-8');
    $ranking[] = $rs;
}

// 虚假-佣金排行榜
$nicheng = array('苏生不惑','justjavac','thenbsp','流水破东风','Ochukai','曹宇飞','太上老君手下的妖怪','纸牌屋弗兰克','命中水ヽ','我不了解浮云');
$give_total = $give_total*100;
foreach($ranking as $key=>$val){
    $ranking[$key]['nickname'] = mb_substr($nicheng[$key], 0, 1, 'utf-8').'***'. mb_substr($nicheng[$key], -1, 1, 'utf-8');
    $ranking[$key]['earnings_total'] = $val['earnings_total']*10;
}


// PI
$pidCode = $Base->myEncode($promote_id);

// 微信分享
$wxconfigarray = array(
    'title' => '25boy潮牌新国货',
    'link' => 'http://m.25boy.cn/?m=hd&a=redpack&PI='.$pidCode,
    'imgUrl' => 'http://img.25miao.com/114/1483179767.gif',
    'desc' => '推荐给你很有特色设计的衣服',
);

// 隐藏底部导航栏
$site_nav_display = 'hide';

// $sm->assign("isPromote", $isPromote, true);
$sm->assign("myProer", $myProer, true);
$sm->assign("give_total", number_format($give_total,2,'.',','), true);
$sm->assign("ranking", $ranking, true);
$sm->assign("hide_site_top_banner", true, true);
$sm->assign("myUrl", 'http://m.25boy.cn/?m=hd&a=redpack&PI='.$pidCode, true);
$sm->assign("goback", '/?PI='.$pidCode, true);