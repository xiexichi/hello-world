/* eslint-disable */
export default {
	trim: function(str, char, type) {
		if (char) {
			if (type === 'left') {
				return str.replace(new RegExp('^\\' + char + '+', 'g'), '')
			} else if (type === 'right') {
				return str.replace(new RegExp('\\' + char + '+$', 'g'), '')
			}
			return str.replace(new RegExp('^\\' + char + '+|\\' + char + '+$', 'g'), '')
		}
		return str.replace(/^\s+|\s+$/g, '')
	},
	
	/**
	 * url转为api用的请求方式
	 */
	url2apiparam: function(url = '') {
		url = this.trim(url, '/')
		const temp = url.split('/')
		return {
			'c': temp[0],
			'a': temp[1]
		}
	},
	
	/**
	 * 数组转为字符串参数
	 */
	/**
	* 解析URL地址的JS方法
	* 将数组转为url
	**/
	parseParam: function(obj){
	  var paramStr = '';
	  for (var i in obj){
	      paramStr += '&' + i + '=' + obj[i]
	  }
	  if (paramStr != ''){
	      paramStr = paramStr.substr(1)
	  }
	  return paramStr
	},

	/**
	 * 处理分页方法
	 */
	pages: function(data, callback) {
		const current = parseInt(data.current)
		const page = parseInt(data.pages)
		// 回传
		typeof(callback === 'function') && callback(page > current)
	},

	/**
	 * 判断数组是否有该元素
	 * @param [arr] 数组
	 * @param [key] 查找的值
	 */
	inArray: function(key, arr) {
		for (var x in arr)
			if (arr[x] === key)
				return true;
		return false;
	},

	/**
	 * 删除数据中指定元素
	 */
	delArray: function(key, arr) {
		// 首先需要找到元素的下标
		var index = arr.indexOf(key)
		// 使用splice函数进行移除
		if (index > -1) {
			return arr.splice(index, 1)
		}
		return arr
	},

	/**
	 * 平台差异化处理
	 */
	platformCompile: function(name) {
		console.log(name)
		switch (name) {
			// 生物认证
			case 'soterAuthentication':
				// #ifdef MP-WEIXIN
				uni.checkIsSupportSoterAuthentication({
					success(res) {
						console.log(res)
					}
				})
				// #endif
				break
		}
	},

	/*
		取图片src
		思路分两步
		1，匹配出图片img标签（即匹配出所有图片），过滤其他不需要的字符
		2.从匹配出来的结果（img标签中）循环匹配出图片地址（即src属性）
		*/
	getImagesSrc: function(str) {
		if (str == undefined || str == '') {
			return []
		}

		//匹配图片（g表示匹配所有结果i表示区分大小写）
		var imgReg = /<img.*?(?:>|\/>)/gi
		//匹配src属性
		var srcReg = /src=[\'\"]?([^\'\"]*)[\'\"]?/i
		var arr = str.match(imgReg)
		if (arr == null || arr == '') {
			return []
		}
		var srcArr = new Array()
		for (var i = 0; i < arr.length; i++) {
			var src = arr[i].match(srcReg)
			//获取图片地址
			if (src[1]) {
				srcArr.push(src[1])
			}
		}
		return srcArr
	},
	
	/**
	 * 判断是否空
	 */
	empty: function (val, strict){
		// 0 == '' 是 true
		var strict = arguments[1] ? arguments[1] : false;
		if (strict) {
			if (val === '' || val == undefined)
				return true;
		else
			return false;
		}else {
			if (val == '' || val == undefined || val == "undefined")
				return true;
			else
				return false;
		}
	}
}
