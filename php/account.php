<?php
require 'middleware.php';

switch ($method) {
    case 'GET':
        // checkToken();

        try {
            $query = 'SELECT a.id_account, a.email, a.role, c.nama, c.no_hp
                      FROM account as a JOIN customer as c ON a.id_account = c.account_id';
            $stmt = $pdo->query($query);
            $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $result = [
                'status' => http_response_code(200),
                'message'=> 'Berhasil mengambil data',
                'data'   => $accounts
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

    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);

        // Validasi input
        if (!isset($input['email']) || !isset($input['password']) || !isset($input['nama']) || !isset($input['no_hp'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid input: email, password, nama, and no_hp required']);
            exit;
        }

        try {
            // Mulai transaksi
            $pdo->beginTransaction();

            // Insert ke tabel account
            $stmt = $pdo->prepare("INSERT INTO account (email, password, role) VALUES (?, ?, ?)");
            $stmt->execute([
                $input['email'],
                password_hash($input['password'], PASSWORD_BCRYPT),
                'customer'
            ]);

            // Ambil id_account berdasarkan email (karena email UNIQUE)
            $stmt_id = $pdo->prepare("SELECT id_account FROM account WHERE email = ?");
            $stmt_id->execute([$input['email']]);
            $id_account = $stmt_id->fetchColumn();

            if (!$id_account) {
                throw new Exception('Gagal mengambil ID account');
            }

            // Insert ke tabel customer
            $stmt2 = $pdo->prepare("INSERT INTO customer (account_id, nama, no_hp) VALUES (?, ?, ?)");
            $stmt2->execute([$id_account, $input['nama'], $input['no_hp']]);

            // Commit transaksi
            $pdo->commit();

            // Response sukses
            http_response_code(201); // Created
            echo json_encode([
                'status'  => true,
                'message' => 'Data berhasil ditambahkan',
                'data'    => [
                    'email' => $input['email'],
                    'nama'  => $input['nama'],
                    'no_hp' => $input['no_hp']
                ]
            ]);

        } catch (Exception $e) {
            $pdo->rollBack();

            http_response_code(500);
            echo json_encode([
                'status'  => 500,
                'message' => 'Gagal menambahkan data',
                'error'   => $e->getMessage()
            ]);
        }
        break;

    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['id_account'], $input['nama'], $input['no_hp'], $input['email'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input: id_account, nama, no_hp, and email required']);
            exit;
        }

        try {
            $pdo->beginTransaction();

            // Update account (email)
            $stmt1 = $pdo->prepare("UPDATE account SET email = ? WHERE id_account = ?");
            $stmt1->execute([$input['email'], $input['id_account']]);

            // Update customer (nama, no_hp)
            $stmt2 = $pdo->prepare("UPDATE customer SET nama = ?, no_hp = ? WHERE account_id = ?");
            $stmt2->execute([$input['nama'], $input['no_hp'], $input['id_account']]);

            $pdo->commit();

            echo json_encode(['status' => true, 'message' => 'Data berhasil diupdate']);

        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'message' => 'Gagal mengupdate data',
                'error' => $e->getMessage()
            ]);
        }
        break;


    case 'DELETE':
        parse_str(file_get_contents("php://input"), $input);

        if (!isset($input['id_account'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id_account']);
            exit;
        }

        try {
            $pdo->beginTransaction();

            // Hapus dari tabel customer terlebih dahulu
            $stmt1 = $pdo->prepare("DELETE FROM customer WHERE account_id = ?");
            $stmt1->execute([$input['id_account']]);

            // Kemudian hapus dari tabel account
            $stmt2 = $pdo->prepare("DELETE FROM account WHERE id_account = ?");
            $stmt2->execute([$input['id_account']]);

            $pdo->commit();

            echo json_encode(['status' => true, 'message' => 'Data berhasil dihapus']);

        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'message' => 'Gagal menghapus data',
                'error' => $e->getMessage()
            ]);
        }
        break;


    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
