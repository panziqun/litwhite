<?php  
namespace app\admin\controller;

use app\admin\model\Course;
use app\admin\model\Carousel;
class Index extends Auth{
	protected $is_login = ['*'];
	protected $course;
	protected $carousel;
	public function _initialize()
	{
		parent::_initialize();
		$this->course = new Course();
		$this->carousel = new Carousel();
	}
	public function index()
	{
		return $this->fetch();
	}
	public function bigData()
	{
		return $this->fetch();
	}
	//加载轮播页面
	public function homePage()
	{
		$carousel = $this->carousel->all();
		$arr = [];
		foreach ($carousel as $key => $value) {
			$arr[$key] = $value->toArray();
			$arr[$key]['carousel_image'] ='uploads/' . $arr[$key]['carousel_image'];
		}
		$this->assign('carousel', $arr);
		return $this->fetch();
	}
	//轮播上传
	public function upload()
	{
		if ($this->request->param('course_title') != '' && $this->request->param('carousel_order') != '' && $this->request->file('image') != null) {
			// 获取表单上传文件 例如上传了001.jpg
			$file = $this->request->file('image');
			// 移动到框架应用根目录/public/uploads/ 目录下
			if($file){
				$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
				if($info){
					// 成功上传后 获取上传信息
					// 输出 jpg
					// echo $info->getExtension();
					// 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
					$carousel_image = str_replace('\\', '/', $info->getSaveName());
					// 输出 42a79759f284b767dfcb2a0197904287.jpg
					// echo $info->getFilename();
					$this->carousel->save(['course_title'=>$this->request->param('course_title'),'carousel_image'=>$carousel_image,'carousel_order'=>$this->request->param('carousel_order')]);
					$this->success('新增成功', 'homePage');
				}else{
					// 上传失败获取错误信息
					echo $file->getError();
				}
			}
		}
		$this->success('新增失败', 'homePage');
	}
	//判断课程是否存在
	public function titleIf()
	{
		$course = $this->course->get(['course_title'=>$this->request->param('course_title')]);
		if ($course) {
			return json_encode(['info'=>'存在该课程'], JSON_UNESCAPED_UNICODE);
		}else{
			return json_encode(['info'=>'不存在该课程'], JSON_UNESCAPED_UNICODE);
		}
	}
	//删除轮播
	public function carouselDel()
	{
		dump($this->request->param());
		$this->carousel->destroy($this->request->param('carousel_id'));
	}
	public function webSet()
	{
		return $this->fetch();
	}
}
?>