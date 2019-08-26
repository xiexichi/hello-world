<?php
namespace app\merchant\model;

class Merchantregionadmin extends Base
{
    // 自动时间
    protected $autoWriteTimestamp = 'datetime';

    protected $table = 'merchant_region_admin';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';


}
