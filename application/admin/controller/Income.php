<?php
namespace app\admin\controller;

use think\Request;
use app\admin\controller\Base;
use app\admin\model\Income as appIncome;

class Income extends Base
{
	private $lst_code = '2-4-0';
	
	public function lst()
	{
		$lst_code = $this->lst_code;
		$lst_code_status = $this->checkCode($lst_code);
		if ($lst_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		$order_id = input('order_id', '-1');
		$s_order_id_eq = $order_id == '-1' ? 'neq' : 'eq';
		$o_incomes = appIncome::where('order_id', $s_order_id_eq, $order_id)
			->where('status', '1')
			->order('add_time', 'desc')
			->paginate(10, false, [
                'query' => Request::instance()->param(),//不丢失已存在的url参数
            ]);
        $this->assign('incomes', $o_incomes);
        $f_month_incomes = appIncome::where('status', '1')
			->whereTime("add_time", 'm')
			->sum('money');
		$this->assign('m_incomes', $f_month_incomes);
		$f_day_incomes = appIncome::where('status', '1')
			->whereTime("add_time", 'd')
			->sum('money');
		$this->assign('d_incomes', $f_day_incomes);
		$all_incomes = appIncome::where('status', '1')
			->sum('money');
		$this->assign('all_incomes', $all_incomes);

		return $this->fetch('lst');
	}
}
