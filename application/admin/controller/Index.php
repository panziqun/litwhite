<?php  
namespace app\admin\controller;

class Index extends Auth{
	protected $is_login = ['*'];
	function index()
	{
		return $this->fetch();
	}
	function bigData()
	{
		return $this->fetch();
	}
	function homePage()
	{
		return $this->fetch();
	}
	function webSet()
	{
		return $this->fetch();
	}
}
?>