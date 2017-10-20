<?php  
namespace app\admin\controller;

class Note extends Auth{
	function noteList()
	{
		return $this->fetch();
	}
	function noteDetail()
	{
		return $this->fetch();
	}
}
?>