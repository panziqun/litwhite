<?php
namespace app\index\model;
use think\Model;
use think\Db;
use traits\model\SoftDelete;

class Plate extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	
}