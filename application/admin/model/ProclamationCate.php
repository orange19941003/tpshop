<?php
namespace app\admin\model;

use think\Model;

class ProclamationCate extends Model
{
	public function admin()
	{
		return $this->belongsTo('Admin','uid');
	}

	public function procla()
	{
		return $this->hasMany('Proclamation', 'cate_id')->field('id,status');
	}
}
