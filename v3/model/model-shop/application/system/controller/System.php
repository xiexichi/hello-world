<?php
/**
 * 25boy v3 网站设置模块
 * 2019-01-07 张文杰
 */
namespace app\system\controller;

class System extends Base
{
	/**
	 * 获取网站设置参数
	 */
	public function getSetting()
	{
		$group = input('group', 0, 'int');
		$result = $this->model->where(['group'=>$group])->select();
		return successJson($result);
	}

	/**
	 * 获取网站设置参数
	 */
	public function getSettingByGroup()
	{
		$result = $this->model->getAll();
		// 分分组
		$group = [];
		foreach ($result as $key => $value) {
			$group[$value['group']][] = $value;
		}
		return successJson($group);
	}

	/**
	 * 修改配置
	 */
	public function edit()
  {
  	$post = input('post.');
  	$data = $post['data'];
		$group = $post['group'];

		// 整理数据
		$editData = [];
		foreach ($data as $key => $val) {
			$editData[] = [
				'id' => $key,
				'value' => is_array($val) ? join(',', $val) : trim($val),
				'group' => $group
			];
		}
		$oldData = $this->model->where('group', $group)->select();
		$this->model->setChangeBeforeData($oldData);
		if( ! $this->model->saveAll($editData) ){
			return errorJson(80301, $this->model->getError());
		}else{
			$this->editAfter();
			return successJson([], '保存成功');
		}
  }

  /**
   * 添加参数前置操作
   */
  public function addBefore()
  {
		// 验证器
  	if ($this->validate && objHasMethod($this->validate, 'add')) {
			if (!$checkData = $this->validate->add(input('post.'))) {
				// 设置错误信息和错误码
				$this->setExitErrorInfo($this->validate->getError(), 80401);
				return false; 
			}
			$data = input('post.');
			if (in_array($data['type'],['select','radio','checkbox'])) {
				if( empty($data['extra']) ){
					$this->setExitErrorInfo('类型为select,radio,checkbox，请填写扩展内容', 80402);
					return false;
				}
			}
			$this->model->data($data);
		}
  }

  /**
   * 修改参数后置方法
   */
  public function editAfter()
  {
  	$this->updateConfigFile();

		// 添加操作日志
		actionLogs('修改配置', $this->model);
  }

  /**
   * 添加参数后置方法
   */
  public function addAfter()
  {
  	$this->updateConfigFile();

		// 添加操作日志
		actionLogs('添加配置', $this->model);
  }

  /**
   * 更新配置文件
   * /applicaction/extra/system.php
   */
  public function updateConfigFile()
  {
  	try {
  		// 更新配置文件
	  	$data = $this->model->getAll();
	  	$file_path = APP_PATH  .'extra/system.php';
	  	$string = "<?php" . PHP_EOL; 
	  	$string .= "/*" . PHP_EOL; 
	  	$string .= " * 由后台生成的配置文件 system/system/updateConfigFile" . PHP_EOL; 
	  	$string .= " * 请勿直接修改此文件" . PHP_EOL; 
	  	$string .= " * 最后更新时间：".date('Y-m-d H:i:s',time())."\n*/\n\n"; 
	  	$string .= "return [" . PHP_EOL;
	  	foreach ($data as $key => $val) {
				// $string .= "// {$val['name']}\n";
  			$string .= "\t'{$val['name']}' => '{$val['value']}'," . PHP_EOL;
	  	}
	  	$string .= "];" . PHP_EOL;
			// 写入配置文件
	  	file_put_contents($file_path, $string);
  	} catch (Exception $e) {
  		return $e;
  	}
  }

}