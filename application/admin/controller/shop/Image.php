<?php
namespace app\admin\controller\shop;

use think\Request;
use app\admin\controller\Base;
use app\admin\model\shop\Product;
use app\admin\model\shop\Image as Img;

class Image extends Base
{
	private $lst_code = '2-6-0';
	private $add_code = '2-6-1';
	private $edit_code = '2-6-2';
	private $del_code = '2-6-3';

	public function lst()
	{
		$lst_code = $this->lst_code;
		$lst_code_status = $this->checkCode($lst_code);
		if ($lst_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		$edit_code = $this->edit_code;
		$edit_code_status = $this->checkCode($edit_code);
		$del_code = $this->del_code;
		$del_code_status = $this->checkCode($del_code);
		$data = [];
		$data['edit_code_status'] = $edit_code_status;
		$data['del_code_status'] = $del_code_status;
		$pro_id = input('pro_id', '');
		$s_pro_id_eq = $pro_id == '' ? 'neq' : 'eq';
		$o_images = Img::where('pro_id', $s_pro_id_eq, $pro_id)
			->where('status', '1')
			->order('add_time', 'desc')
			->paginate(10, false, [
                'query' => Request::instance()->param(),//不丢失已存在的url参数
            ]);
        $this->assign('images', $o_images);
        $this->assign('pro_id', $pro_id);

		return $this->fetch('lst', $data);
	}

	public function add()
	{
		if (Request::instance()->isGet()) {
			$id = input('id');
			$this->assign('id', $id);

			return $this->fetch('add');
		}
		if (Request::instance()->isAjax()) {
			$data = input();
			$img = [];
			$img = $data['img'];

			$id = $data['id'];
			foreach ($img as $vv) {
				$image = new Img;
				$image->img = $vv;
				$image->pro_id = $id;
				$image->admin_id = $this->admin_id;
				$time = time();
				$image->add_time = $this->time($time);
				$res = $image->save();
				if (!$res) {
					return $this->no("操作失败");
				} 
			}

			return $this->yes("新增成功");
		}

	}

	public function edit()
	{
		$id = input('id');
			$o_image = Img::where('id', $id)
				->where('status', '1')
				->find();
		if (Request::instance()->isGet()) {
			$this->assign('image', $o_image);

			return $this->fetch('edit');
		}
		if (Request::instance()->isAjax()) {
			$img = input('img', '');
			if (empty($img)) {
				return $this->no("请上传图片");
			}
			$o_image->img = $img;
			$o_image->admin_id = $this->admin_id;
			$res = $o_image->save();
			if (!$res) {
				return $this->no("修改失败");
			}

			return $this->yes("修改成功");
		}
	}

	public function del()
	{
		$id = input('id');
		$o_image = Img::where('id', $id)
			->where('status', '1')
			->find();
		if (!$o_image) {
			return $this->no("对象属性错误");
		}
		$o_image->status = 0;
		$o_image->admin_id = $this->admin_id;
		$res = $o_image->save();
		if (!$res) {
			return $this->no("删除失败");
		}

		return $this->yes("删除成功");
	}
}
