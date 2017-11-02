<?php  
namespace app\admin\controller;

use app\admin\model\Adminlist;
use app\admin\model\Adminrole;
use app\admin\model\AdminlistAdminrole;
use app\admin\model\Adminlimit;
use app\admin\model\AdminroleAdminlimit;
use think\Session;
class Admin extends Auth{
	protected $is_login = ['*'];
	protected $adminlist;
	protected $adminrole;
	protected $adminlist_adminrole;
	protected $adminlimit;
	protected $adminrole_adminlimit;
	public function _initialize()
	{
		parent::_initialize();
		$this->adminlist = new Adminlist();
		$this->adminrole = new Adminrole();
		$this->adminlist_adminrole = new AdminlistAdminrole();
		$this->adminlimit = new Adminlimit();
		$this->adminrole_adminlimit = new AdminroleAdminlimit();
	}
	public function adminAdd()
	{
		if ($this->request->get()) {
			$admin = $this->adminlist->get(['adminlist_name'=>$this->request->get('adminlist_name')]);
			if ($admin) {
				echo json_encode(['code'=>2,'data'=>'已存在']);
			}else{
				echo json_encode(['code'=>1, 'data'=>'√']);
			}
		}else{
			$this->adminlist->save(['adminlist_name'=>$this->request->post('adminlist_name'),
			'adminlist_pwd'=>md5($this->request->post('adminlist_pwd')),'adminlist_phone'=>$this->request->post('adminlist_phone')]);
		}
	}
	public function adminDel()
	{
		$this->adminlist->destroy($this->request->get('adminlist_id'));
		$this->adminlist_adminrole->destroy(['adminlist_id'=>$this->request->get('adminlist_id')]);
	}
	public function adminUpdate(){
		if ($this->request->get()) {
			$re1 = $this->adminlist->get($this->request->get('adminlist_id'));
			echo json_encode($re1->toArray());
		}
		if ($this->request->post()) {
			$arr = $this->request->post();
			dump($arr);
			$adminUpdateId = $arr['adminlist_id'];
			$arr['adminlist_pwd'] = md5($arr['adminlist_pwd']);
			unset($arr['adminlist_id']);
			$re2 = $this->adminlist->get($adminUpdateId);
			$re2->save($arr,['adminlist_id'=>$adminUpdateId]);
		}
	}
	public function adminList()
	{
		if (Session::get('admin_html') != 'super' && strpos(Session::get('admin_html'), '管理列表') === false) {
			$this->error('没有权限');
		}
		//部门实时替换
		if ($this->request->post()) {
			$roleSelect_adminid = $this->request->post('roleSelect_adminid');
			$roleSelect_roleid = $this->request->post('roleSelect_roleid');
			//更新部门
			if ($adminlist_adminroles = $this->adminlist_adminrole->get(['adminlist_id'=>$roleSelect_adminid])) {
				$adminlist_adminroles->adminrole_id = $roleSelect_roleid;
				$adminlist_adminroles->save();
			}else{//未没有部门的增加部门
				$this->adminlist_adminrole->adminlist_id = $roleSelect_adminid;
				$this->adminlist_adminrole->adminrole_id = $roleSelect_roleid;
				$this->adminlist_adminrole->save();
			}
			// 选择完实时显示部门
			// $realrole = $this->adminrole->get(['adminrole_id'=>$roleSelect_roleid]);
			// echo json_encode(['role'=>$realrole->adminrole_name]);
		}
		$adminList = [];
		$admin = $this->adminlist->all();
		foreach ($admin as $key => $value) {
			$adminList[$key]['adminlist_id'] = $value->adminlist_id;
			$adminList[$key]['adminlist_name'] = $value->adminlist_name;
			$adminList[$key]['adminlist_phone'] = $value->adminlist_phone;
			$adminList[$key]['adminrole_name'] = '';
			foreach ($value->adminToRole as $v) {
				$adminList[$key]['adminrole_name'] = $v->adminrole_name;
			}
		}
		$role = $this->adminrole->all();
		$adminRoles = [];
		foreach ($role as $key => $value) {
			$adminRoles[$key]['adminrole_id'] = $value->adminrole_id;
			$adminRoles[$key]['adminrole_name'] = $value->adminrole_name;
		}
		$this->assign([
			'adminRoles'=>$adminRoles,
			'adminList'=>$adminList
		]);
		dump(Session::get('adminhtml'));
		return $this->fetch();
	}











