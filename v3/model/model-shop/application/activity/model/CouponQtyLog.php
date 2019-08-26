<?php

namespace app\activity\model;

class CouponQtyLog extends Base
{
    protected $name = 'coupon_qty_log';

    public function markLog($qty,$couponInfo){
        $data = [
            'coupon_id' => $couponInfo['id'],
            'qty' => bcsub($qty,$couponInfo['qty'],0),
            'before_qty' => $couponInfo['qty'],
            'after_qty' => $qty,
            'create_time' => date('Y-m-d H:i:s')
        ];
        return $this->insert($data);
    }
}
