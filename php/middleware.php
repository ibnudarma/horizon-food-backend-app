<?php
require 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function getAuthorizationHeader() {
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        return trim($_SERVER["HTTP_AUTHORIZATION"]);
    }

    // Fallback untuk beberapa konfigurasi Apache
    if (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            return trim($headers['Authorization']);
        }
    }

    return null;
}

function checkToken()
{
    $authHeader = getAuthorizationHeader();
    if (!$authHeader) {
        http_response_code(401);
        echo json_encode(['error' => 'Token tidak ditemukan']);
        exit;
    }

    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(['error' => 'Format token salah']);
        exit;
    }

    $jwt = $matches[1];
    $secretKey = "Horizon_Food_Secret_Key";

    try {
        $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));
        return $decoded;

    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode([
            'status' => false,
            'error' => 'Token tidak valid atau kadaluarsa', 
            'detail' => $e->getMessage()
        ]);
        exit;
    }
}

