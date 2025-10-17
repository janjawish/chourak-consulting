<?php
ini_set('display_errors',1); error_reporting(E_ALL);
$cfg = require __DIR__.'/config.php';
$dsn = "mysql:host={$cfg['db']['host']};port={$cfg['db']['port']};dbname={$cfg['db']['name']};charset={$cfg['db']['charset']}";
$pdo = new PDO($dsn, $cfg['db']['user'], $cfg['db']['pass'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
echo "OK: connecté\n";
$pdo->query("INSERT INTO contacts (email, message) VALUES ('test@example.com','ping')");
echo "OK: insert\n";
