<?php
require_once __DIR__ . '/lib.php';

$q = input('q');

$emps = query("SELECT * FROM employees WHERE name LIKE '%$q%' OR dept LIKE '%$q%' OR position LIKE '%$q%'");
$custs = query("SELECT * FROM customers WHERE name LIKE '%$q%' OR contact LIKE '%$q%'");
$notices = query("SELECT * FROM notices WHERE title LIKE '%$q%' OR content LIKE '%$q%'");

if($emps === false) $emps = array();
if($custs === false) $custs = array();
if($notices === false) $notices = array();

ok(array(
  'q'=>$q,
  'employees'=>$emps,
  'customers'=>$custs,
  'notices'=>$notices,
  'total'=>count($emps)+count($custs)+count($notices)
));
