<?php  
namespace app\index\controller;

use think\Controller;
use app\index\model\User;
use app\index\model\Userinfo;
use app\index\model\Usersm;
use app\index\model\Addr;
use think\Session;
use think\Image;
use myhelp\Phoneyz;
class Set extends Controller{
	protected $user;
	protected $userinfo;
	protected $usersm;
	protected $addr;
	public function _initialize()
	{
		parent::_initialize();
		$this->user = new User();
		$this->userinfo = new Userinfo();
		$this->usersm = new Usersm();
		$this->addr = new Addr();
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
		$file = request()->file('image');
		if($file){
			$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
			if($info){
				// 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
				$img = $info->getSaveName();
			}
		}
		$realimg = ROOT_PATH . 'public' . DS . 'uploads/' . $img;
		$image = Image::open($realimg);
		$image->thumb(125, 125)->save($realimg);
		$userinfo = $this->userinfo->get(['user_id'=>Session::get('user_id')]);
		if ($userinfo) {
			$userinfo->save(['userinfo_headi'=>$img]);
		}else{
			$this->userinfo->save(['user_id'=>Session::get('user_id'),'userinfo_headi'=>$img]);
		}
		Session::set('userinfo_headi',$img);
		$this->redirect('setBind');
	}
	//获取手机验证码
	public function getPhone()
	{
		if ($this->request->param('phone_number')) {
			Session::set('phone_code',Phoneyz::getYzm($this->request->param('phone_number')));
		}
	}
	//检查手机验证码
	public function checkPhonecode()
	{
		if ($this->request->param('phone_code') == Session::get('phone_code')) {
			return '验证码正确';
		}else{
			return '验证码错误';
		}
	}
	//存储手机号码
	public function savePhone()
	{
		$user = $this->user->get(Session::get('user_id'));
		$user->save(['user_phone'=>$this->request->param('user_phone')]);
	}
	public function checkPrepwd()
	{
		$user_pwd = md5($this->request->param('user_pwd'));
		$user = $this->user->get(Session::get('user_id'));
		if ($user->user_pwd == $user_pwd) {
			return '密码正确';
		}else{
			return '密码错误';
		}
	}
	public function updatePwd()
	{
		$pwd = md5(trim($this->request->param('user_pwd')));
		$user = $this->user->get(Session::get('user_id'));
		$user->save(['user_pwd'=>$pwd]);
		Session::clear();
	}
	public function setProfile()
	{
		$user = $this->user->get(Session::get('user_id'));
		$userinfo = $this->userinfo->get(['user_id'=>Session::get('user_id')]);
		$this->assign(['user'=>$user,'userinfo'=>$userinfo]);
		return $this->fetch();
	}
	public function saveProfile()
	{
		$user = $this->user->get(Session::get('user_id'));
		$userinfo = $this->userinfo->get(['user_id'=>Session::get('user_id')]);
		$arr = $this->request->param();
		unset($arr['user_name']);
		if ($userinfo) {
			$userinfo->save($arr,['user_id'=>Session::get('user_id')]);
		}else{
			$arr['user_id'] = Session::get('user_id');
			$this->userinfo->save($arr);
		}
		$user->save(['user_name'=>$this->request->param('user_name')]);
		Session::set('user_name',$this->request->param('user_name'));
		$this->redirect('setProfile');
	}
	public function setSm()
	{
		$user = $this->user->get(Session::get('user_id'));
		$userinfo = $this->userinfo->get(['user_id'=>Session::get('user_id')]);
		$usersm = $this->usersm->get(['user_id'=>Session::get('user_id'),'usersm_status'=>1]);
		$this->assign(['usersm'=>$usersm,'user'=>$user,'userinfo'=>$userinfo]);
		return $this->fetch();
	}
	public function setAddr()
	{
		//查头像和用户名
		$user = $this->user->get(Session::get('user_id'));
		$userinfo = $this->userinfo->get(['user_id'=>Session::get('user_id')]);
		$this->assign(['user'=>$user,'userinfo'=>$userinfo]);
		//查询收货地址
		$addr = $this->addr->all(['user_id'=>Session::get('user_id')]);
		$this->assign('addr',$addr);
		return $this->fetch();
	}
	//添加地址
	public function addAddr()
	{
		
		$arr = $this->request->param();
		$arr['user_id'] = Session::get('user_id');
		if ($this->addr->get(['user_id',Session::get('user_id')])) {
		}else{
			$arr['addr_select'] = 1;
		}
		$this->addr->save($arr);
		$this->redirect('setAddr');
	}
	//设置默认地址
	public function setAddrSelected()
	{
		$addr_id = $this->request->param('addr_id');
		$addrs = $this->addr->all(['user_id'=>Session::get('user_id')]);
		foreach ($addrs as $value) {
			$value->save(['addr_select'=>0]);
		}
		$addr = $this->addr->get($addr_id);
		$addr->save(['addr_select'=>1]);
	}
	public function addrDel()
	{
		$addr_id = $this->request->param('addr_id');
		$this->addr->destroy($addr_id);
		$addr = $this->addr->get(['user_id'=>Session::get('user_id')]);
		if ($addr) {
			$addr->save(['addr_select'=>1]);
		}
	}
}
?>