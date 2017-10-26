<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use traits\model\SoftDelete;

class Note extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	public function getWeekNote($course_id)
	{
		$beginWeek = mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y'));
		$endWeek = mktime(23,59,59,date('m'),date('d')-date('w')+7,date('Y'));
		return $this->getNoteCount($course_id, $beginWeek, $endWeek);
	}
	public function getMonthNote($course_id)
	{
		$beginMonth = mktime(0,0,0,date('m'),1,date('Y'));
		$endMonth = mktime(23,59,59,date('m'),date('t'),date('Y'));
		return $this->getNoteCount($course_id, $beginMonth, $endMonth);
	}
	public function getNoteCount($course_id, $beginTime, $endTime)
	{
		$noteCount = Db::table('lit_course')
					->alias('course')
					->join('lit_note note', 'course.course_id = note.course_id')
					->field('course.course_id')
					->where('note.create_time', 'between', [$beginTime,$endTime])
					->where('course.course_id',$course_id)
					->count();
		return $noteCount;
	}
}