<?php  
namespace app\admin\model;

use think\Model;
class Adminlist extends Model{
	public function adminToRole()
	{
		return $this->belongsToMany('adminrole','adminlist_adminrole','adminrole_id','adminlist_id');
		// return $this->hasOne('adminlist_adminrole');
	}
}
?>