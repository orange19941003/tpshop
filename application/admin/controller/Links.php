<?php
namespace app\admin\controller;

use think\Request;
use think\Controller;
use app\admin\controller\Base;
use app\admin\model\Links as Link;

class Links extends Base
{
	public function lst()
	{
		$o_links = Link::where('status', '1')
			->order('weight', 'desc')
			->paginate(10);
		$this->assign('links', $o_links);

		return $this->fetch('lst');
	}

	public function add()
	{
		if (Request::instance()->isAjax()) {
			$title = input('title', '');
			$url = input('url', '');
			$weight = input('weight', 100);
			if (empty($title)) {
				return $this->no('标题不能为空');
			}
			if (empty($url)) {
				return $this->no('链接地址不能为空');
			}
			$links = new Link;
			$links->title = $title;
			$links->url = $url;
			$links->weight = $weight;
			$links->uid = $this->admin_id;
			$res = $links->save();
			if (!$res) {
				return $this->no('新增失败');
			}

			return $this->yes('新增成功');
		}
		if (Request::instance()->isGet()) {

			return $this->fetch('add');
		}
	}

	public function edit()
	{
		if (Request::instance()->isAjax()) {
			$id = input('id');
			$title = input('title', '');
			$url = input('url', '');
			$weight = input('weight', 100);
			if (empty($title)) {
				return $this->no('标题不能为空');
			}
			if (empty($url)) {
				return $this->no('链接地址不能为空');
			}
			$links = Link::where('status', '1')
				->where('status', '1')
				->find();
			$links->title = $title;
			$links->url = $url;
			$links->weight = $weight;
			$links->uid = $this->admin_id;
			$res = $links->save();
			if (!$res) {
				return $this->no('修改失败');
			}

			return $this->yes('修改成功');
		}
		if (Request::instance()->isGet()) {
			$id = input('id');
			$o_link = Link::where('id', $id)
				->where('status', '1')
				->find();
			if (!$o_link) {
				return $this->no('对象属性错误');
			}
			$this->assign('link', $o_link); 

			return $this->fetch('edit');
		}
	}

	public function del()
	{
		$id = input('id');
		$o_link = Link::where('id', $id)
			->where('status', '1')
			->find();
		if (!$o_link) {
			return $this->no("对象属性错误");
		}
		$o_link->status = 0;
		$res = $o_link->save();
		if (!$res) {
			return $this->no('删除失败');
		}

		return $this->yes('删除成功');
	}
}
