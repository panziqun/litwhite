<?php  
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Adminlist;
use think\Session;
use app\admin\model\AdminlistAdminrole;
use app\admin\model\Adminrole;
use app\admin\model\AdminroleAdminlimit;
use app\admin\model\Adminlimit;
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
			$adminlistadminrole = AdminlistAdminrole::get(['adminlist_id'=>$adminInfo->adminlist_id]);
			if ($adminlistadminrole) {
				$adminrole_id = $adminlistadminrole->adminrole_id;
				$adminlimit_ids = AdminroleAdminlimit::all(['adminrole_id'=>$adminrole_id]);
				$str3 = '';
				foreach ($adminlimit_ids as $key => $value) {
					$adminlimit_id = $value->adminlimit_id;
					$adminhtml = Adminlimit::get($adminlimit_id);
					$str = $adminhtml->adminlimit_html;
					$str3 .= $str . ',';
				}
				$str3 = rtrim($str3, ',');
			}else{
				$str3 = "super";
			}
			Session::set('admin_html',$str3);
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