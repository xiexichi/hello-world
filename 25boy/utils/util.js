// 严格模式
'use strict';

function formatTime(date) {
  var year = date.getFullYear()
  var month = date.getMonth() + 1
  var day = date.getDate()

  var hour = date.getHours()
  var minute = date.getMinutes()
  var second = date.getSeconds()


  return [year, month, day].map(formatNumber).join('/') + ' ' + [hour, minute, second].map(formatNumber).join(':')
}

function formatNumber(n) {
  n = n.toString()
  return n[1] ? n : '0' + n
}

// 分割数组
function arrayGroup(array, subGroupLength) {
    var index = 0;
    var newArray = [];

    while(index < array.length) {
        newArray.push(array.slice(index, index += subGroupLength));
    }

    return newArray;
}


/*
取图片src
思路分两步
1，匹配出图片img标签（即匹配出所有图片），过滤其他不需要的字符
2.从匹配出来的结果（img标签中）循环checkLogin匹配出图片地址（即src属性）
*/
function getImagesSrc(str){

    if(str == undefined || str == ''){
       return [];
    }

    //匹配图片（g表示匹配所有结果i表示区分大小写）
    var imgReg = /<img.*?(?:>|\/>)/gi;
    //匹配src属性
    var srcReg = /src=[\'\"]?([^\'\"]*)[\'\"]?/i;
    var arr = str.match(imgReg);

    if(arr == null || arr == ''){
        return []
    }

    var srcArr = new Array();
    for (var i = 0; i < arr.length; i++) {
      var src = arr[i].match(srcReg);
      //获取图片地址
      if(src[1]){
        srcArr.push(src[1]);
      }
    }

    return srcArr;
}

// 过滤特殊字符
function illegalChar(str,param)
{
    var reg = "[`~!@#\$%\^&\*\(\)\+<>\?:\"{},\/;'\[\\]]";
    if(param && param!=undefined){
        for(var i=0;i<param.length;i++){
            reg = reg.replace(param[i],'');
        }
    }
    var pattern=new RegExp(reg,'im');
    if(pattern.test(str)){
        return false;
    }
    return true;
}

// 验证手机号码
function checkPhone(phone){
    if(!(/^\d{11}$/.test(phone))){
        return false;
    }
    return true;
}
// 验证邮箱地址
function checkEmail(email){
    if(!(/^(.+)@(.+)$/.test(email))){
        return false;
    }
    return true;
}
// 验证密码
function checkPassword(str){
    if(!(/^([a-zA-Z0-9]|[-_]){6,16}$/.test(str))){
        return false;
    }
    return true;
}

// 判断字符长度 汉字算两个字符
function getByteLen(val) {
    var len = 0;
    for (var i = 0; i < val.length; i++) {
    var a = val.charAt(i);
      if (a.match(/[^\x00-\xff]/ig) != null) {
        len += 2;
      } else {
        len += 1;
      }
    }
    return len;
}

/*
** randomNum 产生任意长度随机数字组合
*/
function randomNum(len){
    var str = "",
        arr = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    for(var i=0; i<len; i++){
        var pos = Math.round(Math.random() * (arr.length-1));
        str += arr[pos];
    }
    return str;
}

/**
* 解析URL地址的JS方法
* 将数组转为url
**/
function parseParam(obj){
  var paramStr = '';
  for(var i in obj){
      paramStr += '&' + i + '=' + obj[i];
  }
  if(paramStr != ''){
      paramStr = paramStr.substr(1);
  }
  return paramStr;
}

/**
* 解析URL地址的JS方法
* 将url转为数组
**/
function parseURL(url) {
    url = url.substring(url.indexOf("?"),url.length);
    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
      var url = url.substr(1);
    }
    var strs = url.split("&");
    for(var i = 0; i < strs.length; i ++) {
      theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
    }
    return theRequest;
}

