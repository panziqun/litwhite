<?php  
namespace app\index\controller;
use think\Controller;
class Index extends Controller{
	public function index()
	{
		return $this->fetch();
	}
	public function indexList()
	{
		return $this->fetch();
	}
	public function indexSearch()
	{
		return $this->fetch();
	}
}
?>