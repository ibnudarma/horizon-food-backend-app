<?php
require 'middleware.php';

switch ($method) {
    case 'GET':
        checkToken();

        try {
            $menu = [];

            // Ambil input dari query string
            $id = $_GET['id'] ?? null;

            if ($id !== null) {
                $stmt = $pdo->prepare("SELECT * FROM menu WHERE id_menu = ?");
                $stmt->execute([$id]);
            } else {
                $stmt = $pdo->query(
                    "SELECT m.id_menu, m.gambar, m.nama_menu, m.harga, c.category, s.nama_kantin
                     FROM menu AS m
                     JOIN category AS c ON m.category_id = c.id_category
                     JOIN seller AS s ON m.seller_id = s.id_seller"
                );
            }

            // Ambil data dari statement
            $menu = $stmt->fetchAll(PDO::FETCH_ASSOC);

            http_response_code(200);
            echo json_encode([
                'status'  => 200,
                'message' => 'Berhasil mengambil data',
                'data'    => $menu
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
