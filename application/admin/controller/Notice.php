<?php  
namespace app\admin\controller;

class Notice extends Auth{
	public function noticeAdd()
	{
		return $this->fetch();
	}
	public function sent()
	{
		dump($this->request->param());
	}
	public function noticeList()
	{
		return $this->fetch();
	}
}
?>