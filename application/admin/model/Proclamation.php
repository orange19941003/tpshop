<?php
namespace app\admin\model;

use think\Model;

class Proclamation extends Model
{
	public function admin()
	{
		return $this->belongsTo('Admin', 'uid');
	}

	public function cate()
	{
		return $this->belongsTo('ProclamationCate', 'cate_id');
	}
}
