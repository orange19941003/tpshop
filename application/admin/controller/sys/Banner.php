<?php

namespace app\admin\controller\sys;

use think\Request;
use app\admin\controller\Base;
use app\admin\model\sys\Banner as adminBanner;

class Banner extends Base
{
	private $lst_code = '3-1-0';
	private $add_code = '3-1-1';
	private $edit_code = '3-1-2';
	private $del_code = '3-1-3';

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
		$o_banners = adminBanner::where('status', 1)
			->order('weight', 'desc')
			->select();
		$this->assign('banners', $o_banners);

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
			$url = input('url', '');
			$weight = input('weight', '');
			$img = input('img', '');
			if (empty($img)) {
				return $this->no("请上传图片");
			}
			$banner = new adminBanner;
			$banner->url = $url;
			$banner->img = $img;
			$banner->weight = $weight;
			$banner->uid = $this->admin_id;
			$res = $banner->save();
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
			$o_banner = adminBanner::where('id', $id)
				->where('status', '1')
				->find();
			if (!$o_banner) {
				return $this->no("对象属性错误");
			}
			$this->assign('banner', $o_banner);

			return $this->fetch('edit');
		}
		if (Request::instance()->isAjax()) {
			$id = input('id');
			$url = input('url', '');
			$weight = input('weight', '');
			$img = input('img', '');
			$banner = adminBanner::where('id', $id)
				->where('status', '1')
				->find();
			$banner->url = $url;
			if (!empty($img)) {
				$banner->img = $img;
			}
			$banner->weight = $weight;
			$banner->uid = $this->admin_id;
			$res = $banner->save();
			if (!$res) {
				return $this->no("修改失败");
			}

			return $this->yes('修改成功');
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
		$o_banner = adminBanner::where('id', $id)
			->where('status', '1')
			->find();
		if (!$o_banner) {
			return $this->no("对象属性错误");
		}
		$o_banner->status = 0;
		$res = $o_banner->save();
		if (!$res) {
			return $this->no("删除失败");
		}

		return $this->yes("删除成功");
	}
}
