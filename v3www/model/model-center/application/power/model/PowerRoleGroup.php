<?php
namespace app\power\model;

class PowerRoleGroup extends Base
{
	// 自动时间
	protected $autoWriteTimestamp = 'datetime';

	protected $table = 'power_role_group';
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';

	public function getGroupIDsByRoleID($roleID){
	    $ids = $this->where('role_id',$roleID)->field('GROUP_CONCAT(group_id) AS ids')->find();
	    return $ids['ids'];
	}
	
	public function delByRoleID($roleID){
	    return $this->where('role_id',$roleID)->delete();
	}
}