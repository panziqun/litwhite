<?php  
namespace app\admin\controller;

class Notice extends Auth{
	protected $is_login = ['*'];
	public function _initialize()
	{
		parent::_initialize();
	}
	public function noticeAdd()
	{
		return $this->fetch();
	}
	public function sent()
	{
		dump($this->request->param());
	}
	public function noticeList()
	{
		return $this->fetch();
	}
}
?>