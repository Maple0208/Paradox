<?php
require_once __DIR__ . '/lib.php';

$action = input('action', 'list');

if($action === 'list'){
  $rows = query("SELECT * FROM customers");
  ok(array('list'=>$rows));
}

if($action === 'detail'){
  $id = intval(input('id'));
  $row = one("SELECT * FROM customers WHERE id=$id");
  if(!$row) err('客户不存在');
  ok(array('customer'=>$row));
}

if($action === 'save'){
  $cur = require_login();
  $id = input('id');
  $name = input('name');
  $contact = input('contact');
  $phone = input('phone');
  $level = input('level', '普通');
  $amount = intval(input('amount'));
  if($id){
    query("UPDATE customers SET name='".esc($name)."',contact='".esc($contact)."',phone='".esc($phone)."',
      level='".esc($level)."',amount=$amount WHERE id=".intval($id));
  } else {
    query("INSERT INTO customers (name,contact,phone,level,amount,owner)
      VALUES ('".esc($name)."','".esc($contact)."','".esc($phone)."','".esc($level)."',$amount,'".esc($cur['username'])."')");
  }
  add_log('编辑客户');
  ok();
}

if($action === 'delete'){
  query("DELETE FROM customers WHERE id=".intval(input('id')));
  add_log('删除客户');
  ok();
}

err('未知操作');
