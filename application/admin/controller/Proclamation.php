<?php

namespace app\admin\controller;

use think\Request;
use app\admin\controller\Base;
use app\admin\model\Proclamation as Procla;
use app\admin\model\ProclamationCate as Cate;

class Proclamation extends Base
{
	public function lst()
	{
		$title = input('title', '');
		$s_title_eq = $title == '' ? 'neq' : 'eq';
		$cate_id = input('cate_id', '-1');
		$s_cate_eq = $cate_id == '-1' ? 'neq' : 'eq';
		$o_proclas = Procla::where('title', $s_title_eq, $title)
			->where('cate_id', $s_cate_eq, $cate_id)
			->where('status', '1')
			->order('add_time', 'desc')
			->paginate(10, false, [
                'query' => Request::instance()->param(),//不丢失已存在的url参数
            ]);
        $o_cates = Cate::where('status', '1')
        	->select();
		$a_arr = array();
		$a_arr['title'] = $title;
		$a_arr['cate_id'] = $cate_id;
		$a_arr['proclas'] = $o_proclas;
		$a_arr['cates'] = $o_cates;

		return $this->fetch("lst", $a_arr);
	}

	public function add()
	{
		if (Request::instance()->isGet()) {
			$o_cates = Cate::where('status', '1')
				->select();
			$this->assign('cates', $o_cates);

			return $this->fetch('add');
		}
		if (Request::instance()->isAjax()) {
			$title = input('title', '');
			$content = input('content', '');
			$author = input('author', '');
			$cate_id = input('cate_id', '');
			if (empty($title)) {
				return $this->no("标题不能为空");
			}
			if (empty($content)) {
				return $this->no("内容不能为空");
			}
			if (empty($cate_id)) {
				return $this->no("请选择分类");
			}
			$procla = new Procla;
			$procla->title = $title;
			$procla->content = $content;
			$procla->author = $author;
			$procla->cate_id = $cate_id;
			$procla->uid = $this->admin_id;
			$time = time();
			$procla->add_time = $this->time($time);
			$res = $procla->save();
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
			$o_procla = Procla::where('id', $id)
				->where('status', '1')
				->find();
			$this->assign('procla', $o_procla);
			$o_cates = Cate::where('status', '1')
				->select();
			$this->assign('cates', $o_cates);

			return $this->fetch('edit');
		}
		if (Request::instance()->isAjax()) {
			$title = input('title', '');
			$content = input('content', '');
			$author = input('author', '');
			$cate_id = input('cate_id', '');
			if (empty($title)) {
				return $this->no("标题不能为空");
			}
			if (empty($content)) {
				return $this->no("内容不能为空");
			}
			if (empty($cate_id)) {
				return $this->no("请选择分类");
			}
			$procla = Procla::where('id', $id)
				->where('status', '1')
				->find();
			$procla->title = $title;
			$procla->content = $content;
			$procla->author = $author;
			$procla->cate_id = $cate_id;
			$procla->uid = $this->admin_id;
			$res = $procla->save();
			if (!$res) {
				return $this->no("修改失败");
			}

			return $this->yes("修改成功");
  		}
	}

	public function del()
	{
		$id = input('id');
		$o_procla = Procla::where('id', $id)
			->where('status', '1')
			->find();
		if (!$o_procla) {
			return $this->no("对象属性错误");
		}
		$o_procla->status = 0;
		$o_procla->uid = $this->admin_id;
		$res = $o_procla->save();
		if (!$res) {
			return $this->no("删除失败");
		}

		return $this->yes("删除成功");
	}
}
