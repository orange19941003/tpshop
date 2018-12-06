<?php
namespace app\admin\model\sys;

use think\Model;

class Banner extends Model
{
	public function admin()
	{
		return $this->belongsTo('app\admin\model\Admin', 'uid');
	}
}
