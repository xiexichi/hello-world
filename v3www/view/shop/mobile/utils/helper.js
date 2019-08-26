import UTIL from './util'

export default class Helper {
	/**
	 * 跳转到页面
	 */
	static goto (url, type) {
		const tabbar = [
			'/pages/index/index', '/pages/cart/index', '/pages/users/index'
		]
		if (UTIL.inArray(url, tabbar)) {
			type = 'switch'
		}
		console.log(type, url)
		switch (type) {
			case 'redirect':
				uni.redirectTo({
					url: url
				})
				break
			case 'switch':
				uni.switchTab({
					url: url
				})
				break
			case 'relaunch':
				uni.reLaunch({
					url: url
				})
				break
			default:
				uni.navigateTo({
					url: url
				})
				break
		}
	}
}
