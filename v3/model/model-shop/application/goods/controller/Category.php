<?php
/**
 * 商品分类
 */
namespace app\goods\controller;
use think\Db;

class Category extends Base
{

    //获取所有分类
    public function getCateAll(){
        if( !$checkDate = $this->validate->getCateAll(input()) ){
            return errorJson(200201, $this->validate->getError());
        }
        $list = $this->model->getCateAll($checkDate);
        return successJson($list);
    }

    public function checkRepeatCateName(){
        $param = input('post.','');
        if( empty($param['cate_name']) ){
            return errorJson(200201, '参数错误');
        }
        if( isset($param['id']) ){
            $where['id'] = ['<>',$param['id']];
        }
        $where['is_deleted'] = ['=',0];
        $where['cate_name'] = ['=',$param['cate_name']];
        $msg = '不存在';
        if( $check_status = !$this->model->checkRepeatCateName($where) ){
            $msg = '已存在';
        }
        return successJson($check_status,$msg);
    }
}
