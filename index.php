<?php
header("Content-Type: application/json");
require_once("php/database.php");

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', trim($uri, '/'));

$resource = $uri[1] ?? null;

switch ($resource) {
    case 'auth':
        require_once 'php/auth.php';
        break;
    case 'accounts':
        require_once 'php/account.php';
        break;
    default:
        http_response_code(404);
        echo json_encode([
            'status'  => false,
            'message' => 'Endpoint not found'
        ]);
        break;
}
