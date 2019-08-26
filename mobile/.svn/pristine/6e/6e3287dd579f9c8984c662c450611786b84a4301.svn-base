<?php
/*
* 删除WAP手机版缓存文件
* $key PC执行同步密钥
* http://www.25boy.cn/admin/cache/delete_mobile
*/
$key = isset($_GET['k'])?trim($_GET['k']):NULL;
if(empty($key)) return false;

$synckey = '25miaocache';
$enkey = sha1(md5($synckey));
if($key == $enkey){

	$arr = array('css','js','smarty','system','view','images');

	$i = 0;
	foreach ($arr as $folder) {
		$cache_path = $_SERVER['DOCUMENT_ROOT'].'/cache/'.$folder.'/';

	    if ( ! is_dir($cache_path))
	    {
	        $return  = array(
	                'status' => 'error',
	                'msg' => "无法读取手机版缓存文件: ". $folder
	        );
	    }else{

	        if($cache_path){
	            // $cache_path = $_SERVER['DOCUMENT_ROOT'].'/'.$cache_path;
	            $Dir = opendir( $cache_path ) or die('打开目录失败');     //打开目录
	            while( $file = readdir( $Dir ) ){   //循环读取目录中
	                if ( $file != '.' && $file != '..' && $file != '.svn' ) {
	                    // echo $file."<br/>";
	                    if($file != '.htaccess' && $file != 'index.html'){
	                        @unlink( $cache_path . '/' . $file );    //删除文件 
	                        $i++;    
	                    }
	                }  
	            }
	        }

	        $return  = array(
	                'status' => 'success',
	                'msg' => "成功删除 $i 个手机版缓存文件！"
	        );
	    }
	}

    echo json_encode($return);
    exit;
}