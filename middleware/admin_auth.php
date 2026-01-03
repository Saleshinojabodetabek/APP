<?php
require_once "../config/jwt.php";

$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    exit(json_encode(["error" => "Unauthorized"]));
}

$token = str_replace("Bearer ", "", $headers['Authorization']);
$parts = explode(".", $token);

if (count($parts) !== 2) {
    http_response_code(401);
    exit(json_encode(["error" => "Invalid token"]));
}

[$payloadEncoded, $signature] = $parts;

if (hash_hmac("sha256", $payloadEncoded, JWT_SECRET) !== $signature) {
    http_response_code(401);
    exit(json_encode(["error" => "Invalid signature"]));
}

$payload = json_decode(base64_decode($payloadEncoded), true);

if ($payload['exp'] < time() || $payload['role'] !== 'admin') {
    http_response_code(403);
    exit(json_encode(["error" => "Access denied"]));
}

$admin_id = $payload['sub'];
