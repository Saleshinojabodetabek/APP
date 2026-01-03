<?php
session_start();
require_once "../config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $phone    = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($phone === '' || $password === '') {
        $error = "Nomor HP dan password wajib diisi";
    } else {

        $stmt = $pdo->prepare("
            SELECT * FROM users
            WHERE phone = ?
              AND role = 'admin'
              AND status = 'active'
            LIMIT 1
        ");
        $stmt->execute([$phone]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($password, $admin['password'])) {
            $error = "Login admin gagal";
        } else {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];

            header("Location: dashboard.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body {
    margin: 0;
    height: 100vh;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #0f4c75, #3282b8);
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-box {
    background: #fff;
    width: 100%;
    max-width: 380px;
    padding: 35px;
    border-radius: 14px;
    box-shadow: 0 15px 40px rgba(0,0,0,.2);
    text-align: center;
}

.login-box img {
    width: 90px;
    margin-bottom: 15px;
}

.login-box h2 {
    margin-bottom: 25px;
    color: #0f4c75;
}

.login-box input {
    width: 100%;
    padding: 12px 14px;
    margin-bottom: 14px;
    border-radius: 8px;
    border: 1px solid #ddd;
    font-size: 14px;
}

.login-box input:focus {
    outline: none;
    border-color: #3282b8;
}

.login-box button {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    background: #0f4c75;
    color: #fff;
    font-size: 15px;
    cursor: pointer;
}

.login-box button:hover {
    background: #3282b8;
}

.error {
    background: #ffe5e5;
    color: #c0392b;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 14px;
}
.footer {
    margin-top: 18px;
    font-size: 12px;
    color: #888;
}
</style>
</head>

<body>

<div class="login-box">

    <img src="/admin/logo.png" alt="logo">

    <h2>Login Admin</h2>

    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="phone" placeholder="Nomor HP" autocomplete="off">
        <input type="password" name="password" placeholder="Password">
        <button type="submit">Login</button>
    </form>

    <div class="footer">
        &copy; <?= date('Y') ?> Admin Panel
    </div>

</div>

</body>
</html>
