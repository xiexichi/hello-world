<?php
/**
 * 晒图评论
 */
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");

// 禁止错误输出
error_reporting(0);




// 数据过滤防（xss）
foreach ($_POST as $k => $v) {
	if(is_array($v)){

	}else{
		$_POST[$k] = htmlspecialchars($v);
	}
}


// 放回json结果
function resultJson($ms_code,$ms_msg,$data = array()){
	echo json_encode(array(
        "ms_code"=>$ms_code,
        'ms_msg'=>$ms_msg,
        'data' => $data
    ));
    exit;
}

// 方法名称
$a = isset($_REQUEST['a'])?$_REQUEST['a']:'';

//检查登录（只有add和zan方法才需要登陆）
if($a == 'add' || $a == 'zan'){
	if(!isset($_SESSION["user_id"])||empty($_SESSION["user_id"])) {
	    resultJson('nologin','请登录后再评论！');
	}	
}

$actions = array('add','zan','get_new','get_more');// 允许执行方法
if(!in_array($a, $actions)){
	resultJson('notaction','执行方法不存在！');
}else{
	// 执行请求方法
	$a();
}

// 表名
$table = 'share';


/*------------------------- 不需要登陆的方法 -----------------------*/
/**
 * [get_new 获取新评论]
 * @return [type] [description]
 */
function get_new(){
	$share_id = $_POST['share_id'];
	$max_id = $_POST['max_id'];
	if(!$share_id){
		resultJson('param_error','缺失share_id参数');
	}
	// 验证评论内容
	if(!$max_id){
		resultJson('param_error','缺失max_id参数');
	}

	$DB = new mysql();
	// 评论sql
	$fileds = 'a.*,b.nickname,b.image_url';
	$shareCommentSql = "SELECT %fields% FROM share_comment a JOIN users b ON a.user_id = b.user_id WHERE a.share_id = '{$share_id}' AND a.is_show = 1 AND a.id > '{$max_id}'";
	// 
	$shareComments = $DB->query(str_replace('%fields%', $fileds, $shareCommentSql));

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
			$shareComment['replys'][] = $reply;
		}

		$comments[] = $shareComment;
	}

	// 返回结果
	resultJson('','has new comments',$comments);
}


/**
 * [get_more 获取更多评论数据]
 * @return [type] [description]
 */
function get_more(){
	$page = post_param('page');
	$share_id = post_param('share_id');
	$order = post_param('order');


	$DB = new mysql();
	// 评论sql
	$fields = 'a.*,b.nickname,b.image_url';
	$shareCommentSql = "SELECT %fields% FROM share_comment a JOIN users b ON a.user_id = b.user_id WHERE a.share_id = '{$share_id}'";

	// 查找总条数
	$res = $DB->query(str_replace("%fields%", "count(*) count", $shareCommentSql));
	if(!$res){
		// 晒图评论不存在
		resultJson('param_error','没有更多数据');
	}

	// 计算分页
	$pagesize = 10;// 每页显示条数
	$count = $DB->fetch_assoc($res)['count'];
	$pageCount = ceil($count / $pagesize);
	// 偏移量
	$offset = ((max(1,$page) - 1) * $pagesize).','.$pagesize;

	// 排序参数
	$orderParam = $order == 1? 'zan' : 'create_time';

	$shareComments = $DB->query(str_replace("%fields%", $fields, $shareCommentSql)." AND share_comment_id IS NULL ORDER BY {$orderParam} DESC LIMIT {$offset}");

	if(!$shareComments){
		// 晒图评论不存在
		resultJson('param_error','没有更多数据');
	}

	// 保存评论数据
	$comments = array();
	while ($shareComment = $DB->fetch_assoc($shareComments)) {
		$shareComment['create_time'] = date('m-d H:i',strtotime($shareComment['create_time']));
		if(!$shareComment['image_url']){
			$shareComment['image_url'] = '/statics/img/user_default_icon.png';
		}
		$shareComment['replys'] = array();
		// 查找评论的回复
		$replys = $DB->query(str_replace('%fields%', $fields, $shareCommentSql).' AND share_comment_id = '.$shareComment['id']);
		while ($reply = $DB->fetch_assoc($replys)) {
			$reply['create_time'] = date('m-d H:i',strtotime($reply['create_time']));
			$shareComment['replys'][] = $reply;
		}

		$comments[] = $shareComment;
	}


	// 返回数据
	resultJson('','',$comments);
}


/*------------------------- 不需要登陆的方法 -----------------------*/


/*------------------------- 需要登陆的方法 -----------------------*/
/**
 * [addComment 添加评论]
 */
