<?php

namespace app\article\model;
use think\Db;
use app\article\model\Goods;
class Special extends Base
{

    protected $name = 'article_special';

    public function editBefore(){

        $goodsModel = new \app\article\model\SpecialGoods();
        if (!empty($this->data['id'])) {
            $id = $this->data['id'];
        }
        
        $delOld = $goodsModel->where('article_id',$id)->delete();

        if (!empty($this->data['goods_list'])) {
            $newGoods = [];
            // $delOld = $goodsModel->where('article_id',$id)->delete();
            foreach ($this->data['goods_list'] as $k => $v) {
               $newGoods['article_id'] = $id;
               $newGoods['goods_id'] = $this->data['goods_list'][$k];
               $goodsModel->insert($newGoods);
            }
        }

        $this->data['update_time'] = date('Y-m-d H:i:s');
        return true;
    }

    public function addBefore(){
        $this->data['create_time'] = date('Y-m-d H:i:s');
        $this->data['update_time'] = $this->data['create_time'];
        return true;
    }

    public function addAfter(){
        $info = [];
        if (!empty($this->data['goods_list'])) {
            $goodsModel = new \app\article\model\SpecialGoods(); 
            foreach ($this->data['goods_list'] as $k => $v) {
                $info['article_id'] = $this->data['id'];
                $info['goods_id'] = $this->data['goods_list'][$k];
                $goodsModel->insert($info);
            }
        }
    }


    public function indexBefore(){
        if (!empty($this->data['title'])) {
            $this->where('title','like',"%{$this->data['title']}%");
        }
        if (!empty($this->data['categorys_id'])) {
            $this->where('categorys_id',$this->data['categorys_id']);
        }

        // $cate = new \app\article\model\Categorys();
        $cate = new \app\article\model\SpecialCategorys();
        if (!empty($this->data)) {
            $this->alias('s')
                ->field('s.*,cate.name as cate_name,(select count(id) from article_special_goods where article_id = s.id) as sum')
                ->join($cate->getTable().' cate','s.categorys_id = cate.id');
        }

    }

    public function oneBefore(){
        if (!empty($this->data['ids'])) {
            $this->where('id',$this->data['ids']);
        }

        $this->with('specialGoods');
    }


    //专题关联商品详情
    public function specialGoods(){
        $goodsModel = new \app\goods\model\Goods();
        $goodsImageModel = new \app\goods\model\GoodsImages();
        return $this->hasMany('SpecialGoods','article_id')
                    ->field('img.image,g.goods_name,g.id as goods_id,article_id')
                    ->join($goodsModel->getTable() . ' g', 'g.id = goods_id')
                    ->join($goodsImageModel->getTable().' img','img.goods_id = g.id');
    }


    public function checkSpecialName($where=[]){
        if (!empty($this->data['title'])) {
            $this->where('title',$this->data['title']);
        }
        if(!empty($where) ){
            foreach( $where as $key => $val ) {
                $this->where($key,$val[0],$val[1]);
            }
        }

        $info = $this->find();
        if($info){
            $this->code = 200402;
            $this->isExit = true;
            $this->error = "专题名已存在！";
            return false;
        }
        return true;
    }

}
