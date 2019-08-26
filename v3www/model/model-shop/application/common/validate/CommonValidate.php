<?php

/**
 * 公共验证器
 */
namespace app\common\validate;

use think\Validate;

class CommonValidate{
	// 错误信息
	public $error;

	protected $filterEmpty = false;
	protected $filterEmptyStr = false;

	/**
	 * [setFilterEmpty 设置过滤空参数]
	 * @param [type] $is_filter_empty [description]
	 */
	public function setFilterEmpty($is_filter_empty){
		$this->filterEmpty = $is_filter_empty;
		return $this;
	}

	/**
	 * [setEmptyString 设置过滤空字符串参数]
	 * @param [type] $is_filter_emptyStr [description]
	 */
	public function setEmptyString($is_filter_emptyStr){
		$this->filterEmptyStr = $is_filter_emptyStr;
		return $this;
	}


	/**
	 * [validate 验证数据]
	 * @param  [type] $rule [验证规则]
	 * @param  [type] $data [验证数据]
	 * @param  [type] $msg  [自定义错误信息]
	 * @return [type]       [array/false]
	 */
	protected function validate($rule , $data, $msg = NULL, $callback = NULL){
		// 创建验证器
		if ($msg) {
			$validate = new Validate($rule, $msg);
		} else {
			$validate = new Validate($rule);
		}

		// if(!$validate->batch()->check($data)){
		if(!$validate->check($data)){
			// 设置错误信息
			$this->error = $validate->getError();
			return false;
		}

		// 返回验证后的数据
		$result = [];

		foreach ($rule as $k => $v) {
			if (isset($data[$k])) {
				// 过滤空参数
				if ($this->filterEmpty) {
					if (empty($data[$k])) {
						continue;
					}
				}

				// 过滤空字符串
				if ($this->filterEmptyStr) {
					if ($data[$k] == '') {
						continue;
					}
				}

				// 字符串过滤前后空格
				if (is_string($data[$k])) {
					$result[$k] = trim($data[$k]);
				} else {
					$result[$k] = $data[$k];
				}
			}
		}
		// 调用回调函数
		if ($callback) {
			if (!call_user_func($callback,$data)) {
				return false;
			}
		}

		return $result;
	}

	/**
	 * [setError 设置错误信息]
	 * @param [type] $error [description]
	 */
	public function setError($error){
		$this->error = $error;
		return $this;
	}

	/**
	 * [getError 获取错误信息]
	 * @return [type] [description]
	 */
	public function getError(){
		return $this->error;
	}

}