function add(){
	$share_id = post_param('share_id');
	$comment = post_param('comment');

	// 验证评论长度
	$limit = 140;
	if(mb_strlen($comment) > $limit){
		resultJson('param_error','评论字数不能大于'.$limit.'个文字');
	}

	// 验证评论内容是否非法
	$checkChars = array('网址','网站','www','.com','.cn','.org','.net','屌','叼','你妈','你爸','老母','老豆','妈的');
	$isIllegal = false;
	$illegalStr = "";
	foreach ($checkChars as $k => $v) {
		if(strpos($comment, $v) > -1){
			$isIllegal = true;
			$illegalStr = $v;
			break;
		}
	}
	if($isIllegal){
		resultJson('param_error',"评论中存在非法字符 '{$illegalStr}'");
	}

	// 数据库
	$DB = new mysql();

	// 验证评论时间间隔
	/*$lastComment = $DB->GetRs('share_comment','*','WHERE user_id = '.$_SESSION["user_id"].' ORDER BY id DESC');
	if($lastComment){
		if(($time = time() - strtotime($lastComment['create_time'])) < 60){
			resultJson('param_error',(60-$time)."秒后才能继续评论");
		}
	}*/

	// 查找share_id是否存在
	$row = $DB->GetRs('share','share_id,user_id','WHERE share_id='.$share_id);
	if(!$row){
		// 没有
		resultJson('param_error',"晒图信息不存在");
	}else{
		$data = array(
			'share_id' => $share_id,
			'comment'  => $comment,
			'user_id'  => $_SESSION["user_id"],
			'create_time' => date('Y-m-d H:i:s')
		);
		// 回复评论id
		if(isset($_POST['share_comment_id'])){
			$shareComment = $DB->GetRs('share_comment','id,user_id','WHERE id='.$_POST['share_comment_id']);
			if($shareComment){
				$data['share_comment_id'] = $shareComment['id'];
			}
		}

		// 添加评论数据	
		if($DB->Add('share_comment',$data)){
			$addId = $DB->insert_id();
			// 查找评论数据
			$shareCommentSql = "SELECT a.*,b.nickname,b.image_url FROM share_comment a JOIN users b ON a.user_id = b.user_id WHERE a.id = {$addId}";
			$res = $DB->query($shareCommentSql);
			$data = array();
			if($res){
				$data = $DB->fetch_assoc($res);
				$data['create_time'] = date('m-d H:i',strtotime($data['create_time']));
				if(!$data['image_url']){
					$data['image_url'] = '/statics/img/user_default_icon.png';
				}
			}

			// 判断是评论还是回复
			if(isset($_POST['share_comment_id'])){
				// 回复
				$msg = "<a href='http://m.25boy.cn/?m=share&a=view&id={$row['share_id']}'>@".$_SESSION['nickname'].'：回复了你的晒图评论，赶紧去看看吧！</a>';
				send_user_inbox($shareComment['user_id'],$msg,$DB);
			}else{
				// 评论
				$msg = "<a href='http://m.25boy.cn/?m=share&a=view&id={$row['share_id']}'>@".$_SESSION['nickname'].'：评论了你的晒图，赶紧去看看吧！</a>';
				send_user_inbox($row['user_id'],$msg,$DB);
			}

			resultJson('',"添加评论成功",$data);
		}else{
			resultJson('add_error',"添加评论失败");
		}
	}

}


/**
 * [zan 评论点赞]
 * @return [type] [description]
 */
function zan(){

	// 数据
	$share_id = post_param('share_id');
	$share_comment_id = post_param('share_comment_id');

	// 数据库
	$DB = new mysql();

	// 查找是否已经点赞
	$isZan = $DB->GetRs('share_comment_zan','id','WHERE user_id = '.$_SESSION['user_id'].' AND share_comment_id = '.$share_comment_id);
	if($isZan){
		resultJson('error','只能赞一次啊亲！');
	}


	// 验证评论是否存在
	$row = $DB->GetRs('share_comment','id,zan','WHERE share_id = '.$share_id.' AND id = '.$share_comment_id);	
	if($row){

		$res = $DB->Set('share_comment',array('zan'=>$row['zan']+1),'WHERE id = '.$row['id']);
		if($res){
			// 记录点赞信息
			$DB->Add('share_comment_zan',array('user_id'=>$_SESSION['user_id'],'share_comment_id'=>$share_comment_id));

			resultJson('','点赞成功');
		}else{
			resultJson('error','点赞失败');
		}
	}else{
		resultJson('nodata','晒图评论不存在');
	}
}

/*------------------------- 需要登陆的方法 -----------------------*/

/**
 * [get_param 获取提交参数]
 * @param  [type] $key [description]
 * @return [type]      [description]
 */
function get_param($key){
	if(isset($_GET[$key]) && $_GET[$key]){
		return htmlspecialchars($_GET[$key]);
	}else{
		resultJson('param_error','缺失参数'.$key);
	}
}

/**
 * [post_param 获取提交参数]
 * @param  [type] $key [description]
 * @return [type]      [description]
 */
function post_param($key){
	if(isset($_POST[$key]) && $_POST[$key]){
		return htmlspecialchars($_POST[$key]);
	}else{
		resultJson('param_error','缺失参数'.$key);
	}
}

/**
 * [send_user_inbox 发送用户站内信]
 * @param  [type] $user_id [接收用户id]
 * @param  string $msg     [发送消息]
 * @param  object $db      [数据库操作对象]
 * @return [type]          [description]
 */
function send_user_inbox($user_id,$msg,$db){
	$data = array(
		'send_id' => 0,
		'revice_id' => $user_id,
		'msg' => addslashes($msg),
		'send_date' => date('Y-m-d H:i:s')
	);
	$db->Add('inbox',$data);
}