<?php  
namespace app\index\controller;

use think\Controller;
use think\Validate;
use myhelp\Phoneyz;
use app\index\model\User;
class Login extends Controller{
	protected $user;
	public function _initialize()
	{
		$this->user = new User();
	}
	public function login()
	{
		return $this->fetch();
	}
	public function loginDeal()
	{
		$user_pwd = md5($this->request->param('user_pwd'));
		if (strpos($this->request->param('user_email_phone'), '@')) {
			$user_email = $this->request->param('user_email_phone');
			$user = $this->user->get(['user_email'=>$user_email,'user_pwd'=>$user_pwd]);
		}else{
			$user_phone = $this->request->param('user_email_phone');
			$user = $this->user->get(['user_phone'=>$user_phone,'user_pwd'=>$user_pwd]);
		}
		if ($user) {
			$this->success('登录成功','Index/index');
		}else{
			$this->error('登录失败');
		}
	}
	//检查手机或者邮箱
	public function checkEmailPhone()
	{
		if (strpos($this->request->param('email_phone'), '@')) {
			if ($this->user->get(['user_email'=>$this->request->param('email_phone')])) {
				return '该邮箱已注册';
			}
			$validate = new Validate([
			'email|邮箱' => 'email'
			]);
			$data = [
			'email' => $this->request->param('email_phone'),
			];
			if (!$validate->check($data)) {
				return $validate->getError();
			}else{
				return "邮箱格式正确";
			}
		}else{
			if ($this->user->get(['user_phone'=>$this->request->param('email_phone')])) {
				return '该手机已注册';
			}
			$pattern = '/^1(3|4|5|7|8)\d{9}$/';
			if (preg_match($pattern, $this->request->param('email_phone'))) {
				return '手机格式正确';
			}else{
				return "手机格式错误";
			}
		}
		
	}
	//检查图片验证码
	public function checkCap()
	{
		$validate = new Validate([
		'captcha|验证码' => 'require|captcha'
		]);
		$data = [
		'captcha' => $this->request->param('cap'),
		];
		if (!$validate->check($data)) {
			return $validate->getError();
		}else{
			return "验证码正确";
		}
	}
	public function register()
	{
		return $this->fetch();
	}
	public function regSuccess()
	{
		$user_name = 'xiaobai_' . substr(str_shuffle(md5('asdq23234asd')), 0, 7);
		$user_phone = $this->request->param('user_phone');
		$user_pwd = md5($this->request->param('user_pwd'));
		$this->user->save(['user_name'=>$user_name, 'user_phone'=>$user_phone, 'user_pwd'=>$user_pwd]);
	}
	//获取手机验证码并返回
	public function getPhone()
	{
		return Phoneyz::getYzm($this->request->param('phone_number'));
	}
}
?>