<?php

namespace app\admin\controller\task;

use think\Db;
use think\Request;
use app\admin\model\User;
use app\admin\controller\Base;
use app\admin\model\task\Task;
use app\admin\model\task\TaskOrder;

class order extends Base
{
	public function lst()
	{
		$user_name = input('user_name', '');
		$task_name = input('task_name', '');
		$s_a_date = input('a_date', '');
		$s_b_date = input('b_date', '');
		$s_date_eq = 'between';
		$a_time = array();
		if (empty($s_a_date) || empty($s_b_date)) {
			$s_date_eq = 'not between';
			$a_time[] = "0-0-0 00:00:00";
			$a_time[] = "0-0-0 00:00:00";
		} else {
			$a_time[] = "$s_a_date 00:00:00";
			$a_time[] = "$s_b_date 23:59:59";
		}
		$user_id = User::where('name', $user_name)
			->where('status', '1')
			->value('id');
		$task_id = Task::where('title', $task_name)
			->where('status', '1')
			->value('id');
		if ($user_id == null) {
			$user_id = '';
		}
		if ($task_id == null) {
			$task_id = '';
		}
		$s_user_id_eq = $user_id == '' ? 'neq' : 'eq';
		$s_task_id_eq = $task_id == '' ? 'neq' : 'eq';
		$o_orders = TaskOrder::where('status', '1')
			->where('user_id', $s_user_id_eq, $user_id)
			->where('task_id', $s_task_id_eq, $task_id)
			->whereTime('add_time', $s_date_eq, $a_time)
			->order('add_time', 'desc')
			->paginate(3, false, [
                'query' => Request::instance()->param(),//不丢失已存在的url参数
            ]);
        $a_arr = array();
        $a_arr['user_name'] = $user_name;
        $a_arr['task_name'] = $task_name;
        $a_arr['orders'] = $o_orders;
        $a_arr['a_date'] = $s_a_date;
        $a_arr['b_date'] = $s_b_date;

		return $this->fetch('lst', $a_arr);
	}

	public function del()
	{
		$a_id = array();
		$a_id = input('id');
		preg_match_all('/\d+/', $a_id, $arr);
		foreach ($arr[0] as $vv) {
			$order = TaskOrder::where('id', $vv)
				->where('status', '1')
				->find();
			$order->status = '0';
			$order->save();
		}

		return $this->yes('删除成功');	
	}

	public function pass()
	{
		$a_id = array();
		$a_id = input('id');
		preg_match_all('/\d+/', $a_id, $arr);
		// 启动事务
        Db::startTrans();
		foreach ($arr[0] as $vv) {
			$o_order = TaskOrder::where('id', $vv)
				->where('status', '1')
				->find();
			if ($o_order->type != 0) {
				return $this->no('订单已不是未通过状态');
			}try{ 
			
				$o_order->type = 1;
				$o_order->uid = $this->admin_id;
				$res = $o_order->save();
				if (!$res) {
					return $this->no('通过失败');
				} 
				$o_user = User::where('id', $o_order->user_id)
					->where('status', '1')
					->find();
				$o_task = Task::where('id', $o_order->task_id)
					->find();
				$o_user->integral += $o_task->integral;
				$o_task->pv += 1;
				$o_task->save(); 
				$o_user->save();
			    Db::commit();
	        } catch (\Exception $e) {
	             return $this->no($e->getMessage());
	            // 回滚事务
	            Db::rollback();
	        }
		}

		return $this->yes('通过成功');	    // 提交事务
	}

	public function nopass() {
		$id = input('id');
		$reson = input('reson');
		$o_order = TaskOrder::where('id', $id)
			->where('status', '1')
			->find();
		$o_order->reson = $reson;
		$o_order->type = 2;
		$o_order->audit_time = time();
		$o_order->uid = $this->admin_id;
		$res = $o_order->save();
		if (!$res) {
			return $this->no('打回失败');
		}

		return $this->yes('打回成功');
	}
}
