<?php  
namespace app\admin\controller;

use app\admin\model\Adminlist;
class Admin extends Auth{
	protected $is_login = ['*'];
	protected $adminlist;
	public function _initialize()
	{
		parent::_initialize();
		$this->adminlist = new Adminlist();
	}
	public function adminAdd()
	{
		$this->adminlist->save(['adminlist_name'=>$this->request->param('adminlist_name'),
			'adminlist_pwd'=>$this->request->param('adminlist_pwd'),'adminlist_phone'=>$this->request->param('adminlist_phone')]);
	}
	public function adminList()
	{

		return $this->fetch();
	}
	public function adminRole()
	{
		return $this->fetch();
	}
	public function adminLimit()
	{
		return $this->fetch();
	}
}
?>