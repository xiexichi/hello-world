<?php
namespace app\power\model;

class PowerGroupAction extends Base
{
	// 自动时间
	protected $autoWriteTimestamp = 'datetime';

	protected $table = 'power_group_action';
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';

	public function delByGroupID($groupID){
	    return $this->where('group_id',$groupID)->delete();
	}
	
}