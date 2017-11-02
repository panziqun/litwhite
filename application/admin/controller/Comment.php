<?php  
namespace app\admin\controller;

use think\Session;
class Comment extends Auth{
	protected $is_login = ['*'];
	public function _initialize()
	{
		parent::_initialize();
	}
	public function commentList()
	{
		if (Session::get('admin_html') != 'super' && strpos(Session::get('admin_html'), '评论管理') === false) {
			$this->error('没有权限');
		}
		return $this->fetch();
	}
	public function commentReply()
	{
		return $this->fetch();
	}
}
?>