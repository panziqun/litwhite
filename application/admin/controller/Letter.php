<?php  
namespace app\admin\controller;

use think\Session;
class Letter extends Auth{
	protected $is_login = ['*'];
	public function _initialize()
	{
		parent::_initialize();
	}
	public function letterList()
	{
		if (Session::get('admin_html') != 'super' && strpos(Session::get('admin_html'), '私信列表') === false) {
			$this->error('没有权限');
		}
		return $this->fetch();
	}
	public function letterSent()
	{
		if (Session::get('admin_html') != 'super' && strpos(Session::get('admin_html'), '添加私信') === false) {
			$this->error('没有权限');
		}
		return $this->fetch();
	}
	public function letterView()
	{
		return $this->fetch();
	}
}
?>