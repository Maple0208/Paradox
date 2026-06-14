<?php
require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, X-Token, X-Role');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){ exit; }

function input($k, $def=''){
  if(isset($_GET[$k])) return $_GET[$k];
  if(isset($_POST[$k])) return $_POST[$k];
  $body = json_body();
  if(isset($body[$k])) return $body[$k];
  return $def;
}

function json_body(){
  static $b = null;
  if($b === null){
    $raw = file_get_contents('php://input');
    $b = json_decode($raw, true);
    if(!is_array($b)) $b = array();
  }
  return $b;
}

function out($data){
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

function err($msg, $extra=array()){
  out(array_merge(array('ok'=>false, 'msg'=>$msg), $extra));
}

function ok($data=array()){
  out(array_merge(array('ok'=>true), $data));
}

function token_user(){
  $t = isset($_SERVER['HTTP_X_TOKEN']) ? $_SERVER['HTTP_X_TOKEN'] : (isset($_COOKIE['ems_token']) ? $_COOKIE['ems_token'] : '');
  if(!$t) return null;
  $raw = base64_decode($t);
  $obj = json_decode($raw, true);
  if(!is_array($obj) || !isset($obj['id'])) return null;
  return $obj;
}

function make_token($u){
  return base64_encode(json_encode(array(
    'id'=>$u['id'], 'username'=>$u['username'], 'role'=>$u['role'], 'name'=>$u['name']
  ), JSON_UNESCAPED_UNICODE));
}

function require_login(){
  $u = token_user();
  if(!$u) err('未登录');
  return $u;
}

function now(){
  return date('Y-m-d H:i');
}

function add_log($action){
  $u = token_user();
  if(!$u) return;
  $user = esc($u['username']);
  $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
  $t = now();
  query("INSERT INTO logs (user,action,ip,time) VALUES ('$user','".esc($action)."','$ip','$t')");
}

function esc($s){
  return db()->real_escape_string($s);
}
