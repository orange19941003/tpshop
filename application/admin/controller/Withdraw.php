<?php
namespace app\admin\controller;

use think\Request;
use app\admin\model\User;
use app\admin\controller\Base;
use app\admin\model\Withdraw as appWithdraw;

class Withdraw extends Base
{
	private $lst_code = '1-6-0';
	private $pass_code = '1-6-1';
	private $nopass_code = '1-6-2';
	private $del_code = '1-6-3';

	public function lst()
	{
		$lst_code = $this->lst_code;
		$lst_code_status = $this->checkCode($lst_code);
		if ($lst_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		$pass_code = $this->pass_code;
		$pass_code_status = $this->checkCode($pass_code);
		$nopass_code = $this->nopass_code;
		$nopass_code_status = $this->checkCode($nopass_code);
		$del_code = $this->del_code;
		$del_code_status = $this->checkCode($del_code);
		$data = [];
		$data['pass_code_status'] = $pass_code_status;
		$data['nopass_code_status'] = $nopass_code_status;
		$data['del_code_status'] = $del_code_status;
		$name = input('name', '');
		$status = input('status', '-1');
		$s_status_eq = $status == '-1' ? 'neq' : 'eq';
		if (empty($name)) {
			$user_id = '-1';
		    $s_user_id_eq = 'neq';
		} else {
			$user = User::where('name', $name)
				->where('status', '1')
				->find();
			if (!$user) {
				$user_id = '-1';
				$s_user_id_eq = 'eq';
			} else {
				$user_id = $user->id;
				$s_user_id_eq = 'eq';
			}
		}
		$o_withdraws = appWithdraw::where('user_id', $s_user_id_eq, $user_id)
			->where('status', $s_status_eq, $status)
			->where('status', 'neq', '3')
			->order('add_time', 'desc')
			->paginate(10, false, [
                'query' => Request::instance()->param(),//不丢失已存在的url参数
            ]);
        $this->assign('name', $name);
        $this->assign('status', $status);
        $this->assign('withdraws', $o_withdraws);

        return $this->fetch('lst', $data);
	}

	public function pass()
	{
		$pass_code = $this->pass_code;
		$pass_code_status = $this->checkCode($pass_code);
		if ($pass_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		$id = input('id');
		$o_withdraw = appWithdraw::where('id', $id)
			->find();
		if (!$o_withdraw) {
			return $this->no("对象属性错误");
		}
		$o_withdraw->audit_time = date("Y-m-d H:i:s");
		$o_withdraw->uid = $this->admin_id;
		$o_withdraw->status = 1;
		$o_withdraw->save();

		return $this->yes('通过成功');
	}

	public function nopass()
	{
		$nopass_code = $this->nopass_code;
		$nopass_code_status = $this->checkCode($nopass_code);
		if ($nopass_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		$id = input('id');
		$information = input('information', '');
		if (empty($information)) {
			return $this->no("请输入打回原因");
		}
		$o_withdraw = appWithdraw::where('id', $id)
			->find();
		if (!$o_withdraw) {
			return $this->no("对象属性错误");
		}
		$o_withdraw->audit_time = date("Y-m-d H:i:s");
		$o_withdraw->uid = $this->admin_id;
		$o_withdraw->status = 2;
		$o_withdraw->reason = $information;
		$o_withdraw->save();
		$user_id = $o_withdraw->user_id;
		$o_user = User::where('id', $user_id)
			->find();
		$o_user->money += $o_withdraw->money;
		$o_user->save();

		return $this->yes("打回成功"); 
	}

	public function del()
	{
		$del_code = $this->del_code;
		$del_code_status = $this->checkCode($del_code);
		if ($pass_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		$id = input('id');
		$o_withdraw = appWithdraw::where('id', $id)
			->find();
		if (!$o_withdraw) {
			return $this->no("对象属性错误");
		}
		$o_withdraw->status = 3;
		$o_withdraw->audit_time = date("Y-m-d H:i:s");
		$o_withdraw->uid = $this->admin_id;
		$o_withdraw->save();

		return $this->yes("删除成功");
	}
}
