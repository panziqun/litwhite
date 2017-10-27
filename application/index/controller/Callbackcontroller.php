<?php
namespace app\index\controller;
use think\Controller;
use kuange\qqconnect\QC;
use app\index\model\User;
class Callbackcontroller extends Controller
{
	public function _initialize()
	{
		$this->user = new User();
	}
    public function qqAction()
    {
        $qc = new QC();
        $access_token = $qc->qq_callback();    // access_token
        $openid = $qc->get_openid();     // openid
        $qc = new QC($access_token, $openid);
		$qq_user_info = $qc->get_user_info();
        $userInfo = $this->user->where('user_openid',$openid)->find();
        if ( $userInfo ) {
        	session('user_id',$userInfo->user_id);
        	session('user_name',$userInfo->user_name);
        } else {
        	$qc = new QC($access_token, $openid);
			$qq_user_info = $qc->get_user_info();
			$this->user->user_name = $qq_user_info['nickname'];
			$this->user->user_openid = $openid;
			$this->user->save();
			$user_id = $this->user->user_id;
			$userData = $$this->user->get($user_id);
			session('user_id',$user_id);
			session('user_name',$userData->user_name);
        }
        $this->success('登录成功', url('/'));


		  /*'ret' => int 0
		  'msg' => string '' (length=0)
		  'is_lost' => int 0
		  'nickname' => string '不温柔' (length=9)
		  'gender' => string '男' (length=3)
		  'province' => string '河北' (length=6)
		  'city' => string '秦皇岛' (length=9)
		  'year' => string '1900' (length=4)
		  'figureurl' => string 'http://qzapp.qlogo.cn/qzapp/101434960/2131C0DC901C45EB3E87098ED2D2342B/30' (length=73)
		  'figureurl_1' => string 'http://qzapp.qlogo.cn/qzapp/101434960/2131C0DC901C45EB3E87098ED2D2342B/50' (length=73)
		  'figureurl_2' => string 'http://qzapp.qlogo.cn/qzapp/101434960/2131C0DC901C45EB3E87098ED2D2342B/100' (length=74)
		  'figureurl_qq_1' => string 'http://q.qlogo.cn/qqapp/101434960/2131C0DC901C45EB3E87098ED2D2342B/40' (length=69)
		  'figureurl_qq_2' => string 'http://q.qlogo.cn/qqapp/101434960/2131C0DC901C45EB3E87098ED2D2342B/100' (length=70)
		  'is_yellow_vip' => string '0' (length=1)
		  'vip' => string '0' (length=1)
		  'yellow_vip_level' => string '0' (length=1)
		  'level' => string '0' (length=1)
		  'is_yellow_year_vip' => string '0' (length=1)
			dump($qq_user_info);
	
		'state' => string '4bd754b5001d643db223269ab9e373ad' (length=32)
		'access_token' => string 'CB2D7659AC7343F19A3A18639F389655' (length=32)
		'openid' => string '2131C0DC901C45EB3E87098ED2D2342B' (length=32)


		*/
    }
}