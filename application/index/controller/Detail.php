<?php  
namespace app\index\controller;

use think\Controller;
class Detail extends Controller{
	public function detailCourse()
	{
		return $this->fetch();
	}
	public function detailPlay()
	{
		return $this->fetch();
	}
}
?>