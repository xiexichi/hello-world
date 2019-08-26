<?php
namespace app\power\model;

class PowerRole extends Base
{
	// 自动时间
	protected $autoWriteTimestamp = 'datetime';

	protected $table = 'power_role';
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';

	/**
	 * 获取某角色id的可设置权限树（包括已设置的）(超管获取所有)
	 * @param unknown $adminIsSuper
	 * @param number $myRoleID
	 * @param number $selectedRoleID
	 * @return array|number[]|boolean[]|unknown[]|number[][]|boolean[][]|unknown[][]
	 */
	public function getRoleGroupTreeByRoleID($adminIsSuper,$myRoleID = 0,$selectedRoleID = 0){
	    $where = [
	        'module'=>[],
	        'controller'=>[],
	        'group' =>[]
	    ];
	    if(empty($adminIsSuper)){//不是超级管理员
	        $roles = self::getRolesByRoleID($myRoleID);
	        if(empty($roles)){
	            return [];
	        }
	        $where['module']['id'] = ['IN',$roles['module_ids']];
	        $where['controller']['id'] = ['IN',$roles['controller_ids']];
	        $where['group']['id'] = ['IN',$roles['group_ids']];
	    }

	    $moduleList = (new PowerModule())->field('id,title')->where($where['module'])->order('sort DESC')->select();
	    $controllerList = (new PowerController())->field('id,title,module_id')->where($where['controller'])->order('sort DESC')->select();
	    $groupList = (new PowerGroup())->field('id,title,module_id,controller_id')->where($where['group'])->order('sort DESC')->select();

	    //选中的角色权限
	    if(!empty($selectedRoleID) && $selectedRoles = self::getRolesByRoleID($selectedRoleID)){
	        $selectedRoles = [
	            'group_ids'    =>  explode(',', $selectedRoles['group_ids']),
	            'module_ids'   =>  explode(',', $selectedRoles['module_ids']),
	            'controller_ids'=> explode(',', $selectedRoles['controller_ids'])
	        ];
	    }else{
	        $selectedRoles = ['group_ids'=>[],'module_ids'=>[],'controller_ids'=>[]];
	    }

	    return self::makeMCGAuthtree($moduleList, $controllerList, $groupList, $selectedRoles);
	}
	/**
	 * 获取某角色的可用【方法组】【控制器】【模块】的ID
	 * @param unknown $roleID
	 * @return array[]|array[]
	 */
	public static function getRolesByRoleID($roleID){
	    $roles = (new PowerGroup())->field('GROUP_CONCAT(id) AS g_ids,GROUP_CONCAT(DISTINCT(module_id)) AS m_ids,GROUP_CONCAT(DISTINCT(controller_id)) AS c_ids')
	    ->where('is_show',1)
	    ->where('id','IN',function($q) use($roleID){
	        $q->table('power_role_group')->where('role_id',$roleID)->field('group_id');
	    })
	    ->order('sort DESC')
	    ->find();

	    if(empty($roles['g_ids'])){
	        return [];
	    }
	    $data = [
	        'group_ids'    =>  $roles['g_ids'],
	        'module_ids'   =>  $roles['m_ids'],
	        'controller_ids'=> $roles['c_ids']
	    ];
	    return $data;
	}
	/**
	 * 创建layui的authtree结构
	 * @param unknown $moduleList
	 * @param unknown $controllerList
	 * @param unknown $groupList
	 * @param array $selectedRoles
	 * @return number[]|boolean[]|unknown[]|number[][]|boolean[][]|unknown[][]
	 */
	public static function makeMCGAuthtree($moduleList,$controllerList,$groupList,$selectedRoles = []){
	    /*
	     //返回树例子
	     $list['trees'] = [
	     ["name"=> "用户管理", "value"=> "yhgl", "checked"=> true],
	     ["name"=> "用户组管理", "value"=> "yhzgl", "checked"=> true, "list"=> [
	     ["name"=> "角色管理", "value"=> "yhzgl-jsgl", "checked"=> true, "list"=>[
	     ["name"=> "添加角色", "value"=> "yhzgl-jsgl-tjjs", "checked"=> true],
	     ["name"=> "角色列表", "value"=> "yhzgl-jsgl-jslb", "checked"=> false]
	     ]]
	     ]],
	     ["name"=> "管理员管理", "value"=> "glygl", "checked"=> false, "list"=>[
	     ["name"=> "添加管理员", "value"=> "glygl-tjgly", "checked"=> false],
	     ["name"=> "管理员列表", "value"=> "glygl-glylb", "checked"=> false],
	     ["name"=> "管理员管理", "value"=> "glygl-glylb", "checked"=> false]
	     ]]
	     ];*/
	    $tree = [];
	    if(empty($selectedRoles)){
	        $selectedRoles = ['module_ids'=>[],'controller_ids'=>[],'group_ids'=>[]];
	    }
	    foreach ($moduleList as $k=>$v){
	        $tree[$k] = [
	            'name'     =>  $v['title'],
	            //'value'    =>  (string)$v['id'],
	            'value'    =>  0,
	            'checked'  =>  in_array($v['id'], $selectedRoles['module_ids'])
	        ];
	        foreach ($controllerList as $kk=>$vv){
	            if($vv['module_id'] == $v['id']){
	                $tree[$k]['list'][$kk] = [
	                    'name'     =>  $vv['title'],
	                    //'value'    =>  $vv['module_id'].'-'.$vv['id'],
	                    'value'    =>  0,
	                    'checked'  =>  in_array($vv['id'], $selectedRoles['controller_ids'])
	                ];
	                foreach ($groupList as $kkk=>$vvv){
	                    if($vvv['controller_id'] == $vv['id']){
	                        $tree[$k]['list'][$kk]['list'][$kkk] = [
	                            'name'     =>  $vvv['title'],
	                            //'value'    =>  $vvv['module_id'].'-'.$vvv['controller_id'].'-'.$vvv['id'],
	                            'value'    =>  $vvv['id'],
	                            'checked'  =>  in_array($vvv['id'], $selectedRoles['group_ids'])
	                        ];
	                        unset($groupList[$kkk]);
	                    }
	                }
	                unset($controllerList[$kk]);
	            }
	        }
	    }
	    return $tree;
	}

