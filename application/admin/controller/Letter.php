<?php  
namespace app\admin\controller;

class Letter extends Auth{
	protected $is_login = ['*'];
	public function _initialize()
	{
		parent::_initialize();
	}
	public function letterList()
	{
		return $this->fetch();
	}
	public function letterSent()
	{
		return $this->fetch();
	}
	public function letterView()
	{
		return $this->fetch();
	}
}
?>