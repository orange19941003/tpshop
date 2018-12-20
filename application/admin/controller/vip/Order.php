<?php

namespace app\admin\controller\vip;

use think\Request;
use app\admin\model\User;
use app\admin\controller\Base;
use app\admin\model\vip\VipCate;
use app\admin\model\vip\VipOrder;

class Order extends Base
{
	private $lst_code = '1-4-0';
	private $del_code = '1-4-1';
	
	public function lst()
	{
		$lst_code = $this->lst_code;
		$lst_code_status = $this->checkCode($lst_code);
		if ($lst_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		$del_code = $this->del_code;
		$del_code_status = $this->checkCode($del_code);
		$data = [];
		$data['del_code_status'] = $del_code_status;
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
		$cate_id = input('cate_id', '-1');
		$s_cate_id_eq = $cate_id == '-1' ? 'neq' : 'eq';
		$name = input('name', '');
		$user_id = '-1';
		$s_user_id_eq = 'neq';
		if (!empty($name)) {
			$user_id = User::where('name', $name)
				->where('status', '1')
				->value('id');
			$s_user_id_eq = 'eq'; 
		}
		$o_orders = VipOrder::where('cate_id', $s_cate_id_eq, $cate_id)
			->where('user_id', $s_user_id_eq, $user_id)
			->whereTime('add_time', $s_date_eq, $a_time)
			->where('status', '1')
			->order('add_time', 'desc')
			->paginate(10, false, [
                'query' => Request::instance()->param(),//不丢失已存在的url参数
            ]);
        $money_sum = VipOrder::where('cate_id', $s_cate_id_eq, $cate_id)
			->where('user_id', $s_user_id_eq, $user_id)
			->whereTime('add_time', $s_date_eq, $a_time)
			->where('status', '1')
			->sum('money');
        $o_cates = VipCate::where('status', '1')
        	->select();
        $data['money_sum'] = $money_sum;
        $data['cate_id'] = $cate_id;
        $data['name'] = $name;
        $data['cates'] = $o_cates;
        $data['orders'] = $o_orders;
        $data['a_date'] = $s_a_date;
        $data['b_date'] = $s_b_date;

    	return $this->fetch('lst', $data);
	}

	public function del()
	{
		$del_code = $this->del_code;
		$del_code_status = $this->checkCode($del_code);
		if ($del_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		$id = input('id');
		$o_order = VipOrder::where('status', '1')
			->where('id', $id)
			->find();
		if (!$o_order) {
			return $this->no("对象属性错误");
		}
		$o_order->status = 0;
		$o_order->uid = $admin_id;
		$o_order->save();

		return $this->yes('删除成功');
	}
}
