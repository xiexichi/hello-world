<?php

namespace app\goods\model;

class GoodsTagBind extends Base
{
    protected $name = 'goods_tag_bind';

    //获取tag绑定状态
    public function getTagBindNum($tag_id){
        return $this->where('tag_id',$tag_id)->value('count(id)');
    }


}
