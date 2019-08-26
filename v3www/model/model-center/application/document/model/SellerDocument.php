<?php

namespace app\document\model;

class SellerDocument extends Base
{
    protected $name = 'seller_document';

    // 列表信息
    public function indexBefore(){
        // pe($this->data);
        if (!empty($this->data['seller_id'])) {
            $this->where('d.seller_id',$this->data['seller_id']);
        }
        //会员列表
        if (!empty($this->data)) {
            $this->alias('d')
                ->field('d.*,u.user_name as seller_name')
                ->join('seller s','d.seller_id = s.id')
                ->join('user u','u.id = s.user_id');
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
