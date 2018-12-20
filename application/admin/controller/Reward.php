<?php
namespace app\admin\controller;

use think\Request;
use app\admin\controller\Base;
use app\admin\model\Reward as shopReward;

class Reward extends Base
{
	private $lst_code = '1-5-0';
	private $edit_code = '1-5-2';
	private $add_code = '1-5-1';
	
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
		$o_rewards = shopReward::where('status', '1')
			->select();
		$this->assign('rewards', $o_rewards);

		return $this->fetch('lst', $data);
	}

	public function edit()
	{
		$edit_code = $this->edit_code;
		$edit_code_status = $this->checkCode($edit_code);
		if ($edit_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		$id = input('id', '');
		if (empty($id)) {
				return $this->no("对象属性错误");
			}
	    $reward = shopReward::where('id', $id)
			->where('status', '1')
			->find();

		if (Request::instance()->isGet()) {
			$this->assign('reward', $reward);

			return $this->fetch('edit');
		}
		if (Request::instance()->isAjax()) {
			$addition_one = input('addition_one', 0);
			$addition_two = input('addition_two', 0);
			$addition_three = input('addition_three', 0);
			$reward->addition_one = $addition_one;
			$reward->addition_two = $addition_two;
			$reward->addition_three = $addition_three;
			$reward->uid = $this->admin_id;
			$time = time();
			$reward->add_time = $this->time($time);
			$res = $reward->save();
			if (!$res) {
				return $this->no("修改失败");
			}

			return $this->yes("修改成功");
		}
	}

	public function add()
	{
		$add_code = $this->add_code;
		$add_code_status = $this->checkCode($add_code);
		if ($add_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		if (Request::instance()->isGet()) {
			$o_rewards = shopReward::where('status', '1')
				->select();
			if (count($o_rewards) != '0') {
				exception('异常操作', 100006);
			} 
			return $this->fetch('add');
		}
		if (Request::instance()->isAjax()) {
			$addition_one = input('addition_one', 0);
			$addition_two = input('addition_two', 0);
			$addition_three = input('addition_three', 0);
			$reward = new shopReward;
			$reward->addition_one = $addition_one;
			$reward->addition_two = $addition_two;
			$reward->addition_three = $addition_three;
			$reward->uid = $this->admin_id;
			$time = time();
			$reward->add_time = $this->time($time);
			$res = $reward->save();
			if (!$res) {
				return $this->no("新增失败");
			}

			return $this->yes("新增成功");
		}
	}
}
