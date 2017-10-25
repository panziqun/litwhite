<?php  
namespace app\index\controller;

use think\Controller;
class Message extends Controller{
	public function messageLetter()
	{
		return $this->fetch();
	}
}
?>