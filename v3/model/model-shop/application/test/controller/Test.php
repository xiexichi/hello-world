<?php
namespace app\test\controller;

use app\common\controller\Service;
use think\Exception;

class Test extends Base{
    public function getTest(){
        $res = $this->model->where('id','<','10')->order('id desc')->select();
        return successJson($res);
    }
}
