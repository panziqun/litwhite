<?php  
namespace app\index\controller;

use think\Controller;
use app\index\model\Shopcar;
use app\index\model\Course;
use think\Session;
class Order extends Controller{
	protected $shopcar;
	protected $course;
	public function _initialize()
	{
		parent::_initialize();
		$this->shopcar = new Shopcar();
		$this->course = new Course();
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
	public function orderInfo()
	{
		return $this->fetch();
	}

}
?>