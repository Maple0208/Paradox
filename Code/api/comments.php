<?php
require_once __DIR__ . '/lib.php';

$action = input('action', 'list');

if($action === 'list'){
  $rows = query("SELECT * FROM comments ORDER BY id");
  ok(array('list'=>$rows));
}

if($action === 'add'){
  $cur = require_login();
  $content = input('content');
  $t = now();
  query("INSERT INTO comments (user,content,time) VALUES ('".esc($cur['name'])."','".esc($content)."','$t')");
  add_log('发表评论');
  ok(array('user'=>$cur['name'], 'content'=>$content, 'time'=>$t));
}

if($action === 'delete'){
  $cur = require_login();
  $id = intval(input('id'));
  $row = one("SELECT * FROM comments WHERE id=$id");
  if(!$row) err('评论不存在');
  if($row['user'] !== $cur['name'] && input('role') !== 'admin') err('无权删除');
  query("DELETE FROM comments WHERE id=$id");
  add_log('删除评论');
  ok();
}

err('未知操作');
