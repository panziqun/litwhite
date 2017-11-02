<?php  
namespace app\admin\controller;

use think\Session;
class Notice extends Auth{
	protected $is_login = ['*'];
	public function _initialize()
	{
		parent::_initialize();
	}
	public function noticeAdd()
	{
		if (Session::get('admin_html') != 'super' && strpos(Session::get('admin_html'), '添加通知') === false) {
			$this->error('没有权限');
		}
		return $this->fetch();
	}
	public function sent()
	{
		dump($this->request->param());
	}
	public function noticeList()
	{
		if (Session::get('admin_html') != 'super' && strpos(Session::get('admin_html'), '通知管理') === false) {
			$this->error('没有权限');
		}
		return $this->fetch();
	}
}
?>