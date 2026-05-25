<?php

include 'koneksi.php';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $nama = $_POST['nama'];
    $password = $_POST['password'];

    if (!empty('email') && !empty('nama') && !empty('password')) {

        $query = "INSERT INTO tb_users (email,nama,password) VALUES ('$email','$nama','$password')";
        $eksekusi = mysqli_query($koneksi,$query);

        mysqli_close($koneksi);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form registrasi</title>
</head>
<body>


<h2>Form registrasi</h2>

<form action="" method="POST">

<table>
    <tr>
        <td><label for="">Nama</label></td>
        <td>:</td>
        <td><input type="text" name="nama" id="nama" required></td>
    </tr>
    <tr>
        <td><label for="">Email</label></td>
        <td>:</td>
        <td><input type="email" name="email" id="email" required></td>
    </tr>
    <tr>
        <td><label for="">Password</label></td>
        <td>:</td>
        <td><input type="password" name="password" id="password" required></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td><button type="submit" name="submit" id="submit" >Masuk</button></td>
    </tr>
</table>


</form>
    
</body>
</html>