<?php  
namespace app\admin\controller;

class Comment extends Auth{
	function commentList()
	{
		return $this->fetch();
	}
	function commentReply()
	{
		return $this->fetch();
	}
}
?>