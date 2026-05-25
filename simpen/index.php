<?php

session_start();

$users = [
  'admin' => ['password' => 'admin123','role' => 'admin'],
  'siswa' => ['password' => 'siswa123','role' => 'siswa'],
];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = $_POST['username'] ?? '';
  $p = $_POST['password'] ?? '';
  $r = $_POST['role'] ?? '';

  if(isset($users[$u]) && $users[$u]['password'] === $p && $users[$u]['role'] === $r) {
    $_SESSION['username'] = $u;
    $_SESSION['role'] = $r;
    header('location: ' . $r . '/dashboard.php');
    exit();
  } else {
    $error = 'Pastikan Username/Password benar!';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form Login</title>
</head>

<style>

</style>

<body>

  <div class="box">
    <h2>Login</h2>
  <form action="" method="POST">

  <label for="">Role</label>
  <select name="role" id="">
    <option value="admin">Admin</option>
    <option value="siswa">Siswa</option>
  </select>

  <label for="">Username</label>
  <input type="text" name="username" placeholder="Masukan Username" required>
  
  <label for="">Password</label>
  <input type="password" name="password" placeholder="Masukan Password" required>

  <button type="submit">login</button>

    </form>
</div>


  
</body>
</html>