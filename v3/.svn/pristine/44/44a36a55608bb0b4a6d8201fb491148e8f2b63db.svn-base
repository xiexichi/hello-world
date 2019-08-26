<?php
/**
 * 25boy v3 生成二唯码
 * 2019-01-23 张文杰
 */
namespace app\picshow\controller;
use app\apps\model\AppThird;
use app\common\library\Wechat;

class Qrcode extends Base
{
	// 关联查询
	public function indexBefore()
	{
		$data = input();
		$this->model->with('appThird')->data($data);
	}

	// 生成二唯码
	public function add()
	{
		$data = input();
		$this->model->data($data);

		// 保存到列表
		if(!empty($data['savatolist'])) {
			$this->model->add();
		}

		// 生成二唯码
		return $this->doQrcode($data);
	}

	// 生成二唯码
	public function edit()
	{
		$data = input();
		$this->model->data($data);
		// 保存到列表
		$this->model->edit();

		// 生成二唯码
		return $this->doQrcode($data);
	}

	// 生成二唯码
	public function doQrcode($data = [])
	{
		if(empty($data) && input('post.id')) {
			$data = $this->model->find(input('post.id'));
		}
		$this->model->setData($data);
		$result = $this->model->createQrcode();
		if(empty($this->model->error)) {
			return successJson(['imgEncode' => $result]);
		}else{
			return errorJson(90402, $this->model->error);
		}
	}

	/**
	 * 获取第三方app列表
	 */
	public function getThirdApps()
	{
		$data = input();

		// 条件 
		$where = [];
		if(!empty($data['type'])) {
			$where['type'] = $data['type'];
		}
		$result = AppThird::field('id,name,type,appid')->where($where)->select();
		return successJson($result);
	}
}
