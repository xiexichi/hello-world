<?php
namespace app\share\controller;

class Share extends Base
{
    public function indexBefore()
    {
        $param = $this->validate->index(input());
        if( $param === false ){
            $this->isExit = true;
            $this->code = 030101;
            $this->error = $this->validate->getError();
        }
        $where = [];
        //user_id
        if( isset($param['user_id']) && $param['user_id'] != '' ){
            $where['user_id'] = ['=',$param['user_id']];
        }
        //审核
        if( isset($param['verify']) && $param['verify'] != '' ){
            $where['verify'] = ['=',$param['verify']];
        }
        //审核
        if( isset($param['is_chosen']) && $param['is_chosen'] != '' ){
            $where['is_chosen'] = ['=',$param['is_chosen']];
        }
        $this->model->where($where)->order('create_time','desc');
    }

}
