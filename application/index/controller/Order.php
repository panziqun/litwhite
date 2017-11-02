<?php  
namespace app\index\controller;

use think\Controller;
use app\index\model\Shopcar;
use app\index\model\Course;
use app\index\model\Addr;
use app\index\model\Order as OrderModel;
use think\Session;
use think\Db;
class Order extends Controller{
	protected $shopcar;
	protected $course;
	protected $addr;
	protected $order;
	public function _initialize()
	{
		parent::_initialize();
		$this->shopcar = new Shopcar();
		$this->course = new Course();
		$this->addr = new Addr();
		$this->order = new OrderModel();
	}
	public function orderCart()
	{
		$user_id = Session::get('user_id');
		$shopcar = $this->shopcar->all(['user_id'=>$user_id]);
		if ($shopcar) {
			$arr = [];
			foreach ($shopcar as $key => $value) {
				$arr2 = $value->toArray();
				$course_id = $value['course_id'];
				$course = $this->course->get($course_id);
				$course = $course->toArray();
				$arr2 = array_merge($course,$arr2);
				$arr[] = $arr2;
			}
		}else{
			$arr = '';
		}
		$this->assign('shopcar',$arr);
		return $this->fetch();
	}
	public function addCart()
	{
		$course_id = $this->request->param('course_id');
		$user_id = Session::get('user_id');
		$this->shopcar->save(['user_id'=>$user_id,'course_id'=>$course_id]);
	}
	public function cartDel()
	{
		$this->shopcar->destroy($this->request->param('shopcar_id'));
	}
	public function orderSur()
	{
		//拿到课程基本信息
		$course_ids = trim($this->request->param('course_ids'),',');
		$arr = explode(',', $course_ids);
		$array = [];
		$price = 0;
		foreach ($arr as $value) {
			$course = $this->course->get($value);
			$array[] = $course->toArray();
			$price += $course->course_rate;
		}
		//收货地址
		$addr = $this->addr->all(['user_id'=>Session::get('user_id')]);
		$this->assign(['course'=>$array,'price'=>$price]);
		$this->assign('addr',$addr);
		$this->assign('order_id',"XB" . date("ymd_His") . rand(10, 99));
		return $this->fetch('ordersur');
	}
	public function sendPayOrd()
	{	
		$order_id = $this->request->param('p2_Order');
		$user_id = Session::get('user_id');
		$arr = input('post.order_course/a');
		$order_course = implode(',', $arr);
		$order_money = $this->request->param('p3_Amt');
		$addr_id = $this->request->param('addr_id');
		$addr = $this->addr->get($addr_id);
		$order_name = $addr->addr_name;
		$order_address = $addr->addr_city . $addr->addr_address;
		$order_phone = $addr->addr_phone;
		$this->order->save(['order_id'=>$order_id,'user_id'=>$user_id,'order_course'=>$order_course,'order_money'=>$order_money,'order_name'=>$order_name,'order_address'=>$order_address,'order_phone'=>$order_phone]);
		foreach ($arr as $key => $value) {
			$this->shopcar->destroy(['user_id'=>$user_id,'course_id'=>$value]);
		}
		//发送支付这块
		include 'yeepayCommon.php';
		$data = array();
		#业务类型
		$data['p0_Cmd']				= "Buy";
		#	商户订单号,选填.
		$data['p1_MerId']     = $p1_MerId;
		##若不为""，提交的订单号必须在自身账户交易中唯一;为""时，易宝支付会自动生成随机的商户订单号.
		$data['p2_Order']			= $this->request->param('p2_Order');
		#	支付金额,必填.
		##单位:元，精确到分.
		$data['p3_Amt']			  = $this->request->param('p3_Amt');
		#	交易币种,固定值"CNY".
		$data['p4_Cur']				= "CNY";
		#	商户接收支付成功数据的地址,支付成功后易宝支付会向该地址发送两次成功通知.
		$data['p8_Url']			  = '';	
		#签名串
		$hmac                         = HmacMd5(implode($data),$merchantKey);
		$this->assign('data',$data);
		$this->assign('reqURL_onLine',$reqURL_onLine);
		$this->assign('hmac',$hmac);
		$this->assign('p1_MerId',$p1_MerId);
		return $this->fetch();
	}
	public function orderInfo()
	{
		$orders = $this->order->all(['user_id'=>Session::get('user_id')]);
		$count = Db::table('lit_order')->where('user_id',Session::get('user_id'))->count();
		$info = [];
		foreach ($orders as $key => $value) {
			$arr = $value->toArray();
			$course_id = explode(',', $arr['order_course']);
			$arr2 = [];
			foreach ($course_id as $v) {
				$course = $this->course->get($v);
				$arr2[] = $course->toArray();
			}
			$arr['order_course'] = $arr2;
			$info[] = $arr;
		}
		$this->assign('count',$count);
		$this->assign('orders',$info);
		return $this->fetch();
	}
}
?>