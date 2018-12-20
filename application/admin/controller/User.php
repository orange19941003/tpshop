<?php
namespace app\admin\controller;

use think\Db;
use think\Request;
use app\admin\model\vip\Vip;
use app\admin\controller\Base;
use app\admin\model\vip\VipCate;
use app\admin\model\User as appUser;

class User extends Base
{
	private $lst_code = "1-1-0";
	private $add_code = "1-1-1";
	private $edit_code = "1-1-2";
	private $del_code = "1-1-3";
	private $vip_code = "1-1-4";
	private $chongzhi_code = "1-1-5";

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
		$vip_code = $this->vip_code;
		$vip_code_status = $this->checkCode($vip_code);
		$chongzhi_code = $this->del_code;
		$chongzhi_code_status = $this->checkCode($chongzhi_code);
		$data = [];
		$data['edit_code_status'] = $edit_code_status;
		$data['add_code_status'] = $add_code_status;
		$data['del_code_status'] = $del_code_status;
		$data['vip_code_status'] = $vip_code_status;
		$data['chongzhi_code_status'] = $chongzhi_code_status;
		$name = input('name', '');
		$s_name_eq = $name == '' ? 'neq' : 'eq';
		$vip = input('vip', '-1');
		$s_vip_eq = $vip == '-1' ? 'neq' : 'eq';
		$user = new appUser;
		$o_users = $user->where('name', $s_name_eq, $name)
			->where('vip', $s_vip_eq, $vip)
			->where('status', '1')
			->order('add_time', 'desc')
			->paginate(10, false, [
                'query' => Request::instance()->param(),//不丢失已存在的url参数
            ]);
        $o_cates = VipCate::all();
		$data['name'] = $name;
		$data['vip'] = $vip;
		$data['users'] = $o_users;
		$data['cates'] = $o_cates;

		return $this->fetch("lst", $data);
	}

	public function add()
	{
		$add_code = $this->add_code;
		$add_code_status = $this->checkCode($add_code);
		if ($add_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		if (Request::instance()->isAjax()) {
			$name = input('name', '');
			$tel = input('tel', '');
			$password = input('password', '');
			$money = input('money', '0');
			if (empty($name)) {
				return $this->no("用户名不能为空");
			}
			if (empty($tel)) {
				return $this->no("请填写电话号码");
			}
			if (empty($password)) {
				return $this->no("密码不能为空");
			}
			$res = preg_match("/^1[34578]\d{9}$/", $tel);
			if (!$res) {
				return $this->no("请填写正确的电话号码");
			}
			$password = md5($password);
			$user = new appUser;
			$is_exit_name = $user->where('name', $name)
				->where('status', '1')
				->find();
			if ($is_exit_name) {
				return $this->no("用户名已存在");
			}
			$is_exit_tel = $user->where('tel', $tel)
				->where('status', '1')
				->find();
			if ($is_exit_tel) {
				return $this->no("手机号已被注册");
			}
			$user->name = $name;
			$user->password = $password;
			$user->tel = $tel;
			$user->uid = $this->admin_id;
			$user->money = $money;
			$time = time();
			$user->add_time = $this->time($time);
			$res = $user->save();
			if (!$res) {
				return $this->no("新增失败");
			}

			return $this->yes("用户增加成功");
		}
		if (Request::instance()->isGet()) {
			return $this->fetch('add');
		}
	}

	public function del()
	{
		$del_code = $this->del_code;
		$del_code_status = $this->checkCode($del_code);
		if ($del_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		$id = input('id');
		$user = new appUser;
		$o_user = $user->where('id', $id)
			->where('status', '1')
			->find();
		if (!$o_user) {
			return $this->no("对象属性错误");
		}
		$o_user->status = 0;
		$res = $o_user->save();
		if (!$res) {
			return $this->no('删除失败');
		}

		return $this->yes("用户删除成功");
	}

	public function edit()
	{
		$edit_code = $this->edit_code;
		$edit_code_status = $this->checkCode($edit_code);
		if ($edit_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		$id = input('id');
		if (Request::instance()->isAjax()) {
			$name = input('name', '');
			$tel = input('tel', '');
			$password = input('password', '');
			$money = input('money', '0');
			if (empty($name)) {
				return $this->no("用户名不能为空");
			}
			if (empty($tel)) {
				return $this->no("请填写电话号码");
			}
			$res = preg_match("/^1[34578]\d{9}$/", $tel);
			if (!$res) {
				return $this->no("请填写正确的电话号码");
			}
			$user = new appUser;
			$is_exit_name = $user->where('name', $name)
				->where('id', 'neq', $id)
				->where('status', '1')
				->find();
			if ($is_exit_name) {
				return $this->no("用户名已存在");
			}
			$o_user = $user->where('id', $id)
				->where('status', '1')
				->find();
			if (!$o_user) {
				return $this->no("对象属性错误");
			}
			if (!empty($password)) {
				$o_user->password = md5($password);
			}
			$o_user->money = $money;
			$o_user->name = $name;
			$o_user->tel = $tel;
			$res = $o_user->save();
			if (!$res) {
				return $this->no("修改失败");
			}

			return $this->yes("用户信息修改成功");
		}
		if (Request::instance()->isGet()) {
			$user = new appUser;
			$o_user = $user->where('id', $id)
				->where('status', '1')
				->find();
			$this->assign('user', $o_user);

			return $this->fetch('edit');
		}
	}

	public function chongzhi()
	{
		$chongzhi_code = $this->chongzhi_code;
		$chongzhi_code_status = $this->checkCode($chongzhi_code);
		if ($chongzhi_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		$id = input('id');
		$money = input('money');
		$o_user = appUser::where('id', $id)
			->where('status', '1')
			->find();
		if (!$o_user) {
			return $this->no("对象属性错误");
		}
		$o_user->money += $money;
		$o_user->uid = $this->admin_id;
		$res = $o_user->save();
		if (!$res) {
			return $this->no("充值失败");
		} 

		return $this->yes("充值成功");
	}

	public function vip()
	{
		$vip_code = $this->vip_code;
		$vip_code_status = $this->checkCode($vip_code);
		if ($chongzhi_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		// 启动事务
        Db::startTrans();
        try{ 
        	$i_vip = input('vip');
			$id = input('id');
			$o_user = appUser::where('id', $id)
				->where('status', '1')
				->find();
			$o_user->vip = $i_vip;
			$o_user->uid = $this->admin_id;
			$res = $o_user->save();
			if (!$res) {
				return $this->no("操作失败");
			}
			$o_vip = Vip::where('user_id', $id)
				->where('status', '1')
				->find();
			if ($o_vip) {
				$o_vip->status = 0;
				$o_vip->save();
			}
			$vip = new Vip;
			$vip->user_id = $id;
			$vip->uid = $this->admin_id;
			$vip->cate_id = $i_vip;
			$time = VipCate::where('id', $i_vip)
				->value('day');
			$time = $time*60*60*24 + time();
			$vip->end_time = $this->time($time);
			$time = time();
			$vip->add_time = $this->time($time);
			$res = $vip->save();
			if (!$res) {
				return $this->no("操作失败");
			}
			
			return $this->yes("操作成功");
		} catch (\Exception $e) {
	             return $this->no($e->getMessage());
	            // 回滚事务
	            Db::rollback();
	        }
	}
}
