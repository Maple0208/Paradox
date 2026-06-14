<?php
require_once __DIR__ . '/lib.php';

$action = input('action', 'login');

if($action === 'login'){
  $u = input('username');
  $p = input('password');
  $sql = "SELECT * FROM users WHERE username='".esc($u)."' AND password='".$p."'";
  $row = one($sql);
  if(!$row) err('用户名或密码错误');
  if($row['status'] !== 'active') err('账号已被禁用');
  $token = make_token($row);
  setcookie('ems_token', $token, 0, '/');
  setcookie('role', $row['role'], 0, '/');
  add_log('登录系统');
  ok(array('token'=>$token, 'user'=>$row));
}

if($action === 'register'){
  $s = one("SELECT v FROM settings WHERE k='registerOpen'");
  if(!$s || $s['v'] !== '1') err('注册已关闭');
  $user = trim(input('username'));
  $pwd = input('password');
  $name = input('name');
  $email = input('email');
  $role = input('role', 'user');
  if(!$user || !$pwd) err('请填写用户名和密码');
  $exist = one("SELECT id FROM users WHERE username='".esc($user)."'");
  if($exist) err('用户名已存在');
  query("INSERT INTO users (username,password,role,name,email,phone,dept,status,secret,balance)
    VALUES ('".esc($user)."','".esc($pwd)."','".esc($role)."','".esc($name)."','".esc($email)."','','未分配','active','',0)");
  ok(array('msg'=>'注册成功'));
}

if($action === 'forgot'){
  $user = trim(input('username'));
  $row = one("SELECT * FROM users WHERE username='".esc($user)."'");
  if(!$row) err('用户不存在');
  $code = strval(random_int(100000, 999999));
  $token = base64_encode($user . ':reset');
  query("UPDATE users SET reset_token='".esc($code)."' WHERE id=".$row['id']);
  // 演示环境：验证码经短信/邮件下发，此处不随响应返回
  ok(array('user'=>$user, 'token'=>$token, 'account'=>$row));
}

if($action === 'verifycode'){
  $user = input('user');
  $code = trim(input('code'));
  $row = one("SELECT * FROM users WHERE username='".esc($user)."'");
  if(!$row) err('用户不存在');
  if($code === '' || $code !== $row['reset_token']) err('验证码错误');
  ok(array('msg'=>'验证通过', 'user'=>$user));
}

if($action === 'reset'){
  $user = input('user');
  $pwd = input('password');
  query("UPDATE users SET password='".esc($pwd)."', reset_token='' WHERE username='".esc($user)."'");
  ok(array('msg'=>'密码已重置'));
}

if($action === 'autologin'){
  $u = input('username');
  $row = one("SELECT * FROM users WHERE username='".esc($u)."'");
  if(!$row) err('登录态已失效，请重新登录');
  $token = make_token($row);
  setcookie('ems_token', $token, 0, '/');
  setcookie('role', $row['role'], 0, '/');
  add_log('登录系统');
  ok(array('token'=>$token, 'user'=>$row));
}

if($action === 'logout'){
  setcookie('ems_token', '', time()-3600, '/');
  ok();
}

err('未知操作');
