<?php

$db = 'mysql';
$host = 'localhost';
$db_name = 'horizon_food';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("$db:host=$host;dbname=$db_name;charset=utf8", $username, $password);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}