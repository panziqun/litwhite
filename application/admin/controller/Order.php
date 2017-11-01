<?php  
namespace app\admin\controller;

use app\admin\model\Order;
use app\admin\model\User;
use app\admin\model\Course;
use app\admin\model\Addr;
class Order extends Auth{
	protected $is_login = ['*'];
	protected $order;
	protected $user;
	protected $course;
	protected $addr;
	public function _initialize()
	{
		parent::_initialize();
		$this->order = new Order();
		$this->user = new User();
		$this->course = new Course();
		$this->addr = new Addr();
	}
	public function orderPaid()
	{
		$orders = $this->order->all();
		$array = [];
		foreach ($orders as $key => $value) {
			$order = $value->toArray();
			$user = $this->user->get($order['user_id']);
			$order['user_name'] = $user->user_name;
			$courses = $order['order_course'];
			$courses = explode(',', $courses);
			$str1 = '';
			foreach ($courses as $k => $v) {
				$course = $this->course->get($v);
				$str1 .= $course->course_title . ',';
			}
			$order['order_course'] = rtrim($str1, ',');
			$array[] = $order;
		}
		$this->assign('order',$array);
		return $this->fetch();
	}
	public function updateAddr()
	{
		$order_id = $this->request->param('order_id');
		$order_name = $this->request->param('order_name');
		$order_phone = $this->request->param('order_phone');
		$order_address = $this->request->param('order_address');
		$order = $this->order->get($order_id);
		$order->save(['order_name'=>$order_name,'order_phone'=>$order_phone,'order_address'=>$order_address]);
	}
	public function orderFinish()
	{
		return $this->fetch();
	}
}
?>