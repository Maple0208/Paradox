<?php
require_once __DIR__ . '/lib.php';

$type = input('type', 'employees');
$fmt = input('fmt', 'csv');

$allow = array('employees','customers','users','logs');
$table = in_array($type, $allow) ? $type : $type;

$rows = query("SELECT * FROM $table");
if($rows === false) $rows = array();

add_log('导出数据 '.$type);

if($fmt === 'json'){
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  exit;
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="'.$type.'.csv"');
if(count($rows)){
  $keys = array_keys($rows[0]);
  echo implode(',', $keys) . "\n";
  foreach($rows as $r){
    $line = array();
    foreach($keys as $k) $line[] = $r[$k];
    echo implode(',', $line) . "\n";
  }
}
exit;
