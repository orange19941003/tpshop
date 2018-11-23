<?php

namespace app\admin\controller\task;

use think\Request;
use app\admin\controller\Base;
use app\admin\model\task\TaskCate;

class Cate extends Base
{
	public function lst()
	{
		$o_cates = TaskCate::where('status', '1')
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
				return $this->no('请输入标题');
			}
			$cate = new TaskCate;
			$cate->title = $title;
			$res = $cate->save();
			if (!$res) {
				return $this->no('新增失败');
			}

			return $this->yes('新增成功');
		}
	}

	public function edit()
	{
		if (Request::instance()->isGet()) {
			$id = input('id');
			$o_cate = TaskCate::where('id', $id)
				->where('status', '1')
				->find();
			$this->assign('cate', $o_cate);

			return $this->fetch('edit');
		}
		if (Request::instance()->isAjax()) {
			$id = input('id');
			$title = input('title', '');
			if (empty($title)) {
				return $this->no('请输入标题');
			}
			$o_cate = TaskCate::where('id', $id)
				->where('status', '1')
				->find();
			$o_cate->title = $title;
			$res = $o_cate->save();
			if (!$res) {
				return $this->no('修改失败');
			}

			return $this->yes('修改成功');
		}
	}

	public function del()
	{
		$id = input('id');
		$o_cate = TaskCate::where('id', $id)
			->where('status', '1')
			->find();
		if (!$o_cate) {
			return $this->no("对象属性错误");
		}
		$o_cate->status = 0;
		$res = $o_cate->save();
		if (!$res) {
			return $this->no('删除失败');
		}
		$o_tasks = $o_cate->task()->where('status', '1')->select();
		foreach ($o_tasks as $vv) {
			$vv->status = 0;
			$vv->save();
		}
		
		return $this->yes('删除成功');
	}
}
