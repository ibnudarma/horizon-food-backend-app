<?php
require 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function checkToken()
{
    // Ambil Authorization header
    $headers = apache_request_headers();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Token tidak ditemukan']);
        exit;
    }

    // Format: Bearer <token>
    $authHeader = $headers['Authorization'];
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(['error' => 'Format token salah']);
        exit;
    }

    $jwt = $matches[1];
    $secretKey = "Horizon_Food_Secret_Key";

    try {
        // Decode token
        $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));
        // Kembalikan payload token
        return (array) $decoded;

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
