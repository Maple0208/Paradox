<?php
require_once __DIR__ . '/lib.php';

$action = input('action', 'list');

if($action === 'list'){
  $kw = input('kw');
  if($kw !== ''){
    $sql = "SELECT * FROM employees WHERE name LIKE '%$kw%' OR dept LIKE '%$kw%' OR position LIKE '%$kw%'";
    $rows = query($sql);
    if($rows === false) err('查询失败: '.db()->error);
  } else {
    $rows = query("SELECT * FROM employees");
  }
  ok(array('list'=>$rows));
}

if($action === 'save'){
  $id = input('id');
  $name = input('name');
  $dept = input('dept');
  $position = input('position');
  $salary = intval(input('salary'));
  $idcard = input('idcard');
  $entry = input('entry');
  if($id){
    query("UPDATE employees SET name='".esc($name)."',dept='".esc($dept)."',position='".esc($position)."',
      salary=$salary,idcard='".esc($idcard)."',entry='".esc($entry)."' WHERE id=".intval($id));
  } else {
    query("INSERT INTO employees (name,dept,position,salary,idcard,entry)
      VALUES ('".esc($name)."','".esc($dept)."','".esc($position)."',$salary,'".esc($idcard)."','".esc($entry)."')");
  }
  add_log('编辑员工 '.$name);
  ok();
}

if($action === 'delete'){
  query("DELETE FROM employees WHERE id=".intval(input('id')));
  add_log('删除员工');
  ok();
}

err('未知操作');
