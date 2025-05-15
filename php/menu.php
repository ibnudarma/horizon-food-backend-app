<?php
require 'middleware.php';

switch ($method) {
    case 'GET':
        // checkToken();

        try {
            $query = 'SELECT m.gambar, m.nama_menu, m.harga, c.category, s.nama_kantin
                      FROM menu as m JOIN category as c ON m.category_id = c.id_category
                      JOIN seller as s ON m.seller_id = s.id_seller';
            $stmt = $pdo->query($query);
            $menu = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $result = [
                'status' => http_response_code(200),
                'message'=> 'Berhasil mengambil data',
                'data'   => $menu
            ];
            
            echo json_encode($result);

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
