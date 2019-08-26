<?php
namespace app\merchant\model;

class Admin extends Base
{
    // 自动时间
    protected $autoWriteTimestamp = 'datetime';

    protected $table = 'admin';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';


}
