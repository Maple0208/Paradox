<?php
require_once __DIR__ . '/lib.php';

$action = input('action', 'list');

if($action === 'list'){
  $rows = query("SELECT * FROM notices ORDER BY top DESC, id DESC");
  ok(array('list'=>$rows));
}

if($action === 'view'){
  $row = one("SELECT * FROM notices WHERE id=".intval(input('id')));
  if(!$row) err('公告不存在');
  ok(array('notice'=>$row));
}

if($action === 'save'){
  $cur = require_login();
  $title = input('title');
  $content = input('content');
  $top = input('top') ? 1 : 0;
  $t = now();
  query("INSERT INTO notices (title,content,author,time,top)
    VALUES ('".esc($title)."','".esc($content)."','".esc($cur['username'])."','$t',$top)");
  add_log('发布公告');
  ok();
}

if($action === 'delete'){
  query("DELETE FROM notices WHERE id=".intval(input('id')));
  add_log('删除公告');
  ok();
}

err('未知操作');
