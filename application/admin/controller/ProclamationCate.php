<?php

namespace app\admin\controller;

use think\Request;
use app\admin\controller\Base;
use app\admin\model\ProclamationCate as Cate;

class ProclamationCate extends Base
{
	public function lst()
	{
		$o_cates = Cate::where('status', '1')
			->select();
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
			if (empty($title)) {
				return $this->no("请输入标题");
			}
			$cate = new Cate;
			$cate->title = $title;
			$cate->uid = $this->admin_id;
			$time = time();
			$cate->add_time = $this->time($time);
			$res = $cate->save();
			if (!$res) {
				return $this->no("新增失败");
			}

			return $this->yes("新增成功");
		}
	}

	public function edit()
	{
		$id = input('id');
		if (Request::instance()->isGet()) {
			$o_cate = Cate::where('id', $id)
				->where('status', '1')
				->find();
			$this->assign('cate', $o_cate);

			return $this->fetch('edit');
		}
		if (Request::instance()->isAjax()) {
			$title = input('title', '');
			if (empty($title)) {
				return $this->no("请输入标题");
			}
			$cate = Cate::where('id', $id)
				->where('status', '1')
				->find();
			$cate->title = $title;
			$cate->uid = $this->admin_id;
			$res = $cate->save();
			if (!$res) {
				return $this->no("修改失败");
			}

			return $this->yes("修改成功");
		}
	}

	public function del()
	{
		$id = input('id');
		$o_cate = Cate::where('id', $id)
			->where('status', '1')
			->find();
		if (!$o_cate) {
			return $this->no("对象属性错误");
		}
		$o_cate->status = 0;
		$res = $o_cate->save();
		if (!$res) {
			return $this->no("删除失败");
		}
		$o_proles = $o_cate->procla()->where('status', '1')->select();
		foreach ($o_proles as $vv) {
			$vv->status = 0;
			$vv->save();
		}

		return $this->yes('删除成功');
	}
}
