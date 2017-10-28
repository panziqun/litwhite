<?php  
namespace app\index\controller;
use app\index\model\Plate;
use app\index\model\Course;
use app\index\model\CourseCount;

use app\index\model\Note;
use app\index\model\NoteUpvote;
use think\Db;
use app\index\model\UserCourse;
use app\index\model\Shopcar;

use think\Controller;
use think\Session;
class Course extends Controller{
	private static $noteStart;
	protected $plate;
	protected $course;
	protected $note;
	protected $noteUpvote;
	protected $usercourse;
	protected $shopcar;

	public function _initialize()
	{
		$this->plate  = new Plate();
		$this->course = new Course();

		$this->note = new Note();
		$this->noteUpvote = new NoteUpvote();
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
	public function getNoteInfo()
	{
		$course_id = $this->request->param('course_id');
		$limit = $this->request->param('limit');
		$noteInfo = $this->note
		//$noteInfo = Db::table('lit_note')
						->alias('n')
						->join('lit_user u','u.user_id = n.user_id')
						->join('lit_userinfo ui','ui.user_id = n.user_id')
						//->join('lit_note_upvote nu','n.note_id = nu.note_id')
						->field('n.note_content,n.note_id,n.create_time,n.upvote_count,u.user_id,u.user_name,ui.userinfo_headi')
						->where('n.course_id',$course_id)
						->order('n.note_id desc')
						->limit($limit,5)
						->select();
		// $noteUpvote = Db::table('lit_note')
		// 				->alias('n')
		// 				->join('lit_note_upvote nu','nu.note_id = n.note_id')
		// 				->where()
		// 				->select();		
		file_put_contents('sql.txt',$this->note->getLastSql(),FILE_APPEND);
		if ($noteInfo) {
			$this->assign([
				'noteInfo'=>$noteInfo ,
			]);
			return $this->fetch();
		} else {
			return $this->fetch('public/lastData');
		}
		
		//return json_encode($noteInfo);
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