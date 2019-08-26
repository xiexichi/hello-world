<?php
namespace app\document\controller;

use think\Request;
use think\File;

class Upload extends Base
{
	// 上传图片,返回图片地址
	public function upload(){
		// return $_FILES;
		$file = request()->file('file');
		if ($file) {
			$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
	        if($info){
	            // $fileData = DS . 'public' . DS . 'uploads/'.$info->getSaveName();
	            $filePath = DS . 'uploads' . DS . $info->getSaveName();
	            $path =  str_replace("\\","/",$filePath);
	            return successJson($path);
	        }else{
	            // 上传失败获取错误信息
	            return errorJson($file->getError());
	        }
		}

		return errorJson("文件上传失败");
	}
}