<?php  
namespace app\admin\controller;

class Index extends Auth{
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