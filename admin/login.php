<?php
header("Content-Type: application/json");
require_once "../config/database.php";

/*
  Ambil data JSON dari request
*/
$data = json_decode(file_get_contents("php://input"), true);

/*
  Validasi input
*/
if (
    !isset($data['phone']) ||
    !isset($data['password']) ||
    $data['phone'] === '' ||
    $data['password'] === ''
) {
    http_response_code(400);
    echo json_encode(["error" => "Phone dan password wajib"]);
    exit;
}

$phone = $data['phone'];
$password = $data['password'];

/*
  Cari user ADMIN berdasarkan phone
*/
$sql = "SELECT * FROM users 
        WHERE phone = ? 
        AND role = 'admin' 
        AND status = 'active'
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([$phone]);
$admin = $stmt->fetch();

if (!password_verify($password, $admin['password'])) {
    echo json_encode(["error" => "Login admin gagal"]);
    exit;
}



/*
  Jika admin tidak ditemukan
*/
if (!$admin) {
    http_response_code(401);
    echo json_encode(["error" => "Login admin gagal"]);
    exit;
}

/*
  Cek password hash
*/
if (!password_verify($password, $admin['password'])) {
    http_response_code(401);
    echo json_encode(["error" => "Login admin gagal"]);
    exit;
}

/*
  LOGIN BERHASIL
*/
echo json_encode([
    "success" => true,
    "admin" => [
        "id" => $admin['id'],
        "name" => $admin['name'],
        "phone" => $admin['phone']
    ]
]);
