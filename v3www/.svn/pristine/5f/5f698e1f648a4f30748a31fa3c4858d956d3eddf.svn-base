<?php

namespace app\document\model;

class ShopDocument extends Base
{
    protected $name = 'shop_document';


    public function indexBefore(){

        // pe($this->data);
        if (!empty($this->data['shop_id'])) {
            $this->where('d.shop_id',$this->data['shop_id']);
        }

        if (!empty($this->data)) {
            $this->alias('d')
                ->field('d.*,s.name as shop_name')
                ->join('shop s','d.shop_id = s.id');
        }
    }


    public function addBefore(){
        // pe($this->data);
    	$this->data['create_time'] = date('Y-m-d H:i:s');
    	$this->data['update_time'] = $this->data['create_time'];
    	return true;
    }

    public function editBefore(){
        // pe($this->data);
        $this->data['update_time'] = date('Y-m-d H:i:s');
        return true;
    }

}
