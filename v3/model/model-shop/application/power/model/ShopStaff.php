<?php
namespace app\power\model;

class ShopStaff extends Base
{
	// 自动时间
	protected $autoWriteTimestamp = 'datetime';

	protected $table = 'shop_staff';
	protected $createTime = 'create_time';
	
	public function getAdminPower($roleID = 0){
	    if(empty($roleID)){//获取所有
	        $sql = 'SELECT pa.is_default,pa.NAME AS a_name,pc.title AS c_title,pc.NAME AS c_name,pm.icon_code AS m_icon,pm.title AS m_title,pm.NAME AS m_name,pc.is_show AS c_show,pm.is_show AS m_show FROM power_action pa INNER JOIN power_controller pc ON pa.controller_id = pc.id INNER JOIN power_module pm ON pc.module_id = pm.id WHERE 1 ORDER BY pm.sort DESC,pc.sort DESC';
	        $powerData = \think\Db::query($sql);
	    }else{
	        $sql = 'SELECT pa.is_default,pa.name AS a_name,pc.title AS c_title,pc.name AS c_name,pm.icon_code AS m_icon,pm.title AS m_title,pm.name AS m_name,pc.is_show AS c_show,pm.is_show AS m_show FROM power_action pa INNER JOIN power_controller pc ON pa.controller_id = pc.id INNER JOIN power_module pm ON pc.module_id = pm.id WHERE pa.id IN (SELECT action_id FROM power_group_action WHERE group_id IN ( SELECT group_id FROM power_role_group WHERE role_id = :role_id ) ) ORDER BY pm.sort DESC,pc.sort DESC';
	        $powerData = \think\Db::query($sql, ['role_id'=>$roleID]);
	    }
	    
	    return $this->makePowerArr($powerData);
	    
	}

	/**
	 * 生成权限数组（包括后台菜单、权限列表）
	 * @param unknown $powerData
	 * @return array[]|boolean[]|string[]|array[][][]|unknown[][][]|string[][]|unknown[][]
	 */
	public function makePowerArr($powerData){
	    $actionArr = [];
	    $menuArr = [];
	    foreach ($powerData as $v){
	        $actionArr[$v['m_name']][$v['c_name']][$v['a_name']] = true;
	        
	        if($v['m_show'] == 1 && empty($menuArr[$v['m_name']])){
	            $menuArr[$v['m_name']] = [
	                'title'    =>  $v['m_title'],
	                'icon'     =>  $v['m_icon'],
	                'children' =>  []
	            ];
	        }
	        if($v['c_show'] == 1 && empty($menuArr[$v['m_name']]['children'][$v['c_name']])){
	            $menuArr[$v['m_name']]['children'][$v['c_name']] = [
	                'title'    =>  $v['c_title'],
	                'link'     =>  ''
	            ];
	        }
	        
	        if($v['is_default'] == 1){
	            $menuArr[$v['m_name']]['children'][$v['c_name']]['link'] = "/{$v['m_name']}/{$v['c_name']}/{$v['a_name']}.html";
	        }
	    }
	    return ['actions'=>$actionArr,'menu'=>$menuArr];
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function getAdminRole($adminID){
	    $role = (new PowerRole())->where('id','=',function($q) use($adminID){
	        $q->table('power_role_admin')->where('admin_id',$adminID)->field('role_id');
	    })->find();
	    return $role;
	}


}



















