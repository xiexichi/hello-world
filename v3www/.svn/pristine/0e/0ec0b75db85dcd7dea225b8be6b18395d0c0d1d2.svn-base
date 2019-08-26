<?php
namespace app\power\model;

class PowerAction extends Base
{
	// 自动时间
	protected $autoWriteTimestamp = 'datetime';

	protected $table = 'power_action';
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';

	public function delAfter(){
	    $actionIds = '';
	    
	    
	    if(!empty($this->tempData['delAfter'])){//上级删除时调用的
	        $actionIds = $this->tempData['delAfter']['action_ids'];
	    }elseif(!empty($this->data['ids'])){//前端删除时的
	        $actionIds = $this->data['ids'];
	    }
	    
	    if(empty($actionIds)){
	        return true;
	    }
	    
	    //删除【方法组-方法】 关联表
	    (new PowerGroupAction())->setWhere('action_id','in',$actionIds)->del();
	}
	
	public function getEditData($controllerID, $groupID){
	    $actionList = $this->field('*,0 AS is_selected')->where('controller_id',$controllerID)->select();
 	    $groupList = (new PowerGroupAction())->field('action_id')->where('group_id',$groupID)->select();

	    if(!empty($actionList) && !empty($groupList)){
	        $groupList = array_column($groupList, 'action_id');
	        foreach ($actionList as $k=>$v){
	            if(in_array($v['id'], $groupList)){
	                $actionList[$k]['is_selected'] = 1;
	            }
	        }
	    }
	    return $actionList;
	}
}