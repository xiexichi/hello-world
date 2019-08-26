<?php
namespace app\system\controller;
use app\common\controller\Common;

class Index extends Common
{
	// html实例
	private $html;

	public function index()
	{
		$data = array();
		$systemTab = config('systemTab');
		// 获取数据
		$result = $this->service->setHost('shop_data')->get('/system/system/getSettingByGroup');
		$result = json_decode($result, true);
		if(isset($result['code']) && $result['code'] == 0){
			$data = $result['data'];
		}

		// HTML类
		vendor('html.Html');
		$this->html = new \Html();

		// 组装数据
		$formHtmlGroup = [];
		for ($i=0; $i < count($systemTab); $i++) { 
			$group = isset($data[$i]) ? $data[$i] : [];
			$formHtmlGroup[$i] = $this->createFormItem($group);
		}

		// 模板赋值
		$view['tabs'] = $systemTab;
		$view['formHtmlGroup'] = $formHtmlGroup;
		return $this->fetch('index', $view);
	}

	/**
	 * 生成html表单项
	 * 0=input, 1=select, 2=radio, 3=checkbox, 4=textarea
	 */
	private function createFormItem($data)
	{

		if(empty($data)) return false;
		$formHtml = '';
		foreach ($data as $key => $val) {
			$formHtml .= $this->html->input($val['type'], "data[{$val['id']}]", $val['value'], $val['title'], $val['note'], $val['name'], $val['extra']);
		}

		return $formHtml;
	}
}
