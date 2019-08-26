<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
//检查登录
if(!isset($_SESSION["user_id"])||empty($_SESSION["user_id"])) {
    echo json_encode(array(
        "ms_code"=>'nologin',
        'ms_msg'=>'你还没有登录，请登录后操作',
    ));
    exit;
}

$content = isset($_POST['content'])?trim($_POST['content']):'';
$simg = isset($_POST['simg'])?$_POST['simg']:'';
$iswx = isset($_POST['iswx'])?intval($_POST['iswx']):0;
$content = str_replace("'", '&acute;', htmlentities($content));

if(empty($content) || empty($simg)){
    echo json_encode(array(
        "ms_code"=>0,
        'ms_msg'=>'内容为空，或者没有上传图片，请检查内容后提交。',
    ));
    exit;
}

// 微信jssdk提交，处理图片
if( $iswx == 1){

    // 引入图片空间类
    include_once($_SERVER['DOCUMENT_ROOT']."/class/upyun.php");
    include_once($_SERVER['DOCUMENT_ROOT']."/class/Space.php");
    $upyun = new Upyun();
    $Space = new Space();

    // 获取access_token
    require_once $_SERVER['DOCUMENT_ROOT']."/pay/wx/WxPay.JsApiPay.php";
    $tools = new JsApiPay();//实例微信的jsApi
    $access_token =$tools->getAccessToken();


    $wxImages = explode(',',$simg);
    $simg = array();
    $error = array();
    foreach ($wxImages as $key => $val) {
        $result = array();
        $output = '';

        // 根据media_id下载文件到服务器
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$access_token}&media_id={$val}");
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_TIMEOUT,60);
        $output = curl_exec($ch);
        $rinfo=curl_getinfo($ch);
        curl_close($ch);
        // print_r($rinfo);
        if( isset($rinfo['content_type']) && $rinfo['content_type']=='image/jpeg' ){
            // 上传到图片空间
            $tree_id = 535;
            $result['text'] = $val;
            $result['tree_id'] = $tree_id;
            $result['file_id'] = md5($val);
            $result['filename'] = time().'.jpg';
            
            // 不存在则上传
            if ($Space->chkFile($result['file_id']))
            {
                $tmp = $upyun->writeFile("/{$tree_id}/".$result['filename'], $output, True);
                if ($tmp)
                {
                    $result['file_type'] = isset($tmp['x-upyun-file-type'])?$tmp['x-upyun-file-type']:'JPEG';
                    $result['file_width'] = isset($tmp['x-upyun-width'])?$tmp['x-upyun-width']:0;
                    $result['file_height'] = isset($tmp['x-upyun-height'])?$tmp['x-upyun-height']:0;
                    $Space->addFile($result['file_id'],
                        $result['filename'],
                        $result['tree_id'],
                        $result['text'],
                        $result['file_type'],
                        $result['file_width'],
                        $result['file_height']);
                }
            }else{
                $tmp = $Space->getFile($result['file_id']);
                $result['filename'] = $tmp['filename'];
                $result['dir'] = $tmp['tree_id'];
                $result['ms_code'] = 2;
                // $result['ms_msg'] = '该目录已存在相同文件';
            }
            $result['dir'] = $tree_id;
            // print_r($result);
            $simg[] = "http://img.25miao.com/{$result['dir']}/{$result['filename']}";
        }else{
            $error[] = 0;
        }
    }
    // 失败提示
    if( count($error) > 0){
        echo json_encode(array(
            "ms_code"=>0,
            'ms_msg'=>count($error).'张图片上传失败，请重试。',
        ));
        exit;
    }
    // print_r($simg);
    $photos['simg'] = $simg;
}else{
    parse_str($simg,$photos);
}


$user = $DB->GetRs('users','user_id,nickname,image_url','WHERE user_id='.$_SESSION['user_id']);
$formdata = array(
    "content"=>$content,
    "photos"=>serialize($photos['simg']),
    "status"=>"1",
    "user_id"=>$user["user_id"],
    "username"=>$user["nickname"],
    "userimg"=>$user["image_url"],
    "date_added"=>date('Y-m-d H:i:s'),
    "share_sort"=>date('Ym'),  // 按月排序
);
if($user['user_id'] == "17593"){
    $formdata['status'] = 0;
}
$result = $DB->Add('share',$formdata);
$lastid = $DB->insert_id();
if($lastid){

    $ms_msg = '<p>分享给朋友一起参与吧！</p>';
    
    // 发放代金券
    $coupon_pws = $Common->used_coupon(20,$_SESSION['user_id']);
    if($coupon_pws){
        $ms_msg = '<p style="font-size:1.6em;padding:5px 0">恭喜您，获得20元代金券！</p><p style="font-size:12px;">(已发放到您的帐户)</p><p style="font-size:1.6em;padding:5px 0;color:orange;">分享到朋友圈叫朋友来投票</p>';
    }

    echo json_encode(array(
        "ms_code"=>1,
        'ms_msg'=>$ms_msg,
        'lastid'=>$lastid,
    ));
    exit;
}