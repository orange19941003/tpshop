<?php
namespace app\index\controller;

use think\Session;
use think\Request;
use think\Controller;
use app\admin\model\User;

class Sys extends Controller 
{
    public function login() 
    {
        if (Request::instance()->isGet()) {
            return $this->fetch('login');
        } else {
            $code = input('code');
            if (!captcha_check($code)) {
                // 校验失败
                $this->error('验证码不正确');
            }
            $username = input('username', '');
            $password = input('password', '');
            if (empty($username)) {
                return $this->error('用户名不能为空');
            } elseif (empty($password)) {
                return $this->error('请输入您的密码');
            } else {
                $user = new User;
                $o_user = $user->where('name', $username)
                    ->where('status', '1')
                    ->find();
                if (!$o_user) {
                    return $this->error('用户名不存在');
                }
                $password = md5($password);
                if ($password != $o_user->password) {
                    return $this->error('密码错误');
                }
                Session::set('appUser', $username);

                return $this->success('登录成功', 'Index/Index');
            }
        }
        
    }

    public function logout()
    {
        Session::delete('appUser');
        return $this->success('退出成功', 'sys/login');
    }

    public function register()
    {
        if (Request::instance()->isPost()) {
            $username = input('username', '');
            if (empty($username)) {
                return $this->error('用户名不能为空');
            }
            $password = input('password');
            if (empty($password)) {
                return $this->error('密码不能为空');
            }
            $repassword = input('repassword');
            $code = input('code');
            $tel = input('tel');
            $res = preg_match("/^1[34578]\d{9}$/", $tel);
            if (!preg_match("/^1[34578]\d{9}$/", $tel)) {
                return $this->error("请输入正确的手机号码");
            } 
            if ($password != $repassword) {
                return $this->error('两次密码输入不一致');
            }
            if (!captcha_check($code)) {
                // 校验失败
                $this->error('验证码不正确');
            }
            $user = new User;
            $is_exit_name = $user->where('name', $username)
                ->where('status', '1')
                ->find();
            if ($is_exit_name) {
                return $this->error('用户名已存在');
            }
            $is_exit_tel = $user->where('tel', $tel)
                ->where('status', '1')
                ->find();
            if ($is_exit_tel) {
                return $this->error('手机号码已经注册');
            }
            $user->name = $username;
            $password = md5($password);
            $user->password = $password;
            $user->tel = $tel;
            $res = $user->save();

            return $this->success('注册成功', 'sys/login');
        }
       
        if (Request::instance()->isGet()) {

            return $this->fetch('register');
        }
    }
}
