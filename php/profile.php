<?php
require 'middleware.php';

switch ($method) {
    case 'GET':
        try {
            $user = checkToken();
            $result = [
                'status' => http_response_code(200),
                'message'=> 'Berhasil mengambil data',
                'data'   => [
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'no_hp' => $user->no_hp,
                ]
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
