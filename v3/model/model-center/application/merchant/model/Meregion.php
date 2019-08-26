<?php
namespace app\merchant\model;

use \think\Db;

class Meregion extends Base
{
    // 自动时间
    protected $autoWriteTimestamp = 'datetime';

    protected $table = 'merchant_region';
    protected $createTime = 'create_time';

    public function indexBefore(){
           $this->alias('mr')
           ->join('merchant_region_admin mra','mr.id = mra.region_id')
           ->join('admin a','mra.admin_id = a.id')
           ->join('merchant_region_shop mrs','mr.id = mrs.region_id')
           ->join('shop s','mrs.shop_id = s.id')
           ->field('mr.id , mr.region_name , s.name , a.loginname');
}





}
