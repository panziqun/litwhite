<?php  
namespace app\admin\controller;

class Notice extends Auth{
	function noticeAdd()
	{
		return $this->fetch();
	}
	function noticeList()
	{
		return $this->fetch();
	}
}
?>