<?php

namespace app\admin\controller\vip;

use think\Request;
use app\admin\controller\Base;
use app\admin\model\vip\VipCate;

class Cate extends Base
{
	public function lst()
	{
		$o_cates = VipCate::all();
		$this->assign('cates', $o_cates);

		return $this->fetch('lst');
	}

	public function add()
	{
		if (Request::instance()->isGet()) {

			return $this->fetch('add');
		}
		if (Request::instance()->isAjax()) {
			$title = input('title', '');
			$price = input('price', '');
			$addition = input('addition', '');
			$day = input('day', ''); 
			$img = input('img', '');
			if (empty($title)) {
				return $this->no("请填写标题");
			}
			if (empty($price)) {
				return $this->no("请填写价格");
			}
			if (empty($day)) {
				return $this->no("请填写天数");
			}
			if (empty($img)) {
				return $this->no("请上传图片");
			}
			$cate = new VipCate;
			$cate->title = $title;
			$cate->img = $img;
			$cate->price = $price;
			$cate->addition = $addition;
			$cate->day = $day;
			$cate->uid = $this->admin_id;
			$res = $cate->save();
			if (!$res) {
				return $this->no("新增失败");
			}

			return $this->yes('新增成功');
		}
	}

	public function edit()
	{
		if (Request::instance()->isGet()) {
			$id = input('id');
			$o_cate = VipCate::where('id', $id)
				->find();
			$this->assign('cate', $o_cate);

			return $this->fetch('edit');
		}
		if (Request::instance()->isAjax()) {
			$id = input('id');
			$title = input('title', '');
			$price = input('price', '');
			$addition = input('addition', '');
			$day = input('day', ''); 
			$img = input('img', '');
			if (empty($title)) {
				return $this->no("请填写标题");
			}
			if (empty($price)) {
				return $this->no("请填写价格");
			}
			if (empty($day)) {
				return $this->no("请填写天数");
			}
			$cate = VipCate::where('id', $id)
				->find();
			$cate->title = $title;
			if (!empty($img)) {
				$cate->img = $img;
			}
			$cate->price = $price;
			$cate->addition = $addition;
			$cate->day = $day;
			$cate->uid = $this->admin_id;
			$res = $cate->save();
			if (!$res) {
				return $this->no("修改失败");
			}

			return $this->yes('修改成功');
		}
	}
}
