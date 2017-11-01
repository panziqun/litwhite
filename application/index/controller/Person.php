<?php  
namespace app\index\controller;

use think\Controller;
use app\index\model\Userinfo;
use app\index\model\UserCourse;
use app\index\model\Course;
use app\index\model\Note;
use think\Session;
use think\Db;

class Person extends Controller{
	protected $userinfo;
	protected $usercourse;
	protected $course;
	protected $note;
	public function _initialize()
	{
		$this->userinfo = new Userinfo();
		$this->usercourse = new UserCourse();
		$this->course = new Course();
		$this->note = new Note();
	}
	public function personNote()
	{
		//头部基本信息
		$learnCount = Db::table("lit_user_course")->where('user_id',Session::get('user_id'))->count('user_course_id');
		$userinfo = $this->userInfo();
		$this->assign(['userinfo'=>$userinfo,'learnCount'=>$learnCount]);
		//笔记信息
		$note = $this->note->order('create_time','desc')->where(['user_id'=>Session::get('user_id')])->select();
		$array = [];
		foreach ($note as $key => $value) {
			$arr = $value->toArray();
			$course_id = $arr['course_id'];
			$course = $this->course->get($course_id);
			$arr['course_title'] = $course->course_title;
			$array[$key] = $arr;
		}
		$this->assign('notes', $array);
		return $this->fetch();
	}
	public function personCourse()
	{
		//头部基本信息
		$learnCount = Db::table("lit_user_course")->where('user_id',Session::get('user_id'))->count('user_course_id');
		$userinfo = $this->userInfo();
		$this->assign(['userinfo'=>$userinfo,'learnCount'=>$learnCount]);
		//已学课程
		$usercourse = $this->usercourse->order('update_time','desc')->where(['user_id'=>Session::get('user_id')])->paginate(10);
		if ($usercourse) {
			$array = [];
			foreach ($usercourse as $key => $value) {
				$arr = $value->toArray();
				$array[$key] = $arr;
				$course = $this->course->get($arr['course_id']);
				$array[$key]['course_title'] = $course['course_title'];
				$array[$key]['course_pic'] = $course['course_pic'];
			}
		}
		
		$this->assign('userCourse',$array);
		return $this->fetch();
	}
	public function userInfo()
	{
		$user_id = Session::get('user_id');
		$userinfo = $this->userinfo->get(['user_id'=>$user_id]);
		return $userinfo;
	}
	public  function noteDel()
	{
		$note_id = $this->request->param('note_id');
		$this->note->destroy($note_id);
	}
}
?>