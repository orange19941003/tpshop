<?php
namespace app\admin\model\task;

use think\Model;
use app\admin\model\User;
use app\admin\model\Admin;

class TaskOrder extends Model
{
	public function task()
	{
		return $this->belongsTo('task','task_id');
	}

	public function user($id)
	{
		$name = User::where('id', $id)
			->where('status', '1')
			->value('name');

		return $name;
	}

	public function getAdmin($id)
	{
		$name = Admin::where('id', $id)
			->where('status', '1')
			->value('name');

		return $name;
	}
}
