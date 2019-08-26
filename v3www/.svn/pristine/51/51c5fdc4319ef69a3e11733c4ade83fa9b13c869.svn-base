<?php
/**
 * 商品属性值
 */
namespace app\goods\controller;

class GoodsPropVal extends Base
{
    //获取所有标签列表
    public function getPropValAll(){
        if( !$checkData = $this->validate->getPropValAll(input('get.')) ){
            return errorJson(020401, $this->validate->getError());
        }
        $list = $this->model->getPropValAll($checkData['prop_id'],$checkData['page'],$checkData['limit'],true);
        return successJson($list['list'],'',$list['count']);
    }

    public function deleted(){
        if( !$checkData = $this->validate->getPropValInfo(input('post.')) ){
            return errorJson(020401, $this->validate->getError());
        }
        if( !$this->model->deleted($checkData['id']) ){
            return errorJson($this->model->getCode(), $this->model->getError());
        }
        return successJson(true,'删除成功');
    }
}
