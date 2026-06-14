<?php
require_once __DIR__ . '/lib.php';

$action = input('action', 'list');

if($action === 'list'){
  $rows = query("SELECT * FROM users");
  $stat = array(
    'total'=>count($rows),
    'admins'=>count(array_filter($rows, function($x){ return $x['role']==='admin'; })),
    'logs'=>count(query("SELECT id FROM logs")),
    'files'=>count(query("SELECT id FROM files"))
  );
  ok(array('list'=>$rows, 'stat'=>$stat));
}

if($action === 'save'){
  $id = input('id');
  $username = input('username');
  $password = input('password');
  $name = input('name');
  $role = input('role', 'user');
  $dept = input('dept');
  $balance = intval(input('balance', 0));
  if($id){
    query("UPDATE users SET username='".esc($username)."',password='".esc($password)."',name='".esc($name)."',
      role='".esc($role)."',dept='".esc($dept)."',balance=$balance WHERE id=".intval($id));
  } else {
    query("INSERT INTO users (username,password,name,role,dept,status,email,phone,secret,balance)
      VALUES ('".esc($username)."','".esc($password)."','".esc($name)."','".esc($role)."','".esc($dept)."','active','','','',$balance)");
  }
  add_log('管理用户 '.$username);
  ok();
}

if($action === 'toggle'){
  $id = intval(input('id'));
  $row = one("SELECT status FROM users WHERE id=$id");
  $ns = ($row && $row['status']==='active') ? 'disabled' : 'active';
  query("UPDATE users SET status='$ns' WHERE id=$id");
  ok(array('status'=>$ns));
}

if($action === 'delete'){
  query("DELETE FROM users WHERE id=".intval(input('id')));
  add_log('删除用户');
  ok();
}

if($action === 'changerole'){
  $id = intval(input('id'));
  $role = input('role');
  query("UPDATE users SET role='".esc($role)."' WHERE id=$id");
  add_log('修改角色');
  ok();
}

err('未知操作');
