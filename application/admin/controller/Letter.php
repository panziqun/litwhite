<?php  
namespace app\admin\controller;

class Letter extends Auth{
	function letterList()
	{
		return $this->fetch();
	}
	function letterSent()
	{
		return $this->fetch();
	}
	function letterView()
	{
		return $this->fetch();
	}
}
?>