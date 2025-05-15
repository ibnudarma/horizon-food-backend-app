<?php
require 'vendor/autoload.php';
use Firebase\JWT\JWT;

$input = json_decode(file_get_contents("php://input"), true);
if (!isset($input['email']) || !isset($input['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Email dan password wajib diisi']);
    exit;
}

try {
    // JOIN ke tabel customer
    $stmt = $pdo->prepare("
        SELECT a.*, c.id_customer, c.nama, c.no_hp
        FROM account a
        LEFT JOIN customer c ON a.id_account = c.account_id
        WHERE a.email = ?
    ");
    $stmt->execute([$input['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($input['password'], $user['password'])) {
        $payload = [
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24), // 24 jam
            'id_account' => $user['id_account'],
            'id_customer' => $user['id_customer'],
            'email' => $user['email'],
            'role' => $user['role'],
            'nama' => $user['nama'],
            'no_hp' => $user['no_hp']
        ];

        $secretKey = "Horizon_Food_Secret_Key";
        $jwt = JWT::encode($payload, $secretKey, 'HS256');

        echo json_encode([
            'status' => true,
            'message' => 'Login berhasil',
            'token' => $jwt,
            'data' => [
                'id_account' => $user['id_account'],
                'id_customer' => $user['id_customer'],
                'email' => $user['email'],
                'nama' => $user['nama'],
                'no_hp' => $user['no_hp']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Email atau password salah']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Terjadi kesalahan', 'detail' => $e->getMessage()]);
}
