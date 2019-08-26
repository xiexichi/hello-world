<?php
/**
 * 商品标签
 */
namespace app\goods\controller;

class GoodsTag extends Base
{

    public function deleted(){
        if( !$checkData = $this->validate->deleted(input('post.')) ){
            return errorJson(020601, $this->validate->getError());
        }
        if( !$this->model->deleted($checkData['id']) ){
            return errorJson($this->model->getCode(), $this->model->getError());
        }
        return successJson(true,'删除成功');
    }

}
