<?php  
namespace app\index\controller;

use think\Controller;
class Detail extends Controller{
	public function messageLetter()
	{
		return $this->fetch();
	}
}
?>