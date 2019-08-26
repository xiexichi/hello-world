<?php

namespace app\goods\model;

class AttributeCate extends Base
{
    protected $name = 'goods_attr_cate';

    /**
     * 获取商品参数类型列表
     * @param int $page         页码
     * @param int $limit        每页数
     * @param bool $getCount    是否获取总数
     * @return mixed
     */
    public function getAttrCateAll($page=1,$limit=10,$getCount=false){
        $pageSize = ( $page - 1 ) * $limit.','.$limit;
        if($limit=0){
            $pageSize = '';
        }
        $where['is_deleted'] = 0;
        $data['list'] = $this->where($where)
            ->order('sort','desc')
            ->limit($pageSize)
            ->select();
        if($getCount){
            $num = $this->where($where)->value('count(id)');
            if(empty($num)){
                $num = 0;
            }
            $data['count'] = $num;
        }
        return $data;
    }

    public function getFilterList(){
        $goodsModel = new \app\goods\model\Goods();
        $goodsAttrModel = new \app\goods\model\GoodsAttr();
        $attr_all = $goodsAttrModel
            ->field('a.id as attr_id,a.attr_name,ga.attr_value')
            ->alias('ga')
            ->join($this->getTable().' a','a.id = ga.goods_attr_cate_id')
            ->join($goodsModel->getTable().' g','g.id = ga.goods_id')
            ->where([
                'a.is_deleted'=>0,
                'g.is_deleted'=>0,
                'g.verify'=>1,
                'g.sales_status'=>1,
            ])
            ->group('ga.attr_value')
            ->order('a.sort','desc')
            ->select();
        $attr_list = [];
        if( !empty($attr_all) ){
            foreach( $attr_all as $attr ){
                if( !isset($attr_list[$attr['attr_id']]) ){
                    $attr_list[$attr['attr_id']] = [
                        'attr_name' => $attr['attr_name'],
                        'attr_val_list' => []
                    ];
                }
                $attr_list[$attr['attr_id']]['attr_val_list'][] = $attr['attr_value'];
            }
        }
        return $attr_list;
    }

    public function addBefore(){
        // 设置添加时间
        $this->data['create_time'] = date('Y-m-d H:i:s');
        $this->data['update_time'] = $this->data['create_time'];
        if( !$this->checkRepeatAttrName() ){
            return false;
        }
        return true;
    }

    public function editBefore(){
        // 设置添加时间
        $this->data['update_time'] = date('Y-m-d H:i:s');
        //检查是否存在
        $info = $this->where("id = {$this->data['id']}")->find();
        if(empty($info)){
            $this->code = 020504;
            $this->isExit = true;
            $this->error = "设置参数不存在！";
            return false;
        }
        //检查是否重复
        $where['id'] = ['<>',$this->data['id']];
        if( !$this->checkRepeatAttrName($where) ){
            return false;
        }
        return true;
    }

    /**
     * 检查参数名唯一
     * @param array $where  额外条件
     * @return bool
     */
    public function checkRepeatAttrName($where=[]){
        $this->where('is_deleted',0);
        if(!empty($this->data['attr_name'])){//前置方法使用
            $this->where('attr_name',$this->data['attr_name']);
        }
        if( !empty($where) ){
            foreach( $where as $key => $val ) {
                $this->where($key,$val[0],$val[1]);
            }
        }
        $info = $this->find();
        if($info){
            $this->code = 020503;
            $this->isExit = true;
            $this->error = "参数名已存在！";
            return false;
        }
        return true;
    }

    /**
     * 删除商品参数
     * @param $ids
     * @return bool
     */
    public function deleted($ids){
        //检查是否存在
        $where['id'] = $ids;
        $info = $this->where($where)->find();
        if(empty($info)){
            $this->code = 020504;
            $this->error = "参数类型不存在！";
            return false;
        }
        $updateData['is_deleted'] = 1;
        $updateData['update_time'] = date('Y-m-d H:i:s');
        if( !$this->where($where)->update($updateData) ){
            $this->code = 020510;
            $this->error = "网络错误，删除失败！";
            return false;
        }
        return true;
    }

}
