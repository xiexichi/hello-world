<?php
namespace app\merchant\model;

class AdminShop extends Base
{
	// 自动时间
	protected $autoWriteTimestamp = 'datetime';

	protected $table = 'admin_shop';
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';
	
	public function bindingAdminShops($adminID,$shopIDArr){
	    $data = [];
        foreach ($shopIDArr as $v){
            $data[] = ['admin_id'=>$adminID,'shop_id'=>$v];
        }
        $this->data($data);
        if(!$this->addAll()){
            return false;
        }
	    return true;
	}
	
	public function removeAllAdminShopsByAdminID($adminID){
	    return $this->where('admin_id',$adminID)->delete();
	}
}