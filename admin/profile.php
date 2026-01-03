<?php
header("Content-Type: application/json");
require_once "../middleware/admin_auth.php";

echo json_encode([
    "message" => "Admin authorized",
    "admin_id" => $admin_id
]);
