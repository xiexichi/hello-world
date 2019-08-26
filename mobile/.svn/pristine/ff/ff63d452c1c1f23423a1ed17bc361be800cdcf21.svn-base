<?php
include_once($_SERVER['DOCUMENT_ROOT']."/config.php");
include_once($_SERVER['DOCUMENT_ROOT']."/class/upyun.php");
include_once($_SERVER['DOCUMENT_ROOT']."/class/image.php");
include_once($_SERVER['DOCUMENT_ROOT']."/class/Space.php");

//检查登录
if(!isset($_SESSION["user_id"])||$_SESSION["user_id"]=="") {
    echo json_encode(array(
        "ms_code"=>'nologin',
        'ms_msg'=>'你还没有登录，请登录后操作',
    ));
    exit;
}
$upyun = new Upyun();
$Space = new Space();

$act = isset($_POST['act'])?trim($_POST['act']):'';
$tree_id = 535; //图片空间目录id

// 上传图片
if($act == 'up'){
    $data = $result = array();
    $path = '/'.$tree_id;
    $length = 0;
    $result['tree_id'] = $tree_id;
    $maxsize = 5242880; // 最大上传5mb 字节

    foreach($_FILES as $file)
    {
        $result = array();
        $result['ms_code'] = 1;

        $fileInfo = @getimagesize($file['tmp_name']);
        if ($fileInfo["mime"] == 'image/png')
            $result['filename'] .= '.png';
        else if ($fileInfo["mime"] == 'image/gif')
            $result['filename'] .= '.gif';
        else if ($fileInfo["mime"] == 'image/jpeg')
            $result['filename'] .= '.jpg';
        else
        {
            $result['ms_code'] = 0;
            $result['ms_msg'] = '图片只能是JPG,PNG或GIF格式，或者超出5MB限制！<br>iphone6S的LIVE图暂不支持上传，要关闭LIVE功能再拍照哦。';
        }

        if($file['size'] > $maxsize){
            $result['ms_code'] = 0;
            $result['ms_msg'] = '文件大小超出限制，单个最大5MB';
        }


        if ($length < 5 && $result['ms_code']==1) {
            $result['tree_id'] = $tree_id;
            $result['text'] = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file['name']);
            $result['text'] = mb_strlen($result['text'],'utf-8')>15 ? mb_substr($result['text'], 0, 15, 'utf-8') : $result['text'];
            $result['file_id'] = md5($tree_id . md5_file($file['tmp_name']));
            $result['filename'] = time();
            sleep(1);

            if ($Space->chkFile($result['file_id']))
            {
                $fh = fopen($file['tmp_name'], 'r');
                $tmp = $upyun->writeFile($path.'/'.$result['filename'], $fh, True);
                fclose($fh);
                if ($tmp)
                {
                    $result['file_type'] = $tmp['x-upyun-file-type'];
                    $result['file_width'] = $tmp['x-upyun-width'];
                    $result['file_height'] = $tmp['x-upyun-height'];
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

            $result['dir'] = $result['tree_id'];
            unset($result['tree_id']);
        }
        
        $data[] = $result;
        $length++;
    }
    echo json_encode($data);
    exit;

// 删除图片
}else if($act == 'rm'){

    $file_id =$_POST['file_id'];
    $item = $Space->getFile($file_id,$tree_id);
    $result = array('ms_code'=>0);
    $result['ms_msg'] = '删除图片失败';

    if(isset($item['filename']) && $item['filename']){
        $del_path = '/'.$tree_id.'/'.$item['filename'];
        $rsp = $upyun->deleteFile($del_path);
        $Space->rmClear($file_id);
        $result = array('ms_code'=>1);
        $result['ms_msg'] = '删除成功';
    }

    echo json_encode($result);
    exit;

}
