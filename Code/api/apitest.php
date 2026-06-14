<?php
require_once __DIR__ . '/lib.php';

$ep = input('ep', '/api/user/info');
$p = input('p');

$resp = array('error'=>'unknown endpoint');

if($ep === '/api/user/info'){
  $resp = one("SELECT * FROM users WHERE id=$p");
  if(!$resp) $resp = array('error'=>'not found');
} else if($ep === '/api/users/list'){
  $resp = query("SELECT * FROM users");
} else if($ep === '/api/customer/detail'){
  $resp = one("SELECT * FROM customers WHERE id=$p");
  if(!$resp) $resp = array('error'=>'not found');
} else if($ep === '/api/file/read'){
  $resp = one("SELECT * FROM files WHERE path='".esc($p)."'");
  if(!$resp) $resp = array('error'=>'not found');
} else if($ep === '/api/admin/config'){
  $rows = query("SELECT * FROM settings");
  $resp = array();
  foreach($rows as $r) $resp[$r['k']] = $r['v'];
}

add_log('调用API '.$ep);
ok(array('endpoint'=>$ep, 'status'=>'200 OK', 'data'=>$resp));
