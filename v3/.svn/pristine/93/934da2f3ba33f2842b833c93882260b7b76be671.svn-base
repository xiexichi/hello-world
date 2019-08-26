<?php
/**
 * 商品售价日志
 */
namespace app\goods\controller;

class GoodsPriceLog extends Base
{
    public function allBefore()
    {
        $data = input();
        if(!empty($data)){
            $where = [];
            foreach($data as $k => $v ){
                // 排序
                if( $k == 'order' ) {
                    if(is_array($v)){
                        $this->model->order($v);
                    }
                    unset($data[$k]);
                }else{
                    $where[$k] = $v;
                }
            }
            if( !empty($where) ){
                $this->model->where($where);
            }
        }
    }
}
