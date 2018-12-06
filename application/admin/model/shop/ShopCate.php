<?php
namespace app\admin\model\shop;

use think\Model;

class ShopCate extends Model
{
	public function getTitle($id)
	{
		$title = ShopCate::where('id', $id)
			->where('status', '1')
			->value('title');
			
		return $title;
	}

	public function product()
	{
		return $this->hasMany('Product', 'cate_id')->field('id,status');
	}
}
