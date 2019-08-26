<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Weibo_client {

	public function __construct($config = array()){

		if(!empty($config)){
			$this->initialize($config);
		}else{
			$this->ci =& get_instance();
			$this->ci->load->library('weibo_oauth');
		}
	}


	public function initialize($config){

		$this->ci =& get_instance();
		$this->ci->load->library('weibo_oauth',$config);

	}
	/**
	 * 获取当前登录用户及其所关注用户的最新微博消息。
	 *
	 * 获取当前登录用户及其所关注用户的最新微博消息。和用户登录 http://weibo.com 后在“我的首页”中看到的内容相同。同friends_timeline()
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/statuses/home_timeline statuses/home_timeline}
	 * 
	 * @access public
	 * @param int $page 指定返回结果的页码。根据当前登录用户所关注的用户数及这些被关注用户发表的微博数，翻页功能最多能查看的总记录数会有所不同，通常最多能查看1000条左右。默认值1。可选。
	 * @param int $count 每次返回的记录数。缺省值50，最大值200。可选。
	 * @param int $since_id 若指定此参数，则只返回ID比since_id大的微博消息（即比since_id发表时间晚的微博消息）。可选。
	 * @param int $max_id 若指定此参数，则返回ID小于或等于max_id的微博消息。可选。
	 * @param int $base_app 是否只获取当前应用的数据。0为否（所有数据），1为是（仅当前应用），默认为0。
	 * @param int $feature 过滤类型ID，0：全部、1：原创、2：图片、3：视频、4：音乐，默认为0。
	 * @return array
	 */
	public function home_timeline( $page = 1, $count = 100, $since_id = 0, $max_id = 0, $base_app = 0, $feature = 0 )
	{
		$params = array();
		if ($since_id) {
			$this->id_format($since_id);
			$params['since_id'] = $since_id;
		}
		if ($max_id) {
			$this->id_format($max_id);
			$params['max_id'] = $max_id;
		}
		$params['count'] = intval($count);
		$params['page'] = intval($page);
		$params['base_app'] = intval($base_app);
		$params['feature'] = intval($feature);


		return $this->ci->weibo_oauth->get('statuses/home_timeline', $params);
	}

	/**
	 * 根据用户UID或昵称获取用户资料
	 *
	 * 按用户UID或昵称返回用户资料，同时也将返回用户的最新发布的微博。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/users/show users/show}
	 * 
	 * @access public
	 * @param int  $uid 用户UID。
	 * @return array
	 */
	function show_user_by_id( $uid )
	{
		$params=array();
		if ( $uid !== NULL ) {
			$this->id_format($uid);
			$params['uid'] = $uid;
		}

		return $this->ci->weibo_oauth->get('users/show', $params );
	}


	/**
	 * OAuth授权之后，获取授权用户的UID
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/account/get_uid account/get_uid}
	 * 
	 * @access public
	 * @return array
	 */
	function get_uid()
	{
		return $this->ci->weibo_oauth->get( 'account/get_uid' );
	}

	protected function id_format(&$id) {
		if ( is_float($id) ) {
			$id = number_format($id, 0, '', '');
		} elseif ( is_string($id) ) {
			$id = trim($id);
		}
	}
}