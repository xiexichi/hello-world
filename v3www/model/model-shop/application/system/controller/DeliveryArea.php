<?php
/**
 * 25boy v3 运费模板模块
 * 2019-01-10 张文杰
 */
namespace app\system\controller;

use app\system\model\DeliveryRegion;

class DeliveryArea extends Base
{
	// 关联查询
	public function indexBefore()
	{
		$data = input();
		if( empty($data['id']) ){
			$this->setExitErrorInfo('请求数据失败:参数ID为空，请返回重新进入。', 80301);
			return false;
		}
		$this->model->with('deliveryRegion')->where(['delivery_id' => $data['id']])->data($data);
	}

	// 关联查询
	public function oneBefore()
	{
		$data = input();
		if( empty($data['id']) ){
			$this->setExitErrorInfo('请求数据失败:参数ID为空，请返回重新进入。', 80301);
			return false;
		}
		$this->model->with('deliveryRegion')->where(['id' => $data['id']])->data($data);
	}

	// 删除
	public function delete()
	{
		$ids = input('post.ids');
		// 输出错误提示
  	if (empty($ids)) {
  		return errorJson(80303, '缺失参数ids');
  	} else {
  		// 设置多个删除条件
  		$this->model->deleted($ids);
  	}
	}
}