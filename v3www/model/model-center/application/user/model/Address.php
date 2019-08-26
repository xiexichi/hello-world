<?php
/**
 * 25boy v3 会员地址模块
 */
namespace app\user\model;

class Address extends Base
{
    protected $name = 'user_address';

    public function oneBefore()
    {
        if( isset($this->data['user_id']) && !empty($this->data['user_id']) ){
            $this->where('user_id',$this->data['user_id']);
        }
    }
}
