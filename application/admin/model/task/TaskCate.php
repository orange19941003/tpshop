<?php
namespace app\admin\model\task;

use think\Model;

class TaskCate extends Model
{
	public function task()
	{
		return $this->hasMany('Task', 'cate_id')->field('id,status');
	}
}
