<?php

namespace app\goods\model;

class GoodsAttr extends Base
{
    protected $name = 'goods_attr';

    /**
     *  创建商品设置商品参数绑定
     * @param $goods_id     主商品id
     * @param $attrData     参数集 array('参数id'=>'参数值')
     * @return bool
     */
    public function setBind($goods_id,$attrData){
        if( empty($goods_id) || empty($attrData) ){
            return false;
        }
        $where['goods_id'] = $goods_id;
        //获取参数列表
        $attr_list = $this->where($where)->select();
        if( empty($attr_list) ){//数据插入操作
            $insertData = [];
            foreach( $attrData as $attr_id => $attr_val ){
                $insertData[] =[
                    'goods_id'              => $goods_id,
                    'goods_attr_cate_id'    => $attr_id,
                    'attr_value'            => $attr_val
                ];
            }
            if( !$this->insertAll($insertData) ){
                return false;
            }
        }else{//更新信息
            foreach( $attr_list as $key => $val ){
                $where['goods_attr_cate_id'] = $val['goods_attr_cate_id'];
                if( isset($attrData[$val['goods_attr_cate_id']]) ){//已存在
                    if( $attrData[$val['goods_attr_cate_id']] != $val['attr_value'] ){
                        $updateData = [];
                        $updateData['attr_value'] = $attrData[$val['goods_attr_cate_id']];
                        if( !$this->where($where)->update($updateData) ){
                            return false;
                        }
                    }
                    unset($attrData[$val['goods_attr_cate_id']]);
                    unset($attr_list[$key]);
                }else{//移除不存在项
                    if( !$this->where($where)->delete() ){
                        return false;
                    }
                }
            }
            //处理新增项
            $insertData = [];
            foreach( $attrData as $attr_id => $attr_val ){
                $insertData[] =[
                    'goods_id'              => $goods_id,
                    'goods_attr_cate_id'    => $attr_id,
                    'attr_value'            => $attr_val
                ];
            }
            if( !empty($insertData) ){
                if( !$this->insertAll($insertData) ){
                    return false;
                }
            }
        }
        return true;
    }
}
