<?php

namespace app\goods\model;
use think\Db;
use think\Exception;

class AttributeGroupVal extends Base
{
    protected $name = 'goods_attr_group_val';

    /**
     * 获取参数组的参数成员
     * @param $group_id
     * @return array|bool|false|\PDOStatement|string|Model
     */
    public function getGroupVals($group_id){
        $attr_list = $this->field('goods_attr_cate_id')
            ->where('goods_attr_group_id',$group_id)
            ->select();
        $list = [];
        if(!empty($attr_list)){
            foreach( $attr_list as $key => $val ){
                $list[] = $val['goods_attr_cate_id'];
            }
        }
        return $list;
    }

    /**
     * 保存参数组成员
     * @param $group_id     参数组id
     * @param array $valData   参数组成员集 [goods_attr_cate_id,goods_attr_cate_id]
     * @return bool
     */
    public function saveGroupVals($group_id,$valData=[]){
        if(empty($group_id)){
            return false;
        }
        //初始化成员组
        $attr_list = $this->getGroupVals($group_id);//获取原成员组
        if( !empty($attr_list) ){
            if( !$this->where('goods_attr_group_id',$group_id)->delete() ){
                return false;
            }
        }
        if(!empty($valData)){
            $insertData = [];
            foreach( $valData as $key => $val ){
                $insertData[] =[
                    'goods_attr_group_id'   => $group_id,
                    'goods_attr_cate_id'    => $val
                ];
            }
            if( !$this->insertAll($insertData) ){
                return false;
            }
        }
        return true;
    }
}
