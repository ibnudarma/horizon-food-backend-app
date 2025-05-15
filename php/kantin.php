<?php
require 'middleware.php';

switch ($method) {
    case 'GET':
        checkToken();

        try {
            $kantin = [];

            // Ambil input dari query string
            $id = $_GET['id'] ?? null;

            if ($id !== null) {
                $stmt = $pdo->prepare("SELECT * FROM seller WHERE id_seller = ?");
                $stmt->execute([$id]);
            } else {
                $stmt = $pdo->query("SELECT * FROM seller");
            }

            $kantin = $stmt->fetchAll(PDO::FETCH_ASSOC);

            http_response_code(200);
            echo json_encode([
                'status'  => 200,
                'message' => 'Berhasil mengambil data',
                'data'    => $kantin
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'status'  => 500,
                'message' => 'Gagal mengambil data',
                'error'   => $e->getMessage()
            ]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
