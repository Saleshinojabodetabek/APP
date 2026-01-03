<?php
header("Content-Type: application/json");
require_once "../config/database.php";
require_once "../config/jwt.php";

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['phone']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(["error" => "Phone dan password wajib"]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, name, phone, password, role 
    FROM users 
    WHERE phone = ? AND role = 'admin' AND status = 'active'
");
$stmt->execute([$data['phone']]);
$admin = $stmt->fetch();

if (!$admin || !password_verify($data['password'], $admin['password'])) {
    http_response_code(401);
    echo json_encode(["error" => "Login admin gagal"]);
    exit;
}

$payload = [
    "sub"  => $admin['id'],
    "role" => "admin",
    "exp"  => time() + JWT_EXPIRE
];

$payloadEncoded = base64_encode(json_encode($payload));
$signature = hash_hmac("sha256", $payloadEncoded, JWT_SECRET);

echo json_encode([
    "token" => $payloadEncoded . "." . $signature,
    "admin" => [
        "id" => $admin['id'],
        "name" => $admin['name'],
        "phone" => $admin['phone']
    ]
]);
