<?php
/*
* 领取50元红包
* 每个用户限领1次，分销不能领取
* 2016-12-23
*/

$do = isset($_POST['do']) ? htmlspecialchars_decode($_POST['do']) : null;
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
// 分销不能领取
if(isset($is_seller) && $is_seller==1){
    echo json_encode(array(
	    "status"=>"error",
	    "msg" => '抱歉，分销不参考此活动'
    ));
    exit;
}


if($do == 'get')
{
	echo json_encode(array(
      "status"=>"end",
      "msg" => '活动已结束'
  ));
  exit;

	// 领取方法
	if(empty($user_id)){
		echo json_encode(array(
	    	"status"=>"nologin",
	    	"msg" => '请登录后领取'
	    ));
	    exit;
	}else{
		$user = $DB->GetRs('users','bag_total,consume_total',"where user_id='{$user_id}'");
		$bag_total = isset($user['bag_total']) ? ($user['bag_total']+50) : 0;
	}

	$bag = $DB->GetRs('bag','bag_id',"where user_id='{$user_id}' and type='prepaid' and `note`='新年红包'");
	if(!empty($bag)){
		echo json_encode(array(
	        "status"=>"geted",
	        "msg" => '你已经领过了'
	    ));
	    exit;
	}

	// 添加帐户流水
	$insData = array(
        "pay_status"=>'paid',
        "pay_sn"=>$Base->build_order_no('B'),
        "method"=>'25boy',
        "user_id"=>$_SESSION["user_id"],
        "create_date"=>date('Y-m-d H:i:s'),
        "money"=>0,
        "plus_price"=>50,
        "type" =>'prepaid',
        "note"=>'新年红包',
        "balance"=>$bag_total,
        "pay_date"=>date('Y-m-d H:i:s'),
        "business_code"=>$Common->getBusinessCodeFrom()
    );
	$DB->Add("bag",$insData);
    //更新用户消费总额
    $res = $DB->Set("users","bag_total={$bag_total},consume_total=consume_total+50","where user_id=".$user_id);
    if($res){
		echo json_encode(array(
	        "status"=>"success",
	        'msg' => '领取成功'
	    ));
	    exit;
	}

	exit;
}

else

{

	// 通过一级id查询子类，或者类型查询两个子类id方法

	$id = isset($_GET['id']) ? intval($_GET['id']) : 57;
	$type = isset($_GET['type']) ? trim($_GET['type']) : '';
	if(empty($id) && empty($type)){
		header("Location: /");
		exit;
	}
	
	$view = array();

	// 通过类型查询两个子类id方法
	if( !empty($type) )
	{
		// 广告图片
		$posPic = array(
			'normal'	=> 58,
			'hea' 		=> 61,
			'ylt' 		=> 63,
			'he75' 		=> 65,
			'gqss' 		=> 67,
			'1626627'	=> 69,
		);
		// 广告列表
		$posList = array(
			'normal'	=> 59,
			'hea' 		=> 62,
			'ylt' 		=> 64,
			'he75' 		=> 66,
			'gqss' 		=> 68,
			'1626627'	=> 70,
		);
		$pic_pos_id  = isset($posPic[$type]) ? $posPic[$type] : $posPic['normal'];
		$list_pos_id = isset($posList[$type]) ? $posList[$type] : $posList['normal'];


		// 红包广告图片
		$adset = $Common->get_picshow($pic_pos_id);
		$prolist = $Common->get_picshow($list_pos_id);


		// 第1个广告位信息
		$pos = $DB->GetRs('position',"pos_id,posname", "where pos_id='{$pic_pos_id}'");
		// 页面seo
		$page_title = $pos['posname'];
		$page_sed_title = $pos['posname'];
	}

	// 通过一级id查询子类
	else
	{
		// 查询广告位
		$pos = $DB->GetRs('position',"pos_id,posname", "where pos_id='{$id}'");
		if(empty($pos)){
			header("Location: /");
			exit;
		}
		// 广告位下子类目
		$rs = $DB->Get('position',"pos_id,posname", "where parent={$id} order by sort asc limit 2");
		$subPos = array();
		while($row = $DB->fetch_assoc($rs)) {
		    $subPos[] = $row;
		}

		// 页面广告
		$adset = $Common->get_picshow($subPos[0]['pos_id']);
		// 商品列表
		$prolist = $Common->get_picshow($subPos[1]['pos_id']);

		// 页面seo
		$page_title = $subPos[0]['posname'];
		$page_sed_title = $subPos[0]['posname'];
	}

	// 模板赋值
	$sm->assign("hide_site_top_banner", true, true);
	$sm->assign("adset", $adset, true);
	$sm->assign("prolist", $prolist, true);
}