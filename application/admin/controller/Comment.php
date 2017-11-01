<?php  
namespace app\admin\controller;

class Comment extends Auth{
	protected $is_login = ['*'];
	public function _initialize()
	{
		parent::_initialize();
	}
	public function commentList()
	{
		return $this->fetch();
	}
	public function commentReply()
	{
		return $this->fetch();
	}
}
?>