	public function addAfter(){
	    if(empty($this->data['authids'])){
	        return true;
	    }
	    //插入【角色-权限组】-关联表
	    $roleID = $this->data['id'];
	    $list = [];
	    array_map(function($v) use ($roleID,&$list){
	        $list[] = ['group_id'=>$v,'role_id'=>$roleID];
	    }, $this->data['authids']);

	        if(!(new PowerRoleGroup())->addAll($list)){
	            $this->setErrorAndCodeExit('更新角色组-方法表失败');
	        }
	}

	public function editAfter(){
	    //清空关联表
	    (new PowerRoleGroup())->delByRoleID($this->data['id']);

	    $this->addAfter();
	}

	public function delAfter(){
	    $id = $this->data['ids'];
	    //删除role_group关联表
	    (new PowerRoleGroup())->where('role_id',$id)->delete();
	    //删除role_admin关联表
	    (new PowerRoleAdmin())->where('role_id',$id)->delete();
	}

	/**
	 * 获取管理员的下级角色/或所有
	 * @param number $roleID
	 * @return unknown|unknown[]
	 */
	public function getAdminNextRoles($roleID = 0){
	    $allRoles = $this->field('id,pid,title')->where('status',1)->where('type',1)->select();
	    return setTree($allRoles,$roleID);
	}
	/**
	 * 获取商户的下级角色/获取所有第一级商户boss角色
	 * @param number $merchantID
	 * @return unknown
	 */
	public function getMerchantNextRoles($merchantID = 0){
	    return $this->field('id,pid,title,type,merchant_id')->where('status',1)->where('type',2)->where('merchant_id',$merchantID)->select();
	}
	/**
	 * 获取所有一级角色
	 * @return unknown
	 */
	public function getAllFirstRoles(){
	    return $this->field('id,pid,title,type,merchant_id')->where('status',1)->select();
	}

    public function getAllAreas(){
        return $this->field('id,merchant_id')->where(1)->select();
    }

	public function getRoleInfoByAdminID($adminID){
	    return $this->field('pr.id,pr.pid,pr.title,pr.type,pr.merchant_id')
	                ->alias('pr')
	                ->join('power_role_admin pra','pr.id=pra.role_id')
	                ->where('pra.admin_id',$adminID)
	                ->find();
	}

}




























