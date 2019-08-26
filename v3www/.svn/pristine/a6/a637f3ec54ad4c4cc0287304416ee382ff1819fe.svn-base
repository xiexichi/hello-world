import Fly from 'flyio/dist/npm/wx'
import util from './util'
import time from './time'
import hash from './hash'
import store from '../store/index'

// 请求实例
let request = new Fly()

// 基本url
// const baseUrlApi = 'https://api.25boy.cn/index.php?'
const baseUrlApi = 'http://api.25boy.com.cn'

// api签名
const APIKEY = 'weapp'
const URL_TOKEN = '9BGunxjLuKU*7SkjLeii03*Xlt13cTJW'
const TIMESTAMP = time.timestamp()
const SIGNATURE = hash.SHA256(APIKEY + TIMESTAMP + URL_TOKEN + TIMESTAMP)

// //定义公共headers
request.config.headers['Content-Type'] = 'application/json'
// //设置超时
request.config.timeout = 20000
// //设置请求基地址
request.config.baseURL = baseUrlApi

// 请求拦截
request.interceptors.request.use((request) => {
  // url参数
  if (request.url && typeof (request.url) === 'string') {
    request.params = util.url2apiparam(request.url)
		request.url = 'index.php'
  }
  // 请求url
  if (typeof (request.body) === 'object' && request.body.baseUrl !== undefined && request.body.baseUrl) {
		request.baseURL = request.body.baseUrl
		delete request.body.baseUrl
  }
	// post请求
	if (request.method === 'POST')
		request.headers['Content-Type'] = 'application/x-www-form-urlencoded'
	
  // 加入cookie
  const cookie = store.state.cookie
  if (cookie) request.headers['cookie'] = cookie
	
  // 加入api签名
  if (request.baseURL.search('user.25boy.cn') !== -1) {
    request.headers['api-key'] = APIKEY
    request.headers['signature'] = SIGNATURE
  }
	request.headers['timestamp'] = time.timestamp()
  
  return request
})

// 响应拦截
request.interceptors.response.use(
  (response) => {
    // 保存cookie
    if (response.headers['set-cookie'])
      store.commit('setCookie', response.headers['set-cookie'][0])
    
    // 检查登录
    if (response.data.code === 80008) {
      // 延迟跳转防止页面未准备好导致错误：navigateTo with an already exist webviewId
      setTimeout(function () {
        uni.redirectTo({
          url: '/pages/login/main'
        })
      }, 500)
    }
    // 只将请求结果的data字段返回
    if (typeof (response.data) === 'string') {
      response.data = JSON.parse(response.data)
    }
    return response.data
    // return promise.resolve(response.data)
  },
  (err) => {
		uni.hideLoading()
		uni.showToast({
			title: err.message
		})
    console.log('网络请求错误', err)
  }
)

export default request
