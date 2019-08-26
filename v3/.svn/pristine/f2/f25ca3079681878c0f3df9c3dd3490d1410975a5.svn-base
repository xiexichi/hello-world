<?php
namespace app\user\model;

use think\Db;

class UserThird extends Base
{
    protected $autoWriteTimestamp = 'datetime';
    protected $table = 'user_third';
    protected $createTime = 'create_time';
    
    public function addBefore(){
    	//先找出第三方类型对应的id
    	$thirdID = Db::table('app_third')->where('type',$this->data['third_type'])->value('id');
    	if(empty($thirdID)){
    		$this->isExit = true;
    		$this->error = "找不到第三方类型：{$this->data['third_type']}";
    		return false;
    	}
    	
    	//检查用户绑定过当前第三方类型没
    	if($this->hadBoundOtherThird($this->data['user_id'], $thirdID)){
    		$this->isExit = true;
    		$this->error = "当前账号已绑定过该第三方";
    		return false;
    	}
    	
    	//检查当前第三方账号是否绑定过用户
    	if($this->hadBoundOtherUser($this->data['openid'], $this->data['unionid'])){
    		$this->isExit = true;
    		$this->error = "当前第三方账号已绑定过25boy会员";
    		return false;
    	}
    	
    	$this->data['app_third_id'] = $thirdID;
    }
    
    /**
     * openid或unionid是否绑定过账号
     * @param string $openID
     * @param string $unionID
     * @return bool
     */
    public function hadBoundOtherUser(string $openID, string $unionID):bool{
    	$res = $this->where('openid',$openID)->whereOr('unionid',$unionID)->find();
    	return !empty($res);
    }

    /**
     * 某会员id是否绑定过某第三方
     * @param string $userID
     * @param string $bindingTypeID
     * @return bool
     */
    public function hadBoundOtherThird(string $userID, string $bindingTypeID):bool{
    	$res = $this->where(['user_id'=>$userID,'app_third_id'=>$bindingTypeID])->find();
    	return !empty($res);
    }

}
