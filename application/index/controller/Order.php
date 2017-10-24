<?php  
namespace app\index\controller;

use think\Controller;
class Detail extends Controller{
	public function orderCart()
	{
		return $this->fetch();
	}
	public function orderInfo()
	{
		return $this->fetch();
	}
}
?>