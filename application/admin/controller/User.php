<?php  
namespace app\admin\controller;

class User extends Auth{
	function clients()
	{
		return $this->fetch();
	}
	function clientsCourseDetail()
	{
		return $this->fetch();
	}
	function clientsCourseList()
	{
		return $this->fetch();
	}
	function clientsHidden()
	{
		return $this->fetch();
	}
}
?>