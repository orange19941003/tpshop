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

		return $this->fetch('lst', $data);
	}

	public function add()
	{
		$add_code = $this->add_code;
		$add_code_status = $this->checkCode($add_code);
		if ($add_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
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
		$edit_code = $this->edit_code;
		$edit_code_status = $this->checkCode($edit_code);
		if ($edit_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
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
		$del_code = $this->del_code;
		$del_code_status = $this->checkCode($del_code);
		if ($del_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
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
