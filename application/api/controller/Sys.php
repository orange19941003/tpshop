<?php
/*
	定时任务生成佣金表，并给推荐人加上佣金
 */
namespace app\api\controller;

use think\Db;
use think\Request;
use app\admin\model\User;
use app\admin\model\Income;
use app\admin\model\Reward;
use app\admin\model\shop\Order;
use app\admin\model\vip\VipCate;

class Sys
{
	public function income()
	{
		// 启动事务
        Db::startTrans();
        try {
        	$o_orders = Order::where('type', '0')
				->where('is_del', '1')
				->select();
			$user = new User;
			foreach ($o_orders as $vv) {
				$user_id = $vv->user_id;
				$order_id = $vv->id;
				$money = $vv->money;
				$o_user = $user->where('id', $user_id)
					->where('status', '1')
					->find();
				$pid = $o_user->pid;
				if ($pid != 0) {
					$res = $this->add_addition($pid, 'addition_one', $order_id, $money);
				}
				$ppid = $o_user->ppid;
				if ($ppid != 0) {
					$res = $this->add_addition($ppid, 'addition_two', $order_id, $money);
				}
				$pppid = $o_user->pppid;
				if ($pppid != 0) {
					$res = $this->add_addition($pppid, 'addition_two', $order_id, $money);
				}
				$vv->type = 1;
				$vv->save();
			}

			return 0;
        } catch (\Exception $e) {

        	return $this->no($e->getMessage());
            // 回滚事务
            Db::rollback();
        }
	}

	private function add_addition($pid, $addition, $order_id, $money)
	{
		$addition = $this->addition($addition, $pid);
		$income = new Income;
		$income->user_id = $pid;
		$income->order_id = $order_id;
		$income->money = $addition*$money;
		$time = date("Y-m-d H:i:s");
		$income->add_time = $time;
		$income->save();
		$o_user = User::where('id', $pid)
			->where('status', '1')
			->find();
	 	$o_user->fmoney += $addition*$money;
	 	$o_user->save();

	 	return 0;
	}

	private function addition($addition, $id)
	{
		$o_user = User::where('id', $id)
			->where('status', '1')
			->find();
		$addition = Reward::where('status', '1')
					->value($addition);
		$addition = $addition*0.01;
		if ($o_user->vip != 0) {
			$vip_addition = VipCate::where('id', $o_user->vip)
				->where('status', '1')
				->value('addition');
			$addition = $addition+$vip_addition*0.01;
		}

		return $addition;	
	}
}
