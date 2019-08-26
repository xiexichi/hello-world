<?php
namespace app\user\controller;

use EasyWeChat\Factory;

class User extends Base
{
    public function getIndex(){
        $checkData = $this->validate->getIndex(input());
        if (false === $checkData) {
            return errorJson(123, $this->validate->getError());
        }
        
        if(!empty($checkData['user_id'])){
            $this->model->where('id',$checkData['user_id']);
        }
        if(!empty($checkData['user_name'])){
            $this->model->where('user_name','LIKE',"%{$checkData['user_name']}%");
        }
        if(!empty($checkData['phone'])){
            $this->model->where('phone','LIKE',"%{$checkData['phone']}%");
        }
        
        return $this->index();
    }
    
    public function getEditData(){
        if (!$checkData = $this->validate->getEditData(input())) {
            return errorJson(123, $this->validate->getError());
        }

        $this->model->where('id',$checkData['id']);
        if(!$userData = $this->model->one()){
            return errorJson(123, $this->model->getError());
        }
        
        $modelArea = new \app\index\model\Area();
        //获取 国家/省/市/区
        $areas = ['country'   =>  [],'province'   =>  [],'city'   =>  [],'region' =>  []];
        $areas['country'] = $modelArea->getAreasByPid(0);
        if(!empty($userData['country_id'])){
            $areas['province'] = $modelArea->getAreasByPid($userData['country_id']);
        }
        if(!empty($userData['province_id'])){
            $areas['city'] = $modelArea->getAreasByPid($userData['province_id']);
        }
        if(!empty($userData['city_id'])){
            $areas['region'] = $modelArea->getAreasByPid($userData['city_id']);
        }

        return successJson(['user'=>$userData,'areas'=>$areas]);
    }
    
    /**
     * 后台/前端 会员注册
     * @return Json
     */
    public function register(){
        if (!$checkData = $this->validate->register(input())) {
            return errorJson(123, $this->validate->getError());
        }

        if(empty($checkData['phone']) && empty($checkData['email'])){
            return errorJson(123, '请填写手机号/邮箱');
        }
        
        //手机验证码是否正确
        //...
        
        //注册来源（移动端、后台）
        switch ($checkData['regist_from']){
            case 'admin':
                $checkData['is_rename'] = 1;
                break;
            case 'mobile':
            default:
                $checkData['is_rename'] = 0;
                break;
        }
        
        $checkData['ip'] = request()->ip();
        //唯一字段设置
        $checkData['phone'] = $checkData['phone'] ?: null;
        $checkData['email'] = $checkData['email'] ?: null;
        $this->model->data($checkData);
        
        if(empty($this->model->add())){
            return errorJson(123, $this->model->getError());
        }
        return successJson([], '添加成功!');
    }
    
    /**
     * 编辑会员资料
     * @return \think\response\Json|unknown
     */
    public function editUser(){
    	if (!$checkData = $this->validate->editUser(input())) {
    		return errorJson(123, $this->validate->getError());
    	}
    	
    	if(empty($checkData['phone']) && empty($checkData['email'])){
    		return errorJson(123, '请填写手机号/邮箱');
    	}
    	//唯一字段设置
    	$checkData['phone'] = $checkData['phone'] ?: null;
    	$checkData['email'] = $checkData['email'] ?: null;
    	
    	$this->model->data($checkData);
    	if(empty($this->model->edit())){
    		return errorJson(123, $this->model->getError());
    	}
    	return successJson([], '修改成功!');
    }
    
    /**
     * 禁用会员
     * @return \think\response\Json|unknown
     */
    public function disable(){
    	$userID = input('id');
    	if(empty($userID)){
    		return errorJson(123, '参数错误');
    	}
    	$this->model->data(['id'=>$userID,'status'=>0]);
    	if($this->model->edit()){
    		return successJson([], '操作成功');
    	}
    	return errorJson(123, '操作失败');
    	
    }
    
    /**
     * 重置密码（将随机生成的密码发送到手机或邮件，都没有就不能重置）
     * @return \think\response\Json|unknown
     */
    public function resetPwd(){
    	if (!$checkData = $this->validate->resetPwd(input())) {
    		return errorJson(123, $this->validate->getError());
    	}

    	$userData = $this->model->field('phone,email')->where('id',$checkData['user_id'])->find();
    	if(empty($userData)){
    		return errorJson(123, '找不到当前会员信息');
    	}

    	//生成随机6位的密码
    	$newPwd = makeRandStr(6);
    	$sendWhere = '';
    	if(!empty($userData['phone'])){
    		$sendWhere = '手机 '.$userData['phone'];
    		//发送手机验证码
    		//................
    	}elseif (!empty($userData['email'])){
    		$sendWhere = '邮件 '.$userData['email'];
			//发送邮件验证码
			//.................
    	}else{
    		return errorJson(123, '请先绑定手机号或邮箱');
    	}
    	
    	//发送成功后修改数据库
    	$this->model->data(['id'=>$checkData['user_id'],'pass'=>$newPwd]);
    	if(!$this->model->edit()){
    		return errorJson(123, '修改失败');
    	}
    	
    	return successJson([], '密码重置成功并已发送至'.$sendWhere);
    }
    
    
    public function login(){
    	$loginType = input('login_type');//暂时有sms和simple
    	$accountType = '';
    	switch ($loginType){
    		case 'sms':
    			if (!$checkData = $this->validate->loginBySms(input())) {
    				return errorJson(123, $this->validate->getError());
    			}
    			//验证手机验证码
    			//......
    			
    			$accountType = 'phone';
    			break;
    		case 'simple':
    			if (!$checkData = $this->validate->loginBySimple(input())) {
    				return errorJson(123, $this->validate->getError());
    			}
    			
    			if(filter_var($checkData['account'], FILTER_VALIDATE_EMAIL)){
    				$accountType = 'email';
    			}elseif($this->validate->mustPhone($checkData['account'],'','')){
    				$accountType = 'phone';
    			}else{
    				$accountType = 'user_name';
    			}
    			break;
    		default:
    			return errorJson(123, '登录类型错误');
    			break;
    	}
    	
    	//获取用户信息
    	$user = $this->model->where([$accountType=>$checkData['account'],'status'=>1])->find();
    	if(empty($user)){
    		return errorJson(123, '用户不存在');
    	}

    	if($loginType == 'simple'){//普通登录类型要验证密码
    		if($user['pass'] != $this->model->passCrypt($checkData['password'])){
    			return errorJson(123, '账号或密码错误');
    		}
    	}
    	
    	unset($user['pass']);
    	return successJson(['user'=>$user], '登录成功');
    }

    
    public function loginByMinprogram(){
//     	$config = [
//     		'app_id' => 'wx3cf0f39249eb0exx',
//     		'secret' => 'f1c242f4f28f735d4687abb469072axx',
    		
//     		// 下面为可选项
//     		// 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
//     		'response_type' => 'array',
    		
//     		'log' => [
//     			'level' => 'debug',
//     			'file' => __DIR__.'/wechat.log',
//     		],
//     	];
    	
//     	$app = Factory::miniProgram($config);
//     	$app->auth->session($code);
    }
    
}
