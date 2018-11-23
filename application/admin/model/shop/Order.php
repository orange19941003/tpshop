<?php
namespace app\admin\model\shop;

use think\Model;
use app\admin\model\Admin;
use app\admin\model\shop\Product;

class Order extends Model
{
	public function admin($id)
	{
		$name = Admin::where('id', $id)
			->where('status', '1')
			->value('name');

		return $name;
	}

	public function product($id)
	{
		$title = Product::where('id', $id)
			//->where('status', '1')
			->value('title');

		return $title;
	}
}
