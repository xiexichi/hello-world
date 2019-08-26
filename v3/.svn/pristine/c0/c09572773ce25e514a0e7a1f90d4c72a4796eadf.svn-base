<?php

namespace app\goods\model;

class Goods extends Base
{
    protected $name = 'goods';

    public function goodsStatusDesc($goodsInfo){
        $desc = '';
        if( $goodsInfo['verify'] == 1 ){
            if( $goodsInfo['sales_status'] == 1 ){
                $desc = '上架中';
            }else{
                $desc = '下架中';
            }
        }else{
            $desc = '待审核';
        }
        if( $goodsInfo['is_deleted'] == 1 ){
            $desc = '已删除';
        }
        return $desc;
    }

    public function indexAfter(){
        if(!empty($this->data)){
            foreach( $this->data as $key => $val ){
                unset($val['content']);
                $val['status_desc'] = $this->goodsStatusDesc($val);
                $this->data[$key] = $val;
            }
        }
    }

    public function goodsImages(){
        return $this->hasOne('GoodsImages','goods_id','id');
    }

    public function oneBefore(){
        $this->with('goodsImages')->field('id,goods_name,erp_code,goods_code');
    }

    public function editBefore(){
        $this->data['update_time'] = date('Y-m-d H:i:s');
    }

    //检查商品是否已存在活动信息
    public function checkGoodsActivity(){
        //是否参与赠品活动

    }

}
