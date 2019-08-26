<?php

namespace app\article\validate;

class Article extends Base
{
    public function index($data){
        $rule = [
            'title' => 'chsDash',
            'categorys_id' => 'integer',
        ];
        return $this->validate($rule, $data);
    }

    public function add($data){
 
        $rule = [
            'title' => 'require|chsDash',
            'categorys_id' => 'require|integer',
            'sort' => 'integer',
            'desc' => 'min:0',
            'content' => 'min:0',
            'image' => 'min:0',

        ];
        return $this->validate($rule,$data);
    }


    public function delete($ids){
        $rule = [
            'ids' =>'require|integer',
        ];
        return $this->validate($rule,$data);
    }


    public function edit($data){

        $rule = [
            'id' => 'require|integer',
            'title' => 'chsDash',
            'categorys_id' => 'require|integer',
            'sort' => 'integer',
            'desc' => 'min:0',
            'content' => 'min:0',
            'image' => 'min:0',
        ];

        return $this->validate($rule,$data);
    }


    public function checkRepeatArticleName($data){
        $rule = [
            'id' => 'integer',
            'title' => 'require|chsDash',
        ];

        return $this->validate($rule,$data);
    }
}
