<?php  
namespace app\index\controller;
use app\index\model\Plate;
use app\index\model\Course;
use app\index\model\CourseCount;
use app\index\model\Note;
use app\index\model\NoteUpvote;
use app\index\model\UserCourse;
use app\index\model\Shopcar;
use think\Controller;
use think\Session;
use think\Db;


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
	/*
	*note页面的头部 的课程信息
	*
	*/
	public function courseNote()
	{		
		$courseInfo = $this->getCourseDetail();
		$shopcar = $this->getCourseCart();
		if (session('?user_id')) {
			$sessionUid = 1;//session user_id 存在返回1
		} else{
			$sessionUid = 2;//session user_id 不存在返回2
		}
		
		$this->assign([
			'courseInfo'=>$courseInfo ,
			'sessionUid'=>$sessionUid,
			'shopcar'=>$shopcar ,
		]);
		return $this->fetch();
	}
	/*
	*note页面的头部 的笔记信息
	*
	*/
	public function getNoteInfo()
	{
		$course_id = $this->request->param('course_id');
		$limit = $this->request->param('limit');
		$noteInfo = $this->note
						->alias('n')
						->join('lit_user u','u.user_id = n.user_id')
						->join('lit_userinfo ui','ui.user_id = n.user_id')
						->field('n.note_content,n.note_id,n.create_time,n.upvote_count,u.user_id,u.user_name,ui.userinfo_headi')
						->where('n.course_id',$course_id)
						//->where('delete_time','=',null)
						->order('n.note_id desc')
						->limit($limit,5)
						->select();
		if ($noteInfo) {
			$this->assign([
				'noteInfo'=>$noteInfo ,
			]);
			return $this->fetch();
		} else {
			return $this->fetch('public/lastData');
		}
	}
	public function setUpvote()
	{
		
		$note_id = $this->request->param('note_id');
		$user_id = session('user_id');

		$noteUpvoteInfo  = $this->noteUpvote
				->withTrashed()
				->where('note_id',$note_id)
				->where('user_id',$user_id)
				->find();
		if ( $noteUpvoteInfo ) {

			if ( $noteUpvoteInfo->delete_time ) {
				$noteUpvoteInfo->delete_time = NULL;
				$result = $noteUpvoteInfo->save();
			} else {
				$result = $this->noteUpvote::destroy($noteUpvoteInfo->note_upvote_id);
				$note =  $this->note->get($note_id);
				$note->setDec('upvote_count',1);
				return json_encode(['code'=>500],JSON_UNESCAPED_UNICODE);
			}
		} else {
			$this->noteUpvote->note_id = $note_id;
			$this->noteUpvote->user_id = $user_id;
			$result = $this->noteUpvote->save();
		}
		$note =  $this->note->get($note_id);
		$note->setInc('upvote_count',1);
		return json_encode(['code'=>200,'info'=>$result],JSON_UNESCAPED_UNICODE);
		

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
	/*
	*获取课程信息详情
	*
	*/
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
	/*
	*video页面获取课程基本信息
	*
	*/
	public function courseVideo()
	{
		$course_id = $this->request->param('course_id');
		$courseInfo = $this->course->get($course_id);
		$this->assign([
			'courseInfo'=>$courseInfo,
		]);
		return $this->fetch();
	}
	public function addNote()
	{
		$noteInfo = $this->request->param();
		$this->note->data([
			'course_id'=>$noteInfo['course_id'],
			'note_content'=>$noteInfo['note_content'],
			'user_id'=>session('user_id'),
		]);
		$result = $this->note->save();
		if (empty($result)) {
			return ['code'=>500,'info'=>'笔记保存失败'];
		} else {
			$noteInfo = $this->note
						->alias('n')
						->join('lit_user u','u.user_id = n.user_id')
						->join('lit_userinfo ui','ui.user_id = n.user_id')
						->field('n.note_content,n.note_id,n.create_time,n.upvote_count,u.user_id,u.user_name,ui.userinfo_headi')
						->where('n.course_id',$noteInfo['course_id'])
						->order('n.note_id desc')
						->limit(1)
						->select();
			$this->assign([
				'noteInfo'=>$noteInfo ,
			]);
			return $this->fetch('getNoteInfo');
		}

		
	}
}
?>