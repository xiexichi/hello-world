<?php
$share_id = isset($_GET['id'])?(int)$_GET['id']:0;
$info = array();
$object = $DB->GetRs("v_share","*","where status=1 AND share_id = ".$share_id);
if(!isset($object['share_id']) || !$object['share_id']){
	$Error->show('页面不存在，或已经删除','访问错误');
	exit;
}

$page_title = "25BOY晒图有礼 - 送EVISU牛仔裤";
$page_sed_title = '';
$seo_desc = $object['content']?$object['content']:$seo_desc;
$sm->assign("goback", '/?m=share', true);

$object['time'] = date('Y-m-d',strtotime($object['date_added']));
$object['avatar'] = $Base->site_img($object['userimg']);
$object['photos'] = unserialize($object['photos']);
$sm->assign("object", $object, true);

// 相关达人晒图
$moreshare = array();
$CKey = 'moreshare_'.$share_id;
$resultCache = $Cache -> get($CKey);
if (is_null($resultCache)){
	$Row = $DB->Get("v_share","*","where status=1 AND share_id<>".$object['share_id']." order by sort desc, date_added desc",9);
	$Row = $DB->result;
	$RowCount = $DB->num_rows($Row);
	if($RowCount!=0){
	    while($result = $DB->fetch_assoc($Row)){
	    	$result['photos'] = unserialize($result['photos']);
	    	$result['img_url'] = $Base->site_img($result['photos'][0]);
	    	$result['userimg'] = $Base->site_img($result['userimg']);
	        $moreshare[] = $result;
	    }
	}
	$Cache->set($CKey, $moreshare, 900);
}else{
    $moreshare = $resultCache;
}

// 是否显示分享提示
$show_wx_share_div = false;
if(isset($_COOKIE['show_wx_share_div'])){
	$show_wx_share_div = false;
}

/*------------晒图评论2017-06-29------------*/
// 评论sql
$fileds = 'a.*,b.nickname,b.image_url';
$shareCommentSql = "SELECT %fields% FROM share_comment a JOIN users b ON a.user_id = b.user_id WHERE a.share_id = {$share_id} AND a.is_show = 1";

// 排序规则
$order = 'zan';
if(isset($_GET['order'])){
	$order = $_GET['order'] == "1" ? 'zan' : 'create_time';
	$sm->assign('order',$_GET['order']);
}else{
	$sm->assign('order',1);
}

// 因为目前这个版本预计的评论不会太多，所以不做分页处理
$shareComments = $DB->query(str_replace('%fields%', $fileds, $shareCommentSql).' AND share_comment_id is null ORDER BY '.$order.' DESC,create_time DESC LIMIT 10');

// 保存评论数据
$comments = array();
while ($shareComment = $DB->fetch_assoc($shareComments)) {
	$shareComment['create_time'] = date('m-d H:i',strtotime($shareComment['create_time']));
	if(!$shareComment['image_url']){
		$shareComment['image_url'] = '/statics/img/user_default_icon.png';
	}
	$shareComment['replys'] = array();
	// 查找评论的回复
	$replys = $DB->query(str_replace('%fields%', $fileds, $shareCommentSql).' AND share_comment_id = '.$shareComment['id']);
	while ($reply = $DB->fetch_assoc($replys)) {
		$reply['create_time'] = date('m-d H:i',strtotime($reply['create_time']));
		$shareComment['replys'][] = $reply;
	}

	$comments[] = $shareComment;
}
// 分配评论数据
$sm->assign('comments',$comments,true);


// 查找评论总数
$commentCount = $DB->query(str_replace('%fields%', 'count(*) count', $shareCommentSql));
$count = 0;
if($commentCount){
	$count = $DB->fetch_assoc($commentCount)['count'];
}
$sm->assign('comment_count',$count,true);

// 不使用smarty缓存
$sm->caching = false;

// 查找最大id
$res = $DB->GetRs('share_comment','max(id) max_id','WHERE share_id = '.$share_id);
$maxId = 0;
if($res){
	$maxId = $res['max_id'];
}
$sm->assign("maxId",$maxId);

/*------------晒图评论2017-06-29------------*/


// 微信分享
$wxconfigarray = array(
	'title' => "#晒图有礼# 来自".$object['username']."的分享！",	// #25BOY晒图# 看看潮人如何穿着吧！
	'link' => "http://m.25boy.cn/?m=share&a=view&id={$object['share_id']}",
	'imgUrl' => $object['photos'][0],
	'desc' => '晒图送EVISU牛仔裤，25BOY国潮男装',
);
if(isset($promote['promote_id'])){
    $PID = $Base->myEncode($promote['promote_id']);
    $wxconfigarray['link'] .= "&PI={$PID}";
}

// print_r($wxconfigarray);
$sm->assign("show_wx_share_div", $show_wx_share_div, true);
$sm->assign("moreshare", $moreshare, true);


// 阅读 +1
$DB->Set('share', "click=click+1", "where share_id=" . $share_id );


