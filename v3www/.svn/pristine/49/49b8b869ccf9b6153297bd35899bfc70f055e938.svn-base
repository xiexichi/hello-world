<?php

namespace app\article\controller;

use think\Exception;
use think\db;

class Article extends Base
{
	// 文章名是否重复
    public function checkRepeatArticleName(){
        $param = input("post.");
        $checkData = $this->validate->checkRepeatArticleName($param);
        if( $checkData === false ){
            return errorJson(020101, $this->validate->getError());
        }

        if( isset($checkData['id']) ){
            $where['id'] = ['<>',$checkData['id']];
        }
        $where['title'] = ['=',$checkData['title']];

        return successJson(!$this->model->checkRepeatArticleName($where));

    }
}	