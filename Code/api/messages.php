<?php
require_once __DIR__ . '/lib.php';

$action = input('action', 'list');

if($action === 'list'){
  $to = input('to');
  if($to === ''){
    $cur = require_login();
    $to = $cur['username'];
  }
  $rows = query("SELECT * FROM messages WHERE `to`='".esc($to)."' ORDER BY id DESC");
  ok(array('list'=>$rows));
}

if($action === 'unread'){
  $cur = require_login();
  $rows = query("SELECT id FROM messages WHERE `to`='".esc($cur['username'])."' AND `read`=0");
  ok(array('count'=>count($rows)));
}

if($action === 'read'){
  $id = intval(input('id'));
  query("UPDATE messages SET `read`=1 WHERE id=$id");
  $row = one("SELECT * FROM messages WHERE id=$id");
  ok(array('message'=>$row));
}

if($action === 'send'){
  $cur = require_login();
  $to = input('to');
  $title = input('title');
  $content = input('content');
  $t = now();
  query("INSERT INTO messages (`to`,`from`,title,content,`read`,time)
    VALUES ('".esc($to)."','".esc($cur['username'])."','".esc($title)."','".esc($content)."',0,'$t')");
  add_log('发送消息');
  ok();
}

err('未知操作');
