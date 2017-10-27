<?php  
namespace app\index\controller;
use app\index\model\Plate;
use app\index\model\Course;
use app\index\model\CourseCount;
use think\Controller;
class Course extends Controller{

	public function _initialize()
	{
		$this->plate  = new Plate();
		$this->course = new Course();
	}
	public function courseDetail()
	{
		$course_id = $this->request->param('course_id');
		$courseInfo = $this->course->get($course_id);
		$courseInfo->CourseCount;
		$this->assign([
			'courseInfo'=>$courseInfo ,
			''
		]);
		return $this->fetch();
	}
	public function courseVideo()
	{
		return $this->fetch();
	}
}
?>