<?php
namespace app\power\model;

class Admin extends Base
{
	// 自动时间
	protected $autoWriteTimestamp = 'datetime';

	protected $table = 'admin';
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';
	
	public function addBefore(){
	    $this->checkRepeatLoginName($this->data['loginname']);
	}
	
	public function addAfter(){
	    $adminID = $this->data['id'];

//         if(!empty($this->data['shop_ids'])){//绑定管核的店铺
//             $modelAdminShop = new \app\merchant\model\AdminShop();
//             $modelAdminShop->removeAllAdminShopsByAdminID($adminID);
//             if(!$modelAdminShop->bindingAdminShops($adminID,$this->data['shop_ids'])){
//                 $this->setErrorAndCodeExit('绑定店铺失败');
//                 return false;
//             }
//         }
	    
	    if(empty($this->data['role_id'])){
	        return true;
	    }
	    //设置管理员的角色
	    $modelPowerRoleAdmin = new PowerRoleAdmin();
	    $modelPowerRoleAdmin->data(['role_id'=>$this->data['role_id'],'admin_id'=>$adminID]);

	    if(!$modelPowerRoleAdmin->add()){
	        $this->setErrorAndCodeExit('更新角色组-管理员失败');
	    }
	}
	
	public function editAfter(){
	    $adminID = $this->data['id'];
	    $roleInfo = $this->tempData['roleInfo'];
	    
// 	    if($roleInfo['type'] == 2 && !empty($roleInfo['merchant_id'])){//商户经理
// 	        $modelAdminShop = new \app\merchant\model\AdminShop();
// 	        //有没有改都删，懒得查
// 	        $modelAdminShop->removeAllAdminShopsByAdminID($adminID);
	        
// 	        if(!empty($this->data['shop_ids'])){//绑定管核的店铺
// 	            if(!$modelAdminShop->bindingAdminShops($adminID,$this->data['shop_ids'])){
// 	                $this->setErrorAndCodeExit('绑定店铺失败');
// 	                return false;
// 	            }
// 	        }
// 	    }
	    
	    $modelPowerRoleAdmin = new PowerRoleAdmin();
	    //删除管理员角色
	    $modelPowerRoleAdmin->where('admin_id',$adminID)->delete();
	    //重新绑定
	    $modelPowerRoleAdmin->data(['role_id'=>$this->data['role_id'],'admin_id'=>$adminID]);
	    
	    if(!$modelPowerRoleAdmin->add()){
	        $this->setErrorAndCodeExit('更新角色组-管理员失败');
	    }
	}
	
	public static function makePassword($pwd,$salt = null){
	    $salt = $salt ?: random_int(1000,9999);
	    $pwd = 'hea'.$pwd.$salt;
	    return ['pwd'=>md5($pwd),'salt'=>$salt];
	}
	
	public function delAfter(){
	    if(empty($this->data['ids'])){
	        return true;
	    }
	    $adminIds = $this->data['ids'];

	    //删除【角色-管理员】表
	    if(!(new PowerRoleAdmin())->setWhere('admin_id','in',$adminIds)->del()){
	        $this->setErrorAndCodeExit('删除角色组-管理员失败');
	    }
	}
	
	public function getAdminDetail($adminID){
	    $adminDetail = $this->alias('a')
	                        ->field('a.*,pra.role_id')
	                        ->join('power_role_admin pra', 'a.id = pra.admin_id', 'LEFT')
	                        ->where('a.id',$adminID)
	                        ->find();
	    return $adminDetail;
	}
	
	protected function checkRepeatLoginName($loginName){
	    if($loginName == 'admin'){
	       $this->setErrorAndCodeExit('登录名已存在',123);
	       return false;
	    }
	    
	    $admin = $this->field('id')->where('loginname',$loginName)->find();
	    if(!empty($admin)){
	        $this->setErrorAndCodeExit('登录名已存在',123);
	        return false;
	    }
	    return true;
	}
	
	public function login(){
	    $adminData = $this->where('loginname',$this->data['loginname'])->find();
	    if(empty($adminData)){
	        $this->error = '账号或密码错误';
	        return false;
	    }
	    
	    //账号状态
	    if($adminData['status'] != 1){
	        $this->error = '账号被冻结中';
	        return false;
	    }

	    //验证密码
	    if(self::makePassword($this->data['password'], $adminData['salt'])['pwd'] != $adminData['password']){
	        $this->error = '账号或密码错误!';
	        return false;
	    }

	    //删除多余数据
	    unset($adminData['password'],$adminData['salt']);
	    
	    //记录登录ip和时间
	    $nowIP = request()->ip();
	    $nowTime = date('Y-m-d H:i:s');
	    $this->where('id',$adminData['id'])->update(['last_ip'=>$nowIP,'last_time'=>$nowTime]);
	    
	    return $adminData;
	}
	
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
	
	public function getAdminRole($adminID){
	    $role = (new PowerRole())->where('id','=',function($q) use($adminID){
	                                   $q->table('power_role_admin')->where('admin_id',$adminID)->field('role_id');
                        	       })->find();
	    return $role;
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
	


}



















