<?php
namespace app\power\model;

class PowerGroup extends Base
{
	// 自动时间
	protected $autoWriteTimestamp = 'datetime';

	protected $table = 'power_group';
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';

	public function addAfter(){
	    if(empty($this->data['action_ids']) || !is_array($this->data['action_ids'])){
	        return false;
	    }
	    $groupID = $this->data['id'];
	    $list = [];
	    array_map(function($v) use ($groupID,&$list){
	        $list[] = ['group_id'=>$groupID,'action_id'=>$v];
	    }, $this->data['action_ids']);

	    if(!(new PowerGroupAction())->addAll($list)){
	        $this->setErrorAndCodeExit('更新权限组-方法表失败');
	    }
	}
	
	public function editAfter(){
	    (new PowerGroupAction())->delByGroupID($this->data['id']);
	    $this->addAfter();
	}
	
	
	public function delAfter(){
	    $groupIds = '';
	    if(!empty($this->tempData['delAfter'])){//上级删除时调用的
	        $groupIds = $this->tempData['delAfter']['group_ids'];
	    }elseif(!empty($this->data['ids'])){//前端删除时的
	        $groupIds = $this->data['ids'];
	    }
	    
	    if(empty($groupIds)){
	        return true;
	    }
	    
	    //删除【方法组-方法】 关联表
	    (new PowerGroupAction())->setWhere('group_id','in',$groupIds)->del();
	    
	    //删除【方法组-角色】关联表
	    (new PowerRoleGroup())->setWhere('group_id',$groupIds)->del();
	}
	
	public function getGroupTree(){
	    $moduleList = collection((new PowerModule())->all())->toArray();
	    $controllerList = collection((new PowerController())->all())->toArray();
	    $groupList = collection($this->all())->toArray();
	    return self::makeMCGTree($moduleList, $controllerList, $groupList);
	}
	
	/**
	 * 将 【模块】【控制器】【方法组】组合成分类树形式，不要试图迭代递归
	 * @param unknown $moduleList
	 * @param unknown $controllerList
	 * @param unknown $groupList
	 * @return array[]|unknown[]
	 */
	protected static function makeMCGTree($moduleList,$controllerList,$groupList){
	    $tree = [];
	    foreach ($moduleList as $v){
	        $v['children'] = [];
	        if(!empty($controllerList)){
	            foreach ($controllerList as $kk=>$vv){
	                if($vv['module_id'] == $v['id']){
	                    $vv['children'] = [];
	                    if(!empty($groupList)){
	                        foreach ($groupList as $kkk=>$vvv){
	                            if($vvv['controller_id'] == $vv['id']){
	                                $vv['children'][] = $vvv;
	                                unset($groupList[$kkk]);
	                            }
	                        }
	                    }
	                    $v['children'][] = $vv;
	                    unset($controllerList[$kk]);
	                }
	            }
	        }
	        $tree[] = $v;
	    }
	    return $tree;
	}

}