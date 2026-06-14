<?php
require_once __DIR__ . '/config.php';

function db(){
  static $conn = null;
  if($conn === null){
    global $DB_HOST, $DB_PORT, $DB_NAME, $DB_USER, $DB_PASS;
    $conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, intval($DB_PORT));
    if($conn->connect_errno){
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(array('ok'=>false, 'msg'=>'数据库连接失败: '.$conn->connect_error));
      exit;
    }
    $conn->set_charset('utf8mb4');
  }
  return $conn;
}

/* 查询：SELECT 返回数组，其它返回布尔 */
function query($sql){
  $res = db()->query($sql);
  if($res === false) return false;
  if($res === true) return true;
  $rows = array();
  while($row = $res->fetch_assoc()) $rows[] = $row;
  return $rows;
}

/* 取单行 */
function one($sql){
  $rows = query($sql);
  if($rows === false || count($rows) === 0) return null;
  return $rows[0];
}
