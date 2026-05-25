<?php

session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header('location:index.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>

<h1>Ini Dashboard Admin</h1>
 <a href="../index.php?logout=1">balik</a>

</body>
</html>