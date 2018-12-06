<?php
namespace app\admin\controller;

use think\Request;
use app\admin\controller\Base;
use app\admin\model\Reward as shopReward;

class Reward extends Base
{
	public function lst()
	{
		$o_rewards = shopReward::where('status', '1')
			->select();
		$this->assign('rewards', $o_rewards);

		return $this->fetch('lst');
	}

	public function edit()
	{
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
