<?php  
namespace app\index\controller;

use think\Controller;
use think\Validate;
use myhelp\Phoneyz;
use app\index\model\User;
use app\index\model\Userinfo;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// use app\index\model\EmailList;
use think\Session;
use think\Cookie;
use myhelp\SaeTOAuthV2;
use myhelp\SaeTClientV2;
use think\Image;
class Login extends Controller{
	const WB_AKEY = '3364510635';
	const WB_SKEY = '5dd72b3f89cbc622f3f0f8d94662b89d';
	const WB_CALLBACK_URL = 'http://www.litwhite.com/index/Login/weibo';
	protected $user;
	protected $mail;
	// protected $emailList;
	protected $userinfo;
	protected $o;
	public function _initialize()
	{
		$this->user = new User();
		$this->userinfo = new Userinfo();
		$this->o = new SaeTOAuthV2(self::WB_AKEY , self::WB_SKEY);
		$this->mail = new PHPMailer();
		//$this->emailList = new EmailList();

	}
	public function login()
	{
		$code_url = $this->o->getAuthorizeURL( self::WB_CALLBACK_URL );
		$this->assign('code_url',$code_url);
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
			$userinfo = $this->userinfo->get(['user_id'=>$user->user_id]);
			Session::set('user_id',$user->user_id);
			Session::set('user_name',$user->user_name);
			Session::set('userinfo_headi',$userinfo->userinfo_headi);
			$this->success('登录成功','Index/index');
		}else{
			$this->error('登录失败');
		}
	}
	public function weibo()
	{
		if (isset($_REQUEST['code'])) {
			$keys = array();
			$keys['code'] = $_REQUEST['code'];
			$keys['redirect_uri'] = self::WB_CALLBACK_URL;
			try {
				$token = $this->o->getAccessToken( 'code', $keys ) ;
			} catch (OAuthException $e) {

			}
		}

		if ($token) {
			Session::set('token',$token);
			// setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
			Cookie::set('weibojs_'.$this->o->client_id, http_build_query($token));
		}
		$arr = Session::get('token');
		$c = new SaeTClientV2( self::WB_AKEY , self::WB_SKEY , $arr['access_token'] );
		// dump($c);
		$ms  = $c->home_timeline(); // done
		$uid_get = $c->get_uid();
		$uid = $uid_get['uid'];
		$user_message = $c->show_user_by_id( $uid);//
		// dump($user_message);
		//存入数据库
		$user_weibo = $user_message['id'];
		$user_name = $user_message['screen_name'];
		$user = $this->user->get(['user_weibo'=>$user_weibo]);
		if ($user) {
			Session::set('user_id',$user->user_id);
			Session::set('user_name',$user->user_name);
			$user_id = $user->user_id;
			$userinfo = $this->userinfo->get(['user_id'=>$user_id]);
			Session::set('userinfo_headi',$userinfo->userinfo_headi);
		}else{
			$this->user->save(['user_weibo'=>$user_weibo,'user_name'=>$user_name]);
			$user_id = $this->user->user_id;
			Session::set('user_id',$user_id);
			Session::set('user_name',$user_name);
			$image = $user_message['profile_image_url'];
			// $image = Image::open($image);
			// //将图片裁剪为300x300并保存为crop.png
			// $haha = substr(str_shuffle(md5(str_shuffle('asdasd2423'))), 0, 8);
			// $image->save(ROOT_PATH . 'public' . DS . 'uploads/' . $haha .'.png');
			$this->userinfo->save(['user_id'=>$user_id,'userinfo_headi'=>$image]);
			Session::set('userinfo_headi',$image);
		}
		$this->redirect('index/index');
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
		$code_url = $this->o->getAuthorizeURL( self::WB_CALLBACK_URL );
		$this->assign('code_url',$code_url);
		return $this->fetch();
	}
	public function regSuccess()
	{
		$user_name = 'xiaobai_' . substr(str_shuffle(md5('asdq23234asd')), 0, 7);
		$user_pwd = md5($this->request->param('user_pwd'));
		if (strpos($this->request->param('user_phone'), '@')) {
			$user_email = $this->request->param('user_phone');
			$this->user->save(['user_name'=>$user_name, 'user_email'=>$user_email, 'user_pwd'=>$user_pwd]);
		}else{
			$user_phone = $this->request->param('user_phone');
			$this->user->save(['user_name'=>$user_name, 'user_phone'=>$user_phone, 'user_pwd'=>$user_pwd]);
		}
	}
	//获取手机验证码并返回
	public function getPhone()
	{
		return Phoneyz::getYzm($this->request->param('phone_number'));
	}
	//获取邮箱验证码
	public function getEmail()
	{
		$email  = $this->request->param('email');
		
        //Server settings
        $randNum = substr(str_shuffle('0123456789'),0,4);
        //$this->emailList->sendEmail($email, $randNum);
       	$this->sendEmail($email, $randNum);	
        return json_encode($randNum,JSON_UNESCAPED_UNICODE);
	}
	public function sendEmail($email, $randNum)
	{
		//$this->mail->SMTPDebug = 2;                                 // Enable verbose debug output
        $this->mail->isSMTP();                                      // Set mailer to use SMTP
        $this->mail->Host = 'smtp.aliyun.com';  // Specify main and backup SMTP servers
        $this->mail->SMTPAuth = true;                               // Enable SMTP authentication
        $this->mail->Charset = "UTF-8";
        $this->mail->Username = 'hp66722667@aliyun.com';                 // SMTP username
        $this->mail->Password = 'Hp123456';                           // SMTP password
        //Recipients
        $this->mail->setFrom('hp66722667@aliyun.com', 'litwhite');
        $this->mail->addAddress($email, 'der');     // Add a recipient
        $this->mail->addReplyTo('hp66722667@aliyun.com', 'litwhite');
        //Content
        $this->mail->isHTML(true);                                  // Set email format to HTML
        $this->mail->Subject = 'Here is the YZM';
        $this->mail->Body    = "This is the YZM message <b>$randNum!</b>";
        $this->mail->send();
	}
	public function loginOut()
	{
		Session::clear();
		$this->redirect('Index/index');
	}
}
?>