<?php

namespace app\depot\validate;


class ShopDifferItem extends Base
{

	/**
	 * [saveDifferItem 保存差异项]
	 * @param  [type] $data [验证数据]
	 * @return [type]       [description]
	 */
	public function saveDifferItem($data){

		// 如果有操作员工id
		if (input('ctrl_staff_id')) {
			// 设置员工id
			$data['staff_id'] = input('ctrl_staff_id');
		}

		$rule = [
            'shop_purchase_item_id' => 'require|number',// 进货批次商品项id
            'apply_quantity' => 'require|number',	// item是数组
            'shop_id' 	=> 'require|number',	// 店铺id
            'staff_id'  => 'number',	// 员工id
        ];
        	
        // 返回验证结果
        return $this->validate($rule, $data);
	}


	/**
	 * [batchEdit 批量修改]
	 * @param  [type] $data [验证数据]
	 * @return [type]       [description]
	 */
	public function batchEdit($data) {
		$rule = [
            'shop_differ_order_id' => 'require|number',// 订单id
            'items' => 'array',	// item是数组
        ];
        	
        // 返回验证结果
        $res = $this->validate($rule, $data);

        if (!$res) {
        	return false;
        }

        // 检测数组
        foreach ($res['items'] as $k => $v) {
        	if (!$item = $this->item($v)) {
        		return false;
        	}
        	// 接收item数据
        	$res['items'][$k] = $item;
        }

        return $res;
	}

	/**
	 * [item 商品项检测]
	 * @param  [type] $data [检测数据]
	 * @return [type]       [description]
	 */
	public function item($data){
		$rule = [
            'id' => 'require|number',// 预选标记id
            'real_quantity' => 'require|number',	// 实发数量
        ];
        // 返回验证结果
        return $this->validate($rule, $data);
	}


}