<?php
namespace app\admin\controller\shop;

use think\Request;
use app\admin\controller\Base;
use app\admin\model\shop\ProType;

class Type extends Base
{
	private $lst_code = '2-2-0';
	private $add_code = '2-2-1';
	private $edit_code = '2-2-2';
	private $del_code = '2-2-3';

	public function lst()
	{
		$pro_id = input('pro_id', '');
		$this->assign('pro_id', $pro_id);
		$s_pro_id_eq = $pro_id == '' ? 'neq' : 'eq';
		$o_types = ProType::where('pro_id', $s_pro_id_eq, $pro_id)
			->where('status', '1')
			->order('add_time', 'desc')
			->paginate(10, false, [
                'query' => Request::instance()->param(),//不丢失已存在的url参数
            ]);
        $this->assign('types', $o_types);

		return $this->fetch('lst');
	}

	public function add()
	{
		$pro_id = input('pro_id');
		if (Request::instance()->isGet()) {
			$this->assign('pro_id', $pro_id);

			return $this->fetch('add');
		}
		if (Request::instance()->isAjax()) {
			$type = input('type', '');
			if (empty($type)) {
				return $this->no("请输入型号");
			}
			$weight = input('weight', '100');
			$o_type = new ProType;
			$o_type->type = $type;
			$o_type->weight = $weight;
			$o_type->pro_id = $pro_id;
			$o_type->uid = $this->admin_id;
			$time = time();
			$o_type->add_time = $this->time($time);
			$res = $o_type->save();
			if (!$res) {
				return $this->no("添加失败");
			}

			return $this->yes("添加成功");			
		}
	}

	public function edit()
	{
		$id = input('id');
		$o_type = ProType::where('id', $id)
			->where('status', '1')
			->find();
		if (Request::instance()->isGet()) {
			$this->assign('type', $o_type);
			$this->assign('id', $id);

			return $this->fetch('edit');
		}
		if (Request::instance()->isAjax()) {
			$type = input('type', '');
			if (empty($type)) {
				return $this->no("请输入型号");
			}
			$weight = input('weight', '100');
			$o_type->type = $type;
			$o_type->weight = $weight;
			$o_type->uid = $this->admin_id;
			$time = time();
			$o_type->add_time = $this->time($time);
			$res = $o_type->save();
			if (!$res) {
				return $this->no("修改失败");
			}

			return $this->yes("修改成功");			
		}
	}

	public function del()
	{
		$a_id = array();
		$a_id = input('id');
		preg_match_all('/\d+/', $a_id, $arr);
		foreach ($arr[0] as $vv) {
			$task = ProType::where('id', $vv)
				->where('status', '1')
				->find();
			$task->status = '0';
			$task->save();
		}

		return $this->yes("删除成功");
	}
}
