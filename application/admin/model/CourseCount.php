<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use traits\model\SoftDelete;

class CourseCount extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	public function courseCountInsertData($courseId)
	{
		$this->data([
			'course_id'=>$courseId,
		]);
		$result = $this->save();
		if ($result) {
			return ['code'=>200,'info'=>'添加成功'];
		} else {
			return ['code'=>500,'info'=>'添加失败'];
		}
	}
}