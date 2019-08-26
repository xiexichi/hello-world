<?php


namespace app\depot\validate;


class ShopDepot extends Base
{

    /**
     * [getStockInfo 验证获取库存信息]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
	public function getStockInfo($data){

		$rule = [
            'shop_id' => 'require|number',// 店铺id
            'stock_id' => 'require|number',// 商品库存id
            'type' => 'require|number',// 商品库存类型：1=商品，2=物料
        ];
        
        // 返回验证结果
        return $this->validate($rule, $data);
	}

}