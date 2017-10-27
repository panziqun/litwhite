<?php  
namespace app\index\controller;
use think\Controller;
use app\index\model\Plate;
use app\index\model\Course;
class Index extends Controller{
	protected $plate;
	/*
	*初始化获取plate对象
	*
	*/
	public function _initialize()
	{
		$this->plate  = new Plate();
		$this->course = new Course();
	}
	public function index()
	{
		//获取首页大模块列表
		$bigPlateList = $this->plate->where('plate_fid',1)->select();
		//获取首页小模块列表
		$smallPlateList = $this->plate->where('plate_fid','>',1)->select();
		//获取首页实战推荐模块课程
		$courseFree	= $this->course
					->alias('c')
					->join('lit_plate p', 'c.plate_id = p.plate_id')
					->join('lit_course_count ct', 'c.course_id = ct.course_id')
					->where('c.course_order','=',3)
					->order('ct.course_sales','desc')
					->field('c.course_id,c.course_title,c.course_grade,ct.course_sales,c.course_start,c.course_rate,c.course_pic,p.plate_title')
					->limit(5)
					->select();
		//获取首页新上好课模块课程
		$courseNew	= $this->course
					->alias('c')
					->join('lit_plate p', 'c.plate_id = p.plate_id')
					->join('lit_course_count ct', 'c.course_id = ct.course_id')
					->where('c.course_order','=',2)
					->order('ct.course_sales','desc')
					->field('c.course_id,c.course_title,c.course_grade,ct.course_sales,c.course_start,c.course_rate,c.course_pic,p.plate_title')
					->limit(5)
					->select();
		$this->assign([
			'bigPlateList'  =>$bigPlateList,
			'smallPlateList'=>$smallPlateList,
			'courseFree'	=>$courseFree,
			'courseNew'		=>$courseNew,
		]);

		return $this->fetch();
	}
	public function indexList()
	{
		return $this->fetch();
	}
	public function indexSearch()
	{
		return $this->fetch();
	}
}
?>