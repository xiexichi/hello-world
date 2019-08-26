<?php
/**
 * 25boy v3 日志行为类
 * 2019-01-19 张文杰
 */
namespace app\common\hook;
use think\Db;

class Logs 
{
	// 请求实例
	protected $request;
	// 是否编辑
	protected $isEdit = false;
	// 模型实例
	protected $model;

	/**
	 * 写入操作日志
	 * @param $params.log_info [string] 操作内容说明
	 * @param $params.model [string] 操作内容说明
	 */
	public function run($params)
	{
		$this->model = $params['model'];
		$this->request = Request();
		// 是否编辑方法
		$editFunc = explode(',', getSystemSet('LOGS_EDIT_FUNC'));
		if(in_array($this->request->action(), $editFunc)) $this->isEdit = true;

		$data = array();
		$data['admin_id'] = 1; //临时写入，需要修改
		$data['log_ip'] = $this->request->ip();
		$data['log_info'] = set_init($params['log_info'], '');
		$data['module'] = humpToLine($this->request->module());
		$data['controller'] = humpToLine($this->request->controller());
		$data['action'] = humpToLine($this->request->action());
		$data['params'] = $this->changeParams($data);	// 变动的数据
		$data['is_edit'] = $this->isEdit;
		$data['create_time'] = date('Y-m-d H:i:s');
		$result = Db::connect('center.db')->table('system_log')->insert($data);
		if($result){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 找出修改内容
	 */
	private function changeParams($data)
	{
		$updateFields = [];
		if( $this->isEdit )
		{
			// 修改前后的数据
			$oldData = $this->model->tempData['changeBeforeData'];
			$newData = $this->model->getData();
			// 修改配置参数单独处理
			if($data['module'] == 'system' && $data['controller'] == 'system'){
				$tmpOld = [];
				foreach ($oldData as $key => $val) $tmpOld[$val['title']] = $val['value'];
				$tmpNew = [];
				foreach ($newData as $key => $val) $tmpNew[$val['title']] = $val['value'];
				$oldData = $tmpOld;
				$newData = $tmpNew;
			}
			// 整理差异
			$diffData = array_diff_assoc($oldData, $newData);
			foreach ($diffData as $key => $val) {
				if(!isset($newData[$key])) continue;
				$updateFields[$key] = [
					'old' => $oldData[$key],
					'new' => $newData[$key],
				];
			}
		} else {
			// 其他方法
			$updateFields = $this->request->param();
		}
		return json_encode($updateFields, JSON_UNESCAPED_UNICODE);
	}

}