<?php

namespace app\order\model;

class OrderConsignee extends Base
{
    protected $name = 'order_consignee';

    public function getOrderConsignee($order_id){
        return $this->field('oc.*,p.name as prov_name,c.name as city_name,a.name as area_name')
            ->alias('oc')
            ->join('region p','p.id = oc.province_id')
            ->join('region c','c.id = oc.city_id')
            ->join('region a','a.id = oc.area_id')
            ->where('order_id',$order_id)->find();
    }

}
