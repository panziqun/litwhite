<?php  
namespace app\admin\controller;

class Plate extends Auth{
	function plateSet()
	{
		return $this->fetch();
	}
	function plateHidden()
	{
		return $this->fetch();
	}
}
?>