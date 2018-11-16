<?php

namespace app\admin\controller\task;

use think\Request;
use app\admin\controller\Base;
use app\admin\model\task\TaskCate;
use app\admin\model\task\Task as adminTask;

class Task extends Base
{
	public function lst()
	{
		$title = input('title', '');
		$s_title_eq = $title == '' ? 'neq' : 'eq';
		$cate_id = input('cate_id', '');
		$s_cate_id_eq = $cate_id == '' ? 'neq' : 'eq';
		$o_tasks = adminTask::where('status', '1')
			->where('title', $s_title_eq, $title)
			->where('cate_id', $s_cate_id_eq, $cate_id)
			->paginate(3, false, [
                'query' => Request::instance()->param(),//不丢失已存在的url参数
            ]);
        $o_cates = TaskCate::where('status', '1')
        	->select();
        $a_arr = array();
        $a_arr['title'] = $title;
        $a_arr['cate_id'] = $cate_id;
        $a_arr['tasks'] = $o_tasks;
        $a_arr['cates'] = $o_cates;

		return $this->fetch('lst', $a_arr);
	}

	public function add()
	{
		if (Request::instance()->isGet()) {
			$o_cates = TaskCate::where('status', '1')
        		->select();
    		$this->assign('cates', $o_cates);

			return $this->fetch('add');
		}
		if (Request::instance()->isAjax()) {
			$title = input('title', '');
			$inte = input('inte', '');
			$cate_id = input('cate_id', '');
			$content = input('content', '');
			if (empty($title)) {
				return $this->no('请输入任务标题');
			}
			if (!preg_match("/^[1-9][0-9]*$/", $inte)) {
				return $this->no('请输入正确的积分格式');
			}
			if (empty($cate_id)) {
				return $this->no('请选择任务所属的分类');
			}
			if (empty($content)) {
				return $this->no('请填写任务描述');
			}
			$task = new adminTask;
			$task->title = $title;
			$task->integral = $inte;
			$task->cate_id = $cate_id;
			$task->content = $content;
			$task->uid = $this->admin_id;
			$task->add_time = time();
			$res = $task->save();
			if (!$res) {
				return $this->no('添加失败');
			}

			return $this->yes('添加成功');
		}	
	}

	public function edit()
	{
		if (Request::instance()->isGet()) {
			$id = input('id');
			$o_task = adminTask::where('id', $id)
				->where('status', '1')
				->find();
			$this->assign('task', $o_task);
			$o_cates = TaskCate::where('status', '1')
        		->select();
    		$this->assign('cates', $o_cates);

			return $this->fetch('edit');
		}
		if (Request::instance()->isAjax()) {
			$id = input('id');
			$title = input('title', '');
			$inte = input('inte', '');
			$cate_id = input('cate_id', '');
			$content = input('content', '');
			if (empty($title)) {
				return $this->no('请输入任务标题');
			}
			if (!preg_match("/^[1-9][0-9]*$/", $inte)) {
				return $this->no('请输入正确的积分格式');
			}
			if (empty($cate_id)) {
				return $this->no('请选择任务所属的分类');
			}
			if (empty($content)) {
				return $this->no('请填写任务描述');
			}
			$o_task = adminTask::where('id', $id)
				->where('status', '1')
				->find();
			$o_task->title = $title;
			$o_task->integral = $inte;
			$o_task->cate_id = $cate_id;
			$o_task->content = $content;
			$o_task->uid = $this->admin_id;
			$o_task->audit_time = time();
			$res = $o_task->save();
			if (!$res) {
				return $this->no('修改失败');
			}

			return $this->yes('修改成功');
		}
	}

	public function del()
	{

		$a_id = array();
		$a_id = input('id');
		preg_match_all('/\d+/', $a_id, $arr);
		foreach ($arr[0] as $vv) {
			$task = adminTask::where('id', $vv)
				->where('status', '1')
				->find();
			$task->status = '0';
			$task->save();
		}

		return $this->yes('删除成功');	
	}
}