// 返回适合小程序的新链接
function newUrl (url) {
  var newUrl='',id='',cid='',A='',M='',type='',k='';
  if(url.id != undefined) id = url.id;
  if(url.cid != undefined) cid = url.cid;
  if(url.a != undefined) A = url.a;
  if(url.m != undefined) M = url.m;
  if(url.type != undefined) type = url.type;
  if(url.k != undefined) k = url.k;

  if(M == 'category'){
      if(A=='product'){
          newUrl = '/pages/products/index?id='+id;
      }else{
          newUrl = '/pages/products/list?id='+cid+'&type='+type+'&keyword='+k;
      }
  }else if(M == 'hd'){
        switch(A){
            case 'cz':
                newUrl = '/pages/users/recharge';
                break;
            case 'coupon':
                newUrl = '/pages/users/coupon';
                break;
            case 'promoteApply':
                newUrl = '/pages/single/promoteApply';
                break;
            case 'promoteCourse':
                newUrl = '/pages/single/promoteCourse';
                break;
            case 'redpack':
                newUrl = '/pages/single/redpack';
                break;
            case 'brand':
                newUrl = '/pages/products/list?brand_id='+id;
                break;
            case 'alanHea':
                newUrl = '/pages/single/alanHea';
                break;
            default:
                newUrl = '/pages/products/list';
                break;
        }
  }else if(M == 'account'){
      switch(A){
        case 'recharge':
            newUrl = '/pages/users/recharge';
            break;
        default:
            newUrl = '/pages/users/'+A;
            break;
      }
  }else if(M == 'sales'){
      newUrl = '/pages/single/sales?id='+id;
  }else if(M == 'share'){
      switch(A){
        case 'view':
            newUrl = '/pages/share/detail?id='+id;
            break;
        default:
            newUrl = '/pages/share/index';
            break;
      }
  }else if(M == 'o2o'){
    newUrl = '/pages/o2o/'+A;
    if( isNull(url.code) == false ){
      newUrl += '?code='+url.code;
    }
  }else{
      newUrl = '/pages/products/list';
  }
  return newUrl
}


function sleep(numberMillis) {
   var now = new Date();
   var exitTime = now.getTime() + numberMillis;
   while (true) {
       now = new Date();
       if (now.getTime() > exitTime)    return;
    }
}

function isNull(val, strict){
    // 0 == '' 是true
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


function inArray(needle,array,bool){
    if(typeof needle=="string"||typeof needle=="number"){
        var len=array.length;
        for(var i=0;i<len;i++){
            if(needle===array[i]){
                if(bool){
                    return i;
                }
                return true;
            }
        }
        return false;
    }
}


// 清除用户相关缓存
function removeUserCache(){
    wx.getStorageInfo({
        success: function(res){
            if(inArray('sessionKey', res.keys)){
                 wx.removeStorage({key:'sessionKey'});
            }
            if(inArray('userInfo', res.keys)){
                 wx.removeStorage({key:'userInfo'});
            }
            if(inArray('wxGetUserInfo', res.keys)){
                 wx.removeStorage({key:'wxGetUserInfo'});
            }
            if(inArray('wxGetUserInfoOutTime', res.keys)){
                 wx.removeStorage({key:'wxGetUserInfoOutTime'});
            }
            if(inArray('newApiCookie', res.keys)){
                 wx.removeStorage({key:'newApiCookie'});
            }
        }
    });
}

/*
*比较小程序基础库版本号
compareVersion('1.11.0', '1.9.9') // => 1 // 1 表示 1.11.0 比 1.9.9 要新
compareVersion('1.11.0', '1.11.0') // => 0 // 0 表示 1.11.0 和 1.9.9 是同一个版本
compareVersion('1.11.0', '1.99.0') // => -1 // -1 表示 1.11.0 比 1.99.0 要老
*/
function compareVersion(v1, v2) {
  v1 = v1.split('.')
  v2 = v2.split('.')
  var len = Math.max(v1.length, v2.length)

  while (v1.length < len) {
    v1.push('0')
  }
  while (v2.length < len) {
    v2.push('0')
  }

  for (var i = 0; i < len; i++) {
    var num1 = parseInt(v1[i])
    var num2 = parseInt(v2[i])

    if (num1 > num2) {
      return 1
    } else if (num1 < num2) {
      return -1
    }
  }

  return 0
}

module.exports = {
  formatTime: formatTime,
  arrayGroup: arrayGroup,
  getImagesSrc: getImagesSrc,
  illegalChar: illegalChar,
  checkPhone: checkPhone,
  checkEmail:checkEmail,
  checkPassword:checkPassword,
  getByteLen:getByteLen,
  randomNum:randomNum,
  parseParam:parseParam,
  parseURL:parseURL,
  newUrl:newUrl,
  sleep:sleep,
  isNull:isNull,
  inArray:inArray,
  removeUserCache:removeUserCache,
  compareVersion:compareVersion
}
