<?php

namespace app\goods\model;

use app\common\model\CommonModel;

class Base extends CommonModel
{
    public function setGoodsPriceLog($goods_id=0,$item_id=0,$old_price,$new_price,$msg,$t=''){
        if(empty($goods_id)){
            return false;
        }
        if( $t == '' ){
            $t = date('Y-m-d H:i:s');
        }
        $admin_id = 0;
        if( !$this->table('goods_price_log')->insert([
            'goods_id' => $goods_id,
            'goods_item_id' => $item_id,
            'old_price' => bcadd($old_price,0,2),
            'new_price' => bcadd($new_price,0,2),
            'remark' => $msg,
            'admin_id' => empty($admin_id) ? 0 : $admin_id,
            'ip'    => Request()->ip(),
            'create_time' => $t
        ]) ){
            return false;
        }
        return true;
    }
}
