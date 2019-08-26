<?php

namespace app\goods\model;

class GoodsBrands extends Base
{
    protected $name = 'brands';

    public function getFilterList(){
        $brandWhere = [
            'is_deleted' => ['=',0],
            'brand_letter' => ['<>','']
        ];
        $brand_all = $this->field('id,brand_name,brand_logo,brand_letter')
            ->where($brandWhere)
            ->order(['brand_letter'=>'asc','sort'=>'desc'])
            ->select();
        //字母分组处理
        $brand_list = [];
        foreach( $brand_all as $brand ){
            $letter = strtoupper(trim($brand['brand_letter']));
            if( !isset($brand_list[$letter]) ){
                $brand_list[$letter] = [];
            }
            if( $brand['brand_letter'] != '' ){
                $brand_list[$letter][] = $brand;
            }
        }
        return $brand_list;
    }

    public function addBefore(){
        $this->data['create_time'] = date('Y-m-d H:i:s');
        $this->data['update_time'] = $this->data['create_time'];
        //检查唯一
        if(!$this->checkRepeatBrandsName()){
            return false;
        }
        return true;
    }

    public function editBefore(){
        $this->data['update_time'] = date('Y-m-d H:i:s');
        //检查是否存在
        $info = $this->where("id = {$this->data['id']}")->find();
        if(empty($info)){
            $this->code = 020304;
            $this->isExit = true;
            $this->error = "品牌不存在！";
            return false;
        }
        //检查是否重复
        $where['id'] = ['<>',$this->data['id']];
        if( !$this->checkRepeatBrandsName($where) ){
            return false;
        }
        return true;
    }

    public function deleted($brand_id){
        //检查是否存在
        $where['id'] = $brand_id;
        $info = $this->where($where)->find();
        if (empty($info)) {
            $this->code = 020304;
            $this->error = '删除项不存在';
            return false;
        }
        //检查是否存在商品项绑定
        $goodsModel = new \app\goods\model\Goods();
        $list = $goodsModel->where('brand_id',$brand_id)->find();
        if( !empty($list['list']) ){
            $this->code = 020305;
            $this->error = '存在商品绑定不允许删除';
            return false;
        }
        $updateData['is_deleted'] = 1;
        $updateData['update_time'] = date('Y-m-d H:i:s');
        if (!$this->where($where)->update($updateData)) {
            $this->code = 020310;
            $this->error = '网络错误，删除失败';
            return false;
        }
        return true;
    }

    public function checkRepeatBrandsName($where=[]){
        if(!empty($this->data['brand_name'])){//前置方法使用
            $this->where('brand_name',$this->data['brand_name']);
            $this->where('is_deleted',0);
        }
        if( !empty($where) ){
            foreach( $where as $key => $val ) {
                $this->where($key,$val[0],$val[1]);
            }
        }
        $info = $this->find();
        if($info){
            $this->code = 020302;
            $this->isExit = true;
            $this->error = "品牌已存在！";
            return false;
        }
        return true;
    }

}
