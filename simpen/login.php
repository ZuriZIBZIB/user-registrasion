<?php
session_start();
require 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, nama, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];

        header("Location: " . ($user['role'] === 'admin' ? 'admin.php' : 'dashboard.php'));
        exit;
    } else {
        $error = 'Email atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Pengaduan Sekolah</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: sans-serif;
      font-size: 15px;
      background: #f4f4f4;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }

    .kotak-login {
      background: white;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 32px 28px;
      width: 100%;
      max-width: 380px;
    }

    h1 { font-size: 18px; margin-bottom: 6px; }
    p.sub { font-size: 13px; color: #888; margin-bottom: 24px; }

    label { display: block; font-size: 13px; color: #555; margin-bottom: 4px; margin-top: 14px; }

    input[type="email"], input[type="password"] {
      width: 100%;
      padding: 9px 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
    }

    button {
      width: 100%;
      padding: 10px;
      background: #1a56db;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 14px;
      cursor: pointer;
      margin-top: 20px;
    }

    button:hover { background: #1648c0; }

    .error {
      background: #fee2e2;
      color: #991b1b;
      border: 1px solid #fca5a5;
      padding: 9px 12px;
      border-radius: 4px;
      font-size: 13px;
      margin-bottom: 14px;
    }
  </style>
</head>
<body>
<div class="kotak-login">
  <h1>📢 Pengaduan Sekolah</h1>
  <p class="sub">Masuk untuk mengajukan saran</p>

  <?php if ($error): ?>
  <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <label for="email">Email</label>
    <input type="email" name="email" id="email" required placeholder="email@siswa.sch.id">

    <label for="password">Password</label>
    <input type="password" name="password" id="password" required placeholder="••••••••">

    <button type="submit">Masuk</button>
  </form>
</div>
</body>
</html>