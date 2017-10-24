<?php  
namespace app\admin\controller;

use app\admin\model\User as UserModel;
use app\admin\model\Usersm;
use app\admin\model\Userinfo;
use app\admin\model\Addr;
use think\Db;
class User extends Auth{
	protected $usermodel;
	protected $usersm;
	protected $userinfo;
	protected $addr;
	public function _initialize()
	{
		parent::_initialize();
		$this->usermodel = new UserModel();
		$this->usersm = new Usersm();
		$this->userinfo = new Userinfo();
		$this->addr = new Addr();
	}
	public function clients()
	{
		//所有用户
		if ($this->request->param()) {
			$user_name = $this->request->param('user_name');
			$allUser = $this->usermodel->where('user_name','like',"%$user_name%")->paginate(10);
			$count = $this->usermodel->where('user_name','like',"%$user_name%")->count();
		}else{
			$allUser = $this->usermodel->paginate(10);
			$count = $this->usermodel->count();
		}
		$arr = [];
		foreach ($allUser as $key => $value) {
			$user = $value->toArray();
			$arr[$key] = $user;
			$usersmStatus = $this->usersm->get(['user_id'=>$user['user_id']]);
			if ($usersmStatus == null) {
				$arr[$key]['smStatus'] = '未认证';
			}elseif ($usersmStatus->usersm_status == '0') {
				$arr[$key]['smStatus'] = '等待验证';
			}else{
				$arr[$key]['smStatus'] = '已认证';
			}
		}
		$allPage = $allUser->render();
		$this->assign('allUser', $arr);
		$this->assign('allPage', $allPage);
		$this->assign('count', $count);
		return $this->fetch();
	}
	//获取用户的个人信息
	public function userInfo()
	{
		$id = $this->request->param('user_id');
		$user = $this->usermodel->get($id);
		$user = $user->toArray();
		$userinfo = $this->userinfo->get(['user_id'=>$id]);
		if ($userinfo != null) {
			$userinfo = $userinfo->toArray();
		}else{
			$userinfo = [];
		}
		$usersm = $this->usersm->get(['user_id'=>$id]);
		if ($usersm != null) {
			$usersm = $usersm->toArray();
		}else{
			$usersm = [];
		}
		$addr = $this->addr->all(['user_id'=>$id]);
		$addrArr = [];
		if ($addr != null) {
			foreach ($addr as $value) {
				$addrArr['addr'][] = $value->toArray();
			}
		}
		$allInfo = array_merge($user, $userinfo, $usersm, $addrArr);
		return json_encode($allInfo, JSON_UNESCAPED_UNICODE);
	}
	//用户加入黑名单
	public function userDel()
	{
		$user_id = $this->request->param('user_id');
		$this->usermodel->destroy($user_id);
	}
	//用户移除黑名单
	public function removeDel()
	{
		Db::table('lit_user')->where('user_id', $this->request->param('user_id'))->setField('delete_time', null);
	}
	public function clientsCourseDetail()
	{
		return $this->fetch();
	}
	public function clientsCourseList()
	{
		return $this->fetch();
	}
	//获取用户黑名单
	public function clientsHidden()
	{
		if ($this->request->param()) {
			$user_name = $this->request->param('user_name');
			$delUser = $this->usermodel->onlyTrashed()->where('user_name','like',"%$user_name%")->paginate(10);
			$delCount = $this->usermodel->onlyTrashed()->where('user_name','like',"%$user_name%")->count();
		}else{
			$delUser = $this->usermodel->onlyTrashed()->paginate(10);
			$delCount = $this->usermodel->onlyTrashed()->count();
		}
		$arr = [];
		foreach ($delUser as $key => $value) {
			$user = $value->toArray();
			$arr[$key] = $user;
			$usersmStatus = $this->usersm->get(['user_id'=>$user['user_id']]);
			if ($usersmStatus == null) {
				$arr[$key]['smStatus'] = '未认证';
			}elseif ($usersmStatus->usersm_status == '0') {
				$arr[$key]['smStatus'] = '等待验证';
			}else{
				$arr[$key]['smStatus'] = '已认证';
			}
		}
		$delPage = $delUser->render();
		$this->assign('delUser', $arr);
		$this->assign('delPage', $delPage);
		$this->assign('delCount', $delCount);
		return $this->fetch();
	}
}
?>