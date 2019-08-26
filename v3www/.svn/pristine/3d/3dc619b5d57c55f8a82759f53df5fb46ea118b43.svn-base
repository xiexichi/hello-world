// 会员登录类
import STORE from '../store/index'
import UTIL from './util'
import HELPER from './helper'

export default class User {
	// 用户信息
	static userInfo
	static login_backurl = '/pages/usres/index'
	
	/**
	* 检查登录
	*/
	static checkLogin () {
		const member = STORE.state.member
		const user_id = member.user_id || ''
		if (user_id === undefined || user_id === '') {
			return false;
		} else {
			return user_id;
		}
	}
	
	/**
	 * 设置用户信息（微信用户信息）
	 */
	static setUserInfo (data) {
		this.userInfo = data
	}
	
	/**
	 * 登录提示信息
	 */
	static showLoginTips (backurl, type) {
		// 登录页面地址
		const loginurl = '/pages/public/login'
		// 登录后返回页面
		const pages = getCurrentPages()
		const currentIndex = pages.length - 1
		const page = pages[currentIndex]
		const query = UTIL.parseParam(page.options)
		if (backurl === undefined || backurl === '') {
			backurl = '/' + page.route
			if (! UTIL.empty(query)) {
				// 加上参数
				backurl += '?' + query
			}
		}
		this.login_backurl = backurl
		uni.showModal({
			title: '提示',
			content: '此操作需要登录后进行',
			confirmText: '登录',
			success (res) {
				if (res.confirm) {
					// 跳转到登录页面
					HELPER.goto(loginurl, type)
				}
			}
		})
	}
}
