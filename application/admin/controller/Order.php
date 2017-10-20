<?php  
namespace app\admin\controller;

class Order extends Auth{
	function orderPaid()
	{
		return $this->fetch();
	}
	function orderFinish()
	{
		return $this->fetch();
	}
}
?>