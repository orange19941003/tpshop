<?php
namespace app\admin\controller;

use think\Request;
use think\Controller;
use app\admin\controller\Base;
use app\admin\model\Reward as adminReward;

class Reward extends Base
{
	public function lst() 
	{
		$o_rewards = adminReward::where('status', '1')
			->paginate(3);
		$this->assign('rewards', $o_rewards);

		return $this->fetch('lst');
	}

	public function edit()
	{
		if (Request::instance()->isAjax()) {
			$title = input('title', '');
			$s_reward = input('reward', '');
			$type = input('type', '');
			$id = input('id');
			if (empty($title)) {
				return $this->no("奖励标题不能为空");
			}
			if (empty($s_reward)) {
				return $this->no("奖励不能为空");
			}
			if ($type == '') {
				return $this->no("请选择奖励类型");
			}

			$reward = adminReward::where('id', $id)
				->where('status', '1')
				->find(); 
			$reward->title = $title;
			$reward->reward = $s_reward;
			$reward->type = $type;
			$reward->uid = $this->admin_id;
			$time = time();
			$reward->audit_time = $this->time($time);
			$res = $reward->save();
			if (!$res) {
				return $this->no('修改失败');
			}

			return $this->yes('修改成功');

		}
		if (Request::instance()->isGet()) {
			$id = input('id');
			$o_reward = adminReward::where('id', $id)
				->where('status', '1')
				->find();
			if (!$o_reward) {
				return $this->no("对象属性错误");
			}
			$this->assign('reward', $o_reward);

			return $this->fetch('edit');			
		}
	}

	public function add()
	{
		if (Request::instance()->isAjax()) {
			$title = input('title', '');
			$s_reward = input('reward', '');
			$type = input('type', '');
			if (empty($title)) {
				return $this->no("奖励标题不能为空");
			}
			if (empty($s_reward)) {
				return $this->no("奖励不能为空");
			}
			if ($type == '') {
				return $this->no("请选择奖励类型");
			}

			$reward = new adminReward; 
			$reward->title = $title;
			$reward->reward = $s_reward;
			$reward->type = $type;
			$reward->uid = $this->admin_id;
			$time = time();
			$reward->add_time = $this->time($time);
			$res = $reward->save();
			if (!$res) {
				return $this->no('新增失败');
			}

			return $this->yes('新增成功');

		}
		if (Request::instance()->isGet()) {

			return $this->fetch('add');			
		}
	}

	public function del()
	{
		$id = input('id');
		$o_reward = adminReward::where('id', $id)
			->where('status', '1')
			->find();
		if (!$o_reward) {
			return $this->no("对象属性错误");
		}
		$o_reward->status = 0;
		$res = $o_reward->save();
		if (!$res) {
			return $this->no("删除失败");
		}

		return $this->yes("删除成功");
	}
}
