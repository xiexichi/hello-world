<?php

namespace app\depot\validate;


class ShopPurchaseItem extends Base
{
		
	/**
	 * [batchEdit 批量修改]
	 * @param  [type] $data [验证数据]
	 * @return [type]       [description]
	 */
	public function batchEdit($data) {
		$rule = [
            'shop_purchase_order_id' => 'require|number',// 订单id
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