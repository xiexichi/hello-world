// 严格模式
'use strict';

// 引入js
var ENCRYPT = require('encrypt.js');
var UTIL = require('util.js');
var hash = require('hash.js');
var VERSION = 'v3';
var dev = false;

// 接口URL
var BASE_URL = "https://api.25boy.cn/";
// websocket地址
var WEBSOCKET_URL = 'wss://api.25boy.com/cs';
// 新api接口
var newAPI = 'https://user.25boy.cn/';
var hotelAPI = 'https://api.inn.25boy.cn/';

// 本地调试环境
if( dev === true ){
  BASE_URL = "http://api.25boy.com.cn/";
  // BASE_URL = "http://boy25.com/";
  WEBSOCKET_URL = 'wss://api.25boy.com/cs';
  newAPI = 'http://user.25boy.com/';
  hotelAPI = 'http://hotel.25boy.com/';
}

var APPID = 'wx78708ce2d3dbf896';
var APITOKEN = 'XqmyZkrNlrfaZsOxN!@UZ1XeUcwmn3vD';

// 获取json数据
function getJSON(params,callback,url) {
	if(url == undefined){
		url = ''
	}

	// 第三方自动登录userToken
	const userToken = wx.getStorageSync('userToken');
	params.userToken = userToken;

	// 加入来源识别
	if(params.connport == undefined || params.connport == ''){
		params.connport = 'weapp'
	}
	// sessionId
	if(params.sessionId == undefined){
		params.sessionId = '';
	}
	// api_token
	var timestamp = Math.round(new Date().getTime()/1000);
	var sessionId = params.sessionId ? params.sessionId : '';
	params.token = ENCRYPT.hex_md5(APITOKEN+timestamp+sessionId).toUpperCase();
	params.timestamp = timestamp;
	params.ver = VERSION;

	// console.log('api query: ', params);
	
	wx.request({
		url:BASE_URL+url,
		data:params,
		method:'GET',
		header: {"content-type":"application/json"},
		complete: function (res) {
			requestResData(res,callback)
		}
	})
}

// 向服务器提交数据
function postDATA(params,callback,url) {
	if(url == undefined){
		url = ''
	}
	// 第三方自动登录userToken
	const userToken = wx.getStorageSync('userToken');
	params.userToken = userToken;

	// 加入来源识别
	if(params.connport == undefined || params.connport == ''){
		params.connport = 'weapp'
	}
	// sessionId
	if(params.sessionId == undefined){
		params.sessionId = '';
	}
	// api_token
	var timestamp = Math.round(new Date().getTime()/1000);
	var sessionId = params.sessionId ? params.sessionId : '';
	params.token = ENCRYPT.hex_md5(APITOKEN+timestamp+sessionId).toUpperCase();
	params.timestamp = timestamp;
	params.ver = VERSION;

	wx.request({
		url:BASE_URL+url,
		header: {"content-type":"application/x-www-form-urlencoded"},
		method:'POST',
		data:json2Form(params),
		complete: function (res) {
			requestResData(res,callback)
		}
	});
}


// 新api接口
function request(params,callback, url) {

	// 签名
	var APIKEY = "weapp";
	var URL_TOKEN = '9BGunxjLuKU*7SkjLeii03*Xlt13cTJW';
	var TIMESTAMP = Math.round(new Date().getTime()/1000);
	var SIGNATURE = hash.SHA256(APIKEY + TIMESTAMP + URL_TOKEN + TIMESTAMP);
	var headers = {};
	headers['content-Type'] = 'application/json';
	headers['api-key'] = APIKEY;
    headers['timestamp'] = TIMESTAMP;
    headers['signature'] = SIGNATURE;

     // 加入会员身份apiCookie
     var apiCookie = '';
    if( !apiCookie ) apiCookie = wx.getStorageSync('newApiCookie');
    if( apiCookie ) headers['Cookie'] = apiCookie;

	if(url == undefined){
		url = ''
	}

	// 第三方自动登录userToken
	if(params.token == undefined || params.token == ''){
		const userToken = wx.getStorageSync('userToken');
		params.token = userToken;
	}

	switch(params.biz){
		case 'hotel':
			url = hotelAPI + url;
			break;
		default:
			url = newAPI + url;
			break;
	}

	wx.request({
		url:url,
		data:params,
		method: params.method || 'POST',
		header: headers,
		success: function (json) {
			if( UTIL.isNull(json.header['Set-Cookie']) == false ){
		        // 保存apicookie
		        wx.setStorageSync('newApiCookie',json.header['Set-Cookie']);
		    }
		    typeof(callback) == 'function' && callback(json);
		}
	})
}


function requestResData(res,callback){
	if(res.errMsg.indexOf("request:fail") >= 0 ){
		var res = {
			data:{
				code:-9
			}
		}
	}
	if(res.data.code == -4001){
		wx.showModal({
			title:'提示',
			content:res.data.msg,
			showCancel:false,
			success:function(rs){
				// 清除用户相关缓存
				UTIL.removeUserCache();
				if(rs.confirm){
					wx.navigateTo({
		              url:'/pages/public/login?gourl=close&infun=invalid'
		            })
				}
			}
		})
	}
	callback(res)
}


// json对象转换为post提交可用form参数
function json2Form(json) {  
    var str = [];  
    for(var p in json){  
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(json[p]));  
    }  
    return str.join("&");  
}


/***** 判断是否为json对象 *******
* @param obj: 对象（可以是jq取到对象）
* @return isjson: 是否是json对象 true/false
*/
function isJson(obj){
  var isjson = typeof(obj) == "object" && Object.prototype.toString.call(obj).toLowerCase() == "[object object]" && !obj.length;    
  return isjson;
}

module.exports = {
	BASE_URL: BASE_URL,
	getJSON: getJSON,
	postDATA: postDATA,
	APPID:APPID,
	WEBSOCKET_URL:WEBSOCKET_URL,
	request:request
}