<?php

namespace app\order\model;

class OrderReturnLog extends Base
{
    protected $name = 'order_return_log';

    const OPERATOR_TYPE_USER = 0;
    const OPERATOR_TYPE_ADMIN = 1;

    public function markLog($operator_id,$order_id,$content,$operator_type=SELF::OPERATOR_TYPE_USER,$t=''){
        if( empty($t) ) {
            $t = date('Y-m-d H:i:s');
        }
        $insertData = [];
        $insertData['operator_id'] = $operator_id;
        $insertData['operator_type'] = $operator_type;
        $insertData['order_return_id'] = $order_id;
        $insertData['content'] = $content;
        $insertData['ip'] = Request()->ip();
        $insertData['create_time'] = $t;
        if( !$this->insert($insertData) ){
            return false;
        }
        return true;
    }

}
