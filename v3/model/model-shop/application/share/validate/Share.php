<?php

/**
 * 验证器基类
 */

namespace app\share\validate;

class Share extends Base
{

    public function index($data){
        $rule = [
            'user_id' => 'integer',
            'verify' => 'integer',
            'is_chosen' => 'integer'
        ];
        $message = [
            'user_id' => '参数错误',
            'verify' => '参数错误',
            'is_chosen' => '参数错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }

    public function add($data){
        $rule = [
            'user_id' => 'require|integer',
            'title' => 'require',
            'description' => 'require',
            'images_list' => 'array'
        ];
        $message = [
            'user_id' => '参数错误',
            'title' => '标题不能为空',
            'description' => '描述不能为空',
            'images_list' => '图片格式错误'
        ];
        // 返回验证结果
        return $this->validate($rule, $data,$message);
    }
}
