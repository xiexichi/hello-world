<?php

namespace app\index\model;

class Area extends Base
{
    protected $table = 'area';

    public function getAreasByPid($pid = 0){
        return $this->where('pid', $pid)->select();
    }
}