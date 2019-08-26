<?php
/**
 * 25boy v3 会员模块
 */
namespace app\user\model;

class User extends Base
{
    protected $autoWriteTimestamp = 'datetime';
    protected $table = 'user';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    
    /**
     * 有没有会员在某会员等级上
     * @param unknown $levelID
     * @return user_id
     */
    public function isLevelHasUser($levelID){
        return $this->where('level_id',$levelID)->value('id');
    }
    
    public function indexAfter(){
        if(!empty($this->data)){
            foreach( $this->data as $v){
                unset($v['pass']);
            }
        }
    }
    
    public function oneAfter(){
        if(!empty($this->data)){
            unset($this->data['pass']);
        }        
    }
    
    public function addBefore(){
    	// 检测唯一字段是否重复
    	if (!$this->checkRepeatFields()) {
    		return false;
    	}
    	// 密码加密
    	if(!empty($this->data['pass'])){
    		$this->data['pass'] = $this->passCrypt($this->data['pass']);
    	}
    }
    
    public function editBefore(){
    	// 检测唯一字段是否重复
    	if (!$this->checkRepeatFields($this->data['id'])) {
    		return false;
    	}
    	// 密码加密
    	if(!empty($this->data['pass'])){
    		$this->data['pass'] = $this->passCrypt($this->data['pass']);
    	}
    }
    
    protected function checkRepeatFields($id = NULL){
        if (!empty($this->data['phone'])) {
            if ($id) {
                $this->where('id', '<>', $id);
            }
            $res = $this->where('phone', $this->data['phone'])->find();
            if ($res) {
                $this->isExit = true;
                $this->error = "手机号：{$this->data['phone']} 已存在，不能重复！";
                return false;
            }
        }
        if (!empty($this->data['user_name'])) {
            if ($id) {
                $this->where('id', '<>', $id);
            }
            $res = $this->where("user_name", $this->data['user_name'])->find();
            if ($res) {
                $this->isExit = true;
                $this->error = "用户名：{$this->data['user_name']} 已存在，不能重复！";
                return false;
            }
        }
        if (!empty($this->data['email'])) {
            if ($id) {
                $this->where('id', '<>', $id);
            }
            $res = $this->where("email", $this->data['email'])->find();
            if ($res) {
                $this->isExit = true;
                $this->error = "邮箱：{$this->data['email']} 已存在，不能重复！";
                return false;
            }
        }
        return true;
    }
}
