<?php
namespace app\admin\controller;

use think\Request;
use think\Controller;
use app\admin\controller\Base;
use app\admin\model\Partner as adminPartner;

class Partner extends Base
{
	public function lst()
	{
		$o_partners = adminpartner::where('status', 1)
			->order('weight', 'desc')
			->select();
		$this->assign('partners', $o_partners);

		return $this->fetch('lst');
	}

	public function add()
	{
		if (Request::instance()->isGet()) {

			return $this->fetch('add');
		}
		if (Request::instance()->isAjax()) {
			$title = input('title', '');
			$weight = input('weight', '');
			$img = input('img', '');
			if (empty($title)) {
				return $this->no("请填写商家名称");
			}
			if (empty($img)) {
				return $this->no("请上传图片");
			}
			$partner = new adminPartner;
			$partner->title = $title;
			$partner->img = $img;
			$partner->weight = $weight;
			$partner->uid = $this->admin_id;
			$res = $partner->save();
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
			$o_partner = adminPartner::where('id', $id)
				->where('status', '1')
				->find();
			if (!$o_partner) {
				return $this->no("对象属性错误");
			}
			$this->assign('partner', $o_partner);

			return $this->fetch('edit');
		}
		if (Request::instance()->isAjax()) {
			$id = input('id');
			$title = input('title', '');
			$weight = input('weight', '');
			$img = input('img', '');
			$partner = adminpartner::where('id', $id)
				->where('status', '1')
				->find();
			$partner->title = $title;
			if (!empty($img)) {
				$partner->img = $img;
			}
			$partner->weight = $weight;
			$partner->uid = $this->admin_id;
			$res = $partner->save();
			if (!$res) {
				return $this->no("修改失败");
			}

			return $this->yes('修改成功');
		}
	}

	public function del()
	{
		$id = input('id');
		$o_partner = adminPartner::where('id', $id)
			->where('status', '1')
			->find();
		if (!$o_partner) {
			return $this->no("对象属性错误");
		}
		$o_partner->status = 0;
		$res = $o_partner->save();
		if (!$res) {
			return $this->no("删除失败");
		}

		return $this->yes("删除成功");
	}
}
