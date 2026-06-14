<?php
$DB_HOST = getenv('EMS_DB_HOST') ?: '127.0.0.1';
$DB_PORT = getenv('EMS_DB_PORT') ?: '3306';
$DB_NAME = getenv('EMS_DB_NAME') ?: 'Paradox';
$DB_USER = getenv('EMS_DB_USER') ?: 'root';
$DB_PASS = getenv('EMS_DB_PASS') ?: 'root';

$UPLOAD_DIR = __DIR__ . '/uploads/';

date_default_timezone_set('Asia/Shanghai');
