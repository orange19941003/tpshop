<?php
namespace app\admin\controller\shop;

use think\Request;
use app\admin\controller\Base;
use app\admin\model\shop\ShopCate as shopCate;

class Cate extends Base
{
	private $lst_code = '2-3-0';
	private $add_code = '2-3-1';
	private $edit_code = '2-3-2';
	private $del_code = '2-3-3';

	public function lst() 
	{
		$lst_code = $this->lst_code;
		$lst_code_status = $this->checkCode($lst_code);
		if ($lst_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		$edit_code = $this->edit_code;
		$edit_code_status = $this->checkCode($edit_code);
		$add_code = $this->add_code;
		$add_code_status = $this->checkCode($add_code);
		$del_code = $this->del_code;
		$del_code_status = $this->checkCode($del_code);
		$data = [];
		$data['edit_code_status'] = $edit_code_status;
		$data['add_code_status'] = $add_code_status;
		$data['del_code_status'] = $del_code_status;
		$o_cates = shopCate::where('status', '1')
			->where('status', '1')
			->paginate(10);
		$this->assign('cates', $o_cates);

		return $this->fetch('lst', $data);
	}

	public function add()
	{
		header("Content-type:text/html;charset=utf-8");
		$add_code = $this->add_code;
		$add_code_status = $this->checkCode($add_code);
		if ($add_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		$cate = new shopCate;
		if (Request::instance()->isGet()) {
			$titles = $cate->where('status', '1')
				->where('level', '1')
				->order('weight', 'desc')
				->select();
			$this->assign('titles', $titles);

			return $this->fetch('add');
		}
		if (Request::instance()->isAjax()) {
			$title = input('title', '');
			$weight = input('weight', '100');
			$pid = input('pid');
			if (empty($title)) {
				return $this->no("名称不能为空");
			}
			$cate->title = $title;
			$cate->weight = $weight;
			$cate->pid = $pid;
			if ($pid == '0') {
				$cate->level = 1;
			} else {
				$pcate_level = shopCate::where('id', $pid)
					->where('status', '1')
					->value('level');
				$cate->level = $pcate_level + 1;
			}
			$res = $cate->save();
			if (!$res) {
				return $this->no("增加失败");
			}

			return $this->yes("增加成功");
		}
	}

	public function edit()
	{
		$edit_code = $this->edit_code;
		$edit_code_status = $this->checkCode($edit_code);
		if ($edit_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		$cate = new shopCate;
		$id = input('id');
		if (Request::instance()->isGet()) {
			$titles = $cate->where('status', '1')
				->where('level', '1')
				->order('weight', 'desc')
				->select();
			$o_cate = $cate->where('id', $id)
				->where('status', '1')
				->find(); 
			$this->assign('cate', $o_cate);
			$this->assign('titles', $titles);

			return $this->fetch('edit');
		}
		if (Request::instance()->isAjax()) {
			$title = input('title', '');
			$weight = input('weight', '100');
			$pid = input('pid');
			if (empty($title)) {
				return $this->no("名称不能为空");
			}
			$o_cate = $cate->where('id', $id)
				->where('status', '1')
				->find();
			$o_cate->title = $title;
			$o_cate->weight = $weight;
			$o_cate->pid = $pid;
			if ($pid == '0') {
				$o_cate->level = 1;
			} else {
				$pcate_level = shopCate::where('id', $pid)
					->where('status', '1')
					->value('level');
				$o_cate->level = $pcate_level + 1;
			}
			$res = $o_cate->save();
			if (!$res) {
				return $this->no("修改失败");
			}

			return $this->yes("修改成功");
		}
	}

	public function del()
	{
		$del_code = $this->del_code;
		$del_code_status = $this->checkCode($del_code);
		if ($del_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		$id = input('id');
		$o_cate = shopCate::where('id', $id)
			->where('status', '1')
			->find();
		if (!$o_cate) {
			return $this->no("对象属性错误");
		}
		$o_cate->status = 0;
		$o_cate->save();
		$o_products = $o_cate->product()->where('status', '1')->select();
		foreach ($o_products as $vv) {
			$vv->status = 0;
			$vv->save();
		}

		return $this->yes("删除成功");

	}

}
