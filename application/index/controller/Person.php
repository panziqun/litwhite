<?php  
namespace app\index\controller;

use think\Controller;
class Person extends Controller{
	public function personCommit()
	{
		return $this->fetch();
	}
	public function personCourse()
	{
		return $this->fetch();
	}
}
?>