<?php
require_once __DIR__ . '/lib.php';

$action = input('action', 'get');

if($action === 'get'){
  $rows = query("SELECT * FROM settings");
  $s = array();
  foreach($rows as $r) $s[$r['k']] = $r['v'];
  $debugInfo = null;
  if(isset($s['debug']) && $s['debug'] === '1'){
    $debugInfo = array(
      'env'=>'production',
      'db'=>'mysql',
      'apiKey'=>isset($s['apiKey'])?$s['apiKey']:'',
      'smtp'=>isset($s['smtp'])?$s['smtp']:'',
      'token'=>isset($_COOKIE['ems_token'])?$_COOKIE['ems_token']:'',
      'cookie'=>$_SERVER['HTTP_COOKIE'] ?? ''
    );
  }
  ok(array('settings'=>$s, 'debug'=>$debugInfo));
}

if($action === 'save'){
  $map = json_body();
  foreach($map as $k=>$v){
    if($k === 'action') continue;
    query("INSERT INTO settings (k,v) VALUES ('".esc($k)."','".esc($v)."')
      ON DUPLICATE KEY UPDATE v='".esc($v)."'");
  }
  add_log('修改系统设置');
  ok();
}

err('未知操作');
