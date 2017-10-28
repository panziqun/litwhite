<?php  
namespace app\index\controller;
use app\index\model\Plate;
use app\index\model\Course;
use app\index\model\CourseCount;
use app\index\model\UserCourse;
use app\index\model\Shopcar;
use think\Controller;
use think\Session;
class Course extends Controller{
	protected $plate;
	protected $course;
	protected $usercourse;
	protected $shopcar;
	public function _initialize()
	{
		$this->plate  = new Plate();
		$this->course = new Course();
		$this->usercourse = new UserCourse();
		$this->shopcar = new Shopcar();
	}
	public function courseNote()
	{		
		$courseInfo = $this->getCourseDetail();
		$shopcar = $this->getCourseCart();
		$this->assign([
			'courseInfo'=>$courseInfo ,
			'shopcar'=>$shopcar ,
		]);
		return $this->fetch();
	}
	public function courseComment()
	{
		$courseInfo = $this->getCourseDetail();
		$shopcar = $this->getCourseCart();
		$this->assign([
			'courseInfo'=>$courseInfo ,
			'shopcar'=>$shopcar ,
		]);
		return $this->fetch();
	}
	protected function getCourseDetail()
	{

		$course_id = $this->request->param('course_id');
		$courseInfo = $this->course->get($course_id);
		$courseInfo->CourseCount;
		return $courseInfo;
	}
	//判断用户是否加入购物车
	public function getCourseCart()
	{
		$course_id = $this->request->param('course_id');
		$user_id = Session::get('user_id');
		$shopcar = $this->shopcar->get(['user_id'=>$user_id,'course_id'=>$course_id]);
		if ($shopcar) {
			return $shopcar;
		}else{
			return '';
		}
	}
	public function courseVideo()
	{
		return $this->fetch();
	}
}
?>