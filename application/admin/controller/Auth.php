<?php  
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Adminlist;
use think\Session;
class Auth extends Controller
{
	protected $is_login = [''];
	public function _initialize()
	{
		if (!$this->checkLogin() && in_array('*', $this->is_login)) {
			$this->error('没有登录',url('admin/auth/adminLogin'));
		}
	}
	public function adminLogin()
	{
		return $this->fetch('public/adminLogin');
	}
	public function checkLogin()
	{
		return session('?adminlist_Id');
	}
	public function dologin()
	{
		$adminInfo = Adminlist::get(['adminlist_name'=>$this->request->param('name'), 'adminlist_pwd'=>md5($this->request->param('password'))]);
		if ($adminInfo) {
			session('adminlist_Id',$adminInfo->adminlist_id);
			$this->success('登录成功',url('admin/index/index'));
		}else{
			$this->error('登陆失败',url('admin/auth/adminLogin'));
		}
	}
	public function loginOut()
	{
		Session::clear();
		$this->redirect('adminLogin');
	}
}
?>