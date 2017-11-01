<?php  
namespace app\admin\controller;
use app\admin\model\Note;

class Note extends Auth{
	protected $note;
	protected $is_login = ['*'];

	public function _initialize()
	{
		parent::_initialize();
		$this->note  = new Note();
	}
	public function noteList()
	{
		$note = $this->note
			->withTrashed()
			->alias('n')
			->join('lit_course c','c.course_id = n.course_id')
			->join('lit_user u','u.user_id = n.user_id')
			->join('lit_userinfo ui','ui.user_id = n.user_id')
			->field('n.note_id,n.note_content,n.delete_time,n.create_time,n.upvote_count,u.user_name,ui.userinfo_headi,c.course_title')
			->order('n.note_id','desc')

			->paginate(10);
		$page = $note->render();
		$this->assign([
			'note'=>$note,
			'page'=>$page,
		]);
		return $this->fetch();
	}
	public function noteDetail()
	{	
		$note_id = $this->request->param('note_id');
		$note = $this->note
			->withTrashed()
			->alias('n')
			->join('lit_user u','u.user_id = n.user_id')
			->join('lit_userinfo ui','ui.user_id = n.user_id')
			->field('n.note_id,n.note_content,n.delete_time,n.create_time,n.upvote_count,u.user_name,ui.userinfo_headi')
			->where('note_id',$note_id)
			->find();
			$this->assign([
			'note'=>$note,
			]);
		return $this->fetch();
	}
	public function noteDec()
	{
		$note_id = $this->request->param('note_id');
		$delete_time = $this->request->param('delete_time');
		//dump($delete_time);
		if ( $delete_time ) {
			$note = $this->note->withTrashed()->where('note_id',$note_id)->find();
			$note->delete_time = NULL;
			$result = $note->save();
		} else {
			$result = $this->note::destroy($note_id);
		}
		$this->redirect('noteList');
		//dump($result);
	}
}
?>