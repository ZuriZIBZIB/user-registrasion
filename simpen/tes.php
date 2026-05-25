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
    $_SESSION['username'] = $u ;
    $_SESSION['role'] = $r ;
    header('location: ' . $r . '/dashboard.php');
    exit; } 
    else {
    $error = 'rusakkk';
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: Arial;
    background: #e9ecef;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
  }

  .box {
    background: white;
    padding: 30px;
    width: 300px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, .1);
  }

  h2 {
    margin-bottom: 20px;
    font-size: 20px;
  }

  label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    font-size: 14px;
  }

  input,
  select {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 3px;
  }

  button {
    width: 100%;
    padding: 10px;
    background: #333;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    font-weight: bold;
  }

  button:hover {
    background: #555;
  }

  .error {
    color: red;
    margin-bottom: 15px;
    font-size: 13px;
  }
</style>

<body>

  <div class="box">
    <h2>Login</h2>
    <?php if($error):?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>
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
