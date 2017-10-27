<?php  
namespace app\index\controller;

use think\Controller;
use app\index\model\User;
use app\index\model\Userinfo;
use think\Session;
use think\Image;
class Set extends Controller{
	protected $user;
	protected $userinfo;
	public function _initialize()
	{
		parent::_initialize();
		$this->user = new User();
		$this->userinfo = new Userinfo();
	}
	public function setAddr()
	{
		return $this->fetch();
	}
	public function setBind()
	{
		$user_id = Session::get('user_id');
		$user = $this->user->get($user_id);
		$arr = $user->toArray();
		$userinfo = $this->userinfo->get(['user_id'=>$user_id]);
		if ($userinfo) {
			$userinfo = $userinfo->toArray();
			$arr['userinfo_headi'] = $userinfo['userinfo_headi'];
		}else{
			$arr['userinfo_headi'] = '';
		}
		if ($arr['user_phone']) {
			$arr['user_phone'] = '*******' . substr($arr['user_phone'], 7);
		}
		$this->assign('user',$arr);
		// dump($arr);
		return $this->fetch();
	}
	public function headi()
	{

	}
	public function setProfile()
	{
		return $this->fetch();
	}
	public function setSm()
	{
		return $this->fetch();
	}
}
?>