<?php
require_once __DIR__ . '/lib.php';

$rows = query("SELECT * FROM logs ORDER BY id DESC");
ok(array('list'=>$rows));
