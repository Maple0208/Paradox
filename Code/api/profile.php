<?php
require_once __DIR__ . '/lib.php';

$action = input('action', 'get');

if($action === 'get'){
  $id = input('id');
  if($id === ''){
    $cur = require_login();
    $id = $cur['id'];
  }
  $row = one("SELECT * FROM users WHERE id=".$id);
  if(!$row) err('用户不存在');
  ok(array('user'=>$row));
}

if($action === 'save'){
  $id = input('id');
  $fields = array('username','name','email','phone','dept','role','balance');
  $sets = array();
  foreach($fields as $f){
    $v = input($f, null);
    if($v !== null){
      if($f === 'balance') $sets[] = "balance=".intval($v);
      else $sets[] = "$f='".esc($v)."'";
    }
  }
  if($sets){
    query("UPDATE users SET ".implode(',', $sets)." WHERE id=".intval($id));
  }
  add_log('修改个人资料');
  $row = one("SELECT * FROM users WHERE id=".intval($id));
  ok(array('user'=>$row));
}

if($action === 'changepwd'){
  $cur = require_login();
  $newp = input('newpwd');
  query("UPDATE users SET password='".esc($newp)."' WHERE id=".intval($cur['id']));
  add_log('修改密码');
  ok(array('msg'=>'密码修改成功'));
}

if($action === 'memberlist'){
  $rows = query("SELECT id,name,username,role,dept FROM users");
  ok(array('list'=>$rows));
}

err('未知操作');
