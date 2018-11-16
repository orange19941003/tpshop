<?php
namespace app\admin\model\Task;

use think\Model;
use app\admin\model\Admin;

class Task extends Model
{
	public function getAdmin($id)
	{
		$name = Admin::where('id', $id)
			->where('status', '1')
			->value('name');
		
		return $name;
	}

	public function cate()
	{
		return $this->belongsTo('taskCate','cate_id');
	}
}