	public function adminRole()
	{
		if (Session::get('admin_html') != 'super' && strpos(Session::get('admin_html'), '部门管理') === false) {
			$this->error('没有权限');
		}
		//查询所有部门角色
		$adminrole = $this->adminrole->all();
		$arr = [];
		foreach ($adminrole as $key => $value) {
			$arr[$key] = $value->toArray();
			$adminrole_adminlimit = $this->adminrole_adminlimit->all(['adminrole_id'=>$arr[$key]['adminrole_id']]);
				$arr1 = [];
				foreach ($adminrole_adminlimit as $k => $v) {
					$arr4 = $v->toArray();
					$arr1[$k] = $arr4['adminlimit_id'];
				}
			$arr[$key]['limit'] = $arr1;
		}
		$this->assign('adminrole',$arr);
		//查询所有权限
		$adminlimit = $this->adminlimit->all();
		$arr3 = [];
		foreach ($adminlimit as $key => $value) {
			$arr3[$key] = $value->toArray();
		}
		$this->assign('adminlimit', $adminlimit);
		return $this->fetch();
	}
	public function adminRoleCheck()
	{
		$adminrole_name = $this->request->param('adminrole_name');
		$adminrole = $this->adminrole->get(['adminrole_name'=>$adminrole_name]);
		if ($adminrole) {
			return json_encode(['info'=>'已存在']);
		}else{
			return json_encode(['info'=>'√']);
		}
	}
	public function roleAdd()
	{
		$adminrole_name = $this->request->param('adminrole_name');
		$this->adminrole->save(['adminrole_name'=>$adminrole_name]);
	}
	public function limitSelect()
	{
		$adminrole_id = $this->request->param('adminrole_id');
		$adminlimit_id = $this->request->param('adminlimit_id');
		$adminrole_adminlimit = $this->adminrole_adminlimit->get(['adminrole_id'=>$adminrole_id,'adminlimit_id'=>$adminlimit_id]);
		if ($adminrole_adminlimit) {
			$this->adminrole_adminlimit->destroy(['adminrole_id'=>$adminrole_id,'adminlimit_id'=>$adminlimit_id]);
		}else{
			$this->adminrole_adminlimit->save(['adminrole_id'=>$adminrole_id,'adminlimit_id'=>$adminlimit_id]);
		}
	}
	public function roleDel()
	{
		$adminrole_id = $this->request->param('adminrole_id');
		$this->adminrole->destroy(['adminrole_id'=>$adminrole_id]);
		$this->adminrole_adminlimit->destroy(['adminrole_id'=>$adminrole_id]);
	}

	public function limitif(){
		$hadlimit = $this->adminlimit->get(['adminlimit_name'=>$this->request->get('adminlimit_name')]);
		if ($hadlimit) {
			return json_encode(['info'=>'已存在']);
		}else{
			return json_encode(['info'=>'√']);
		}
	}
	public function adminLimit()
	{
		if (Session::get('admin_html') != 'super' && strpos(Session::get('admin_html'), '权限管理') === false) {
			$this->error('没有权限');
		}
		if ($this->request->post()) {
			$this->adminlimit->save(['adminlimit_name'=>$this->request->post('adminlimit_name')]);
		}
		$adminlimits = $this->adminlimit->all();
		$arr = [];
		foreach ($adminlimits as $key => $value) {
				$arr[$value->adminlimit_id] = $value->adminlimit_name;
		}
		$this->assign(['adminlimits'=>$arr]);
		return $this->fetch();
	}
	public function limitInfo()
	{
		//添加权限
		if ($id = $this->request->post('adminlimit_id')) {
			$adminlimit_htmls = $this->adminlimit->get($id);
			if ($adminlimit_htmls->adminlimit_html == null) {
				$html = $this->request->post('adminlimit_html');
			}else{
				$html = $adminlimit_htmls->adminlimit_html . ',' . $this->request->post('adminlimit_html');
			}
			$adminlimit_htmls->save(['adminlimit_html'=>$html]);

		}
		//查看所有权限
		if ($this->request->get()) {
			$info = $this->adminlimit->get(['adminlimit_id'=>$this->request->get('adminlimit_id')]);
			$info = $info->toArray();
			if ($info['adminlimit_html'] != null) {
				$arr = explode(',', $info['adminlimit_html']);
				$arr1 = [];
				foreach ($arr as $value) {
					$arr1[] = $value;
				}
				$info['adminlimit_html'] = $arr1;
			}
			echo json_encode($info, JSON_UNESCAPED_UNICODE);
		}
	}
	//移除权限html
	public function htmlRemove()
	{
		if ($this->request->get()) {
			$id = $this->request->get('adminlimit_id');
			$info = $this->adminlimit->get($id);
			
			$array =  explode(',',$info->adminlimit_html);
			$arr = [];
			
			foreach ($array as $key => $value) {
				if (('移除' . $value) != $this->request->get('adminlimit_html')) {
					$arr[] = $value;
				}
			}
			$str = implode(',',$arr);
			$info->save(['adminlimit_html'=>$str]);
		}
	}
	//删除
	public function limitDel()
	{
		$this->adminlimit->destroy($this->request->param('adminlimit_id'));
	}
}
?>