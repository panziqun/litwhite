<?php  
namespace app\index\controller;

use think\Controller;
class Set extends Controller{
	public function setAddr()
	{
		return $this->fetch();
	}
	public function setBind()
	{
		return $this->fetch();
	}
	public function setProfile()
	{
		return $this->fetch();
	}
	public function setSm()
	{
		return $this->fetch();
	}
}
?>