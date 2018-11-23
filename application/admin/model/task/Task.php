<?php
namespace app\admin\model\task;

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
		return $this->belongsTo('TaskCate','cate_id');
	}
}
