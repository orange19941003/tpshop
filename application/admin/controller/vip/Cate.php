<?php

namespace app\admin\controller\vip;

use think\Request;
use app\admin\controller\Base;
use app\admin\model\vip\VipCate;

class Cate extends Base
{
	private $lst_code = '1-2-0';
	private $add_code = '1-2-1';
	private $edit_code = '1-2-2';
	
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
		$data = [];
		$data['edit_code_status'] = $edit_code_status;
		$data['add_code_status'] = $add_code_status;
		$o_cates = VipCate::all();
		$this->assign('cates', $o_cates);

		return $this->fetch('lst', $data);
	}

	public function add()
	{
		$add_code = $this->add_code;
		$add_code_status = $this->checkCode($add_code);
		if ($add_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
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
			$cate->add_time = date("Y-m-d H:i:s");
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
		$edit_code = $this->edit_code;
		$edit_code_status = $this->checkCode($edit_code);
		if ($edit_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
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
