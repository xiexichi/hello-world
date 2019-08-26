<?php
namespace app\user\model;

class UserIntegral extends Base
{
    // 自动时间
    protected $autoWriteTimestamp = 'datetime';
    protected $table = 'user_integral';
    protected $createTime = 'create_time';
   
    
}