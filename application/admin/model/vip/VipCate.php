<?php
namespace app\admin\model\vip;

use think\Model;

class VipCate extends Model
{
	public function admin()
	{
		return $this->belongsTo('app\admin\model\Admin', 'uid');
	}
}
