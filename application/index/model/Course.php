<?php
namespace app\index\model;
use think\Model;
use think\Db;
use traits\model\SoftDelete;
use app\index\model\CourseCount;

class Course extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	public function courseCount()
	{
		return $this->hasOne('CourseCount','course_id');
	}
	public function getCourseGradeAttr($value)
	{
		$status = [0=>'初级',1=>'中级',2=>'高级'];
		return $status[$value];
	}
	
}