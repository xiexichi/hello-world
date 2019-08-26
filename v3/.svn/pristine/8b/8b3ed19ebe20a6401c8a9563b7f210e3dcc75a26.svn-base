<?php

namespace app\goods\model;

use app\common\controller\Service;

class Evaluation extends Base
{
    protected $name = 'goods_evaluation';
    //显示类型
    const VERIFY_WAIT= 0;  //待审核
    const VERIFY_PAST= 1;  //通过
    const VERIFY_LOST = 2;   //不通过

    public static $map_verify = array(
        self::VERIFY_WAIT => array(
            'desc' => '待审核'
        ),
        self::VERIFY_PAST => array(
            'desc' => '已审核'
        ),
        self::VERIFY_LOST => array(
            'desc' => '不通过'
        ),
    );

    public function indexAfter(){
        if( !empty($this->data) ){
            foreach($this->data as $k => $v ){
                $v['verify_desc'] = self::$map_verify[$v['verify']]['desc'];
                $v['images_list'] = empty($v['images_list']) ? [] : json_decode($v['images_list'],true);
                $v['has_img'] = empty($v['images_list']) ? '无图' : '有图';
                $this->data[$k] = $v;
            }
        }
    }

    public function orderGoods(){
        return $this->hasOne('app\order\model\OrderGoods','id','order_goods_id');
    }

    public function oneBefore(){
        $this->with('orderGoods');
    }

    public function oneAfter(){
        if( !empty($this->data) ){
            $this->data['images_list'] = empty($this->data['images_list']) ? [] : json_decode($this->data['images_list'],true);
            $data['order_goods'] = $this->data['order_goods'];
            unset($this->data['order_goods']);
            $data['eval_info'] = $this->data;
            $server = new Service();
            $data['user_info'] = $server->setHost('center_data')->post('user/user/one',['id'=>$this->data['user_id']]);
            $this->data = $data;
        }
    }

    public function editBefore(){
        $this->data['update_time'] = date('Y-m-d H:i:s');
        //检查是否存在
        $info = $this->where("id = {$this->data['id']}")->find();
        if(empty($info)){
            $this->code = 010704;
            $this->isExit = true;
            $this->error = "评论信息获取失败";
            return false;
        }
        return true;
    }

}
