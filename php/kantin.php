<?php
require 'middleware.php';

switch ($method) {
    case 'GET':
        // checkToken();

        try {
            $input = json_decode(file_get_contents("php://input"), true);
            $query = "";
            if(isset( $input["id"] )) {
                $query = $pdo->prepare("SELECT * FROM seller WHERE id_seller = ?");
                $query->execute([$input['id']]);
            }else{
            $query = 'SELECT *
                      FROM seller';
            }
            $stmt = $pdo->query($query);
            $kantin = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $result = [
                'status' => http_response_code(200),
                'message'=> 'Berhasil mengambil data',
                'data'   => $kantin
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
