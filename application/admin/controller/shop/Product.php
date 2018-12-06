<?php
namespace app\admin\controller\shop;

use think\Request;
use app\admin\controller\Base;
use app\admin\model\shop\ShopCate as Cate;
use app\admin\model\shop\Product as shopProduct;

class Product extends Base
{
	public function lst()
	{
		$cate_id = input('cate_id', '');
		$s_id_eq = $cate_id == '' ? 'neq' : 'eq';
		$title = input('title', '');
		$s_title_eq = $title == '' ? 'neq' : 'eq';
		$pro_no = input('pro_no', '');
		$s_pro_no_eq = $pro_no == '' ? 'neq' : 'eq';
		$o_products = shopProduct::where('status', '1')
			->where('cate_id', $s_id_eq, $cate_id)
			->where('title', $s_title_eq, $title)
			->where('pro_no', $s_pro_no_eq, $pro_no)
			->paginate(10, false, [
                'query' => Request::instance()->param(),//不丢失已存在的url参数
            ]);
		$o_cates = Cate::where('status', '1')
			->where('level', '2')
			->order('weight', 'desc')
			->select();
		$a_arr = array();
		$a_arr['products'] = $o_products;
		$a_arr['cates'] = $o_cates;
		$a_arr['cate_id'] = $cate_id;
		$a_arr['title'] = $title;
		$a_arr['pro_no'] = $pro_no;

		return $this->fetch('lst', $a_arr);
	}

	public function edit()
	{
		$id = input('id');
		$product = new shopProduct;
		if (Request::instance()->isGet()) {
			$o_cates = Cate::where('status', '1')
				->where('level', '2')
				->select();
			$o_cates = $this->sort($o_cates);
			$this->assign('cates', $o_cates);
			$o_product = $product->where('id', $id)
				->where('status', '1')
				->find();
			$this->assign('product', $o_product);

			return $this->fetch('edit');
		}
		if (Request::instance()->isAjax()) {
			$type = input('type');
			$title = input('title', '');
			$img = input('img', '');
			$pro_no = input('pro_no', '');
			$cate_id = input('cate_id', '0');
			$keywords = input('keywords', '');
			$price = input('price', '');
			$cost = input('cost', '');
			$desc = input('desc', '');
			if (empty($title)) {
				return $this->no("请填写商品名称");
			}
			if (empty($pro_no)) {
				return $this->no("请填写商品编号");
			}
			if (empty($price)) {
				return $this->no("请填写商品价格");
			}
			$o_product = $product->where('id', $id)
				->where('status', '1')
				->find(); 
			$o_product->title = $title;
			if (!empty($img)) {
				$o_product->img = $img;
			}
			$o_product->pro_no = $pro_no;
			$o_product->cate_id = $cate_id;
			$o_product->keywords = $keywords;
			$o_product->price = $price;
			$o_product->cost = $cost;
			$o_product->desc = $desc;
			$o_product->type = $type;
			$res = $o_product->save();
			if (!$res) {
				return $this->no('修改失败');
			}

			return $this->yes('修改成功');
		}
	}

	public function add()
	{
		if (Request::instance()->isGet()) {
			$o_cates = Cate::where('status', '1')
				->where('level', '2')
				->select();
			$this->assign('cates', $o_cates);

			return $this->fetch('add');
		}
		if (Request::instance()->isAjax()) {
			$type = input('type');
			$title = input('title', '');
			$img = input('img', '');
			$pro_no = input('pro_no', '');
			$cate_id = input('cate_id', '0');
			$keywords = input('keywords', '');
			$price = input('price', '');
			$cost = input('cost', '');
			$desc = input('desc', '');
			if (empty($title)) {
				return $this->no("请填写商品名称");
			}
			if (empty($img)) {
				return $this->no("请上传商品图片");
			}
			if (empty($pro_no)) {
				return $this->no("请填写商品编号");
			}
			if (empty($price)) {
				return $this->no("请填写商品价格");
			}
			$product = new shopProduct;
			$product->title = $title;
			$product->img = $img;
			$product->pro_no = $pro_no;
			$product->cate_id = $cate_id;
			$product->keywords = $keywords;
			$product->price = $price;
			$product->cost = $cost;
			$product->desc = $desc;
			$product->type = $type;
			$res = $product->save();
			if (!$res) {
				return $this->no('添加失败');
			}

			return $this->yes('添加成功');
		}
	}

	public function del()
	{
		$id = input('id');
		$o_product = shopProduct::where('id', $id)
			->where('status', '1')
			->find();
		if (!$o_product) {
			return $this->no("对象属性错误");
		}
		$o_product->status = 0;
		$o_product->save();

		return $this->yes("删除成功");
	}
}
