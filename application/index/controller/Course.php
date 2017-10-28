<?php  
namespace app\index\controller;
use app\index\model\Plate;
use app\index\model\Course;
use app\index\model\CourseCount;
use app\index\model\Note;
use app\index\model\NoteUpvote;
use think\Db;

use think\Controller;
class Course extends Controller{
	private static $noteStart;
	protected $plate;
	protected $course;
	protected $note;
	protected $noteUpvote;
	public function _initialize()
	{
		$this->plate  = new Plate();
		$this->course = new Course();
		$this->note = new Note();
		$this->noteUpvote = new NoteUpvote();
	}
	public function courseNote()
	{		
		$courseInfo = $this->getCourseDetail();
		$this->assign([
			'courseInfo'=>$courseInfo ,
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
		$this->assign([
			'courseInfo'=>$courseInfo ,
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
	public function courseVideo()
	{
		return $this->fetch();
	}
}
?>