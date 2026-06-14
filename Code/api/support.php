<?php
require_once __DIR__ . '/lib.php';

$action = input('action', 'list');

if($action === 'list'){
  $rows = query("SELECT * FROM chat ORDER BY id");
  ok(array('list'=>$rows));
}

if($action === 'send'){
  $cur = require_login();
  $text = input('text');
  if($text === '') err('内容为空');
  $t = now();
  query("INSERT INTO chat (who,uid,text,time) VALUES ('".esc($cur['name'])."',".intval($cur['id']).",'".esc($text)."','$t')");
  $reply = '感谢咨询，您的问题“'.$text.'”已记录，客服稍后回复。';
  query("INSERT INTO chat (who,uid,text,time) VALUES ('客服小助手',0,'".esc($reply)."','$t')");
  ok(array('reply'=>$reply, 'who'=>$cur['name']));
}

err('未知操作');
