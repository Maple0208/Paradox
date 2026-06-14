<?php
require_once __DIR__ . '/lib.php';

$action = input('action', 'list');

if($action === 'list'){
  $rows = query("SELECT id,name,size,owner,path,time FROM files ORDER BY id DESC");
  ok(array('list'=>$rows));
}

if($action === 'upload'){
  $cur = require_login();
  $path = input('path', '/uploads/'.$cur['username'].'/');
  $desc = input('desc');
  $t = now();
  if(isset($_FILES['file']) && $_FILES['file']['error'] === 0){
    $name = input('name');
    if($name === '') $name = $_FILES['file']['name'];
    $size = round($_FILES['file']['size']/1024).'KB';
    $target = $UPLOAD_DIR . $name;
    @move_uploaded_file($_FILES['file']['tmp_name'], $target);
    $content = @file_get_contents($target);
    query("INSERT INTO files (name,size,owner,path,time,content)
      VALUES ('".esc($name)."','$size','".esc($cur['username'])."','".esc($path.$name)."','$t','".esc($content)."')");
    add_log('上传文件 '.$name);
    ok(array('name'=>$name));
  } else {
    $name = input('name');
    query("INSERT INTO files (name,size,owner,path,time,content)
      VALUES ('".esc($name)."','0KB','".esc($cur['username'])."','".esc($path.$name)."','$t','".esc($desc)."')");
    ok(array('name'=>$name));
  }
}

if($action === 'download'){
  $id = intval(input('id'));
  $row = one("SELECT * FROM files WHERE id=$id");
  if(!$row) err('文件不存在');
  add_log('下载文件 '.$row['name']);
  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename="'.$row['name'].'"');
  echo $row['content'];
  exit;
}

if($action === 'read'){
  $path = input('path');
  $row = one("SELECT * FROM files WHERE path='".esc($path)."'");
  if($row){ ok(array('file'=>$row)); }
  $real = $UPLOAD_DIR . $path;
  if(file_exists($real)){
    ok(array('file'=>array('name'=>basename($path), 'path'=>$path, 'content'=>file_get_contents($real))));
  }
  err('文件不存在: '.$path);
}

err('未知操作');
