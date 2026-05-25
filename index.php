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
    } else {
        $error = "pastikan data yang dimasukan benar";
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form registrasi</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" >
</head>
<body>

<form action="" method="POST">
        
<table class="mx-auto p-2">
    
    <tr>
        <td>
            <h2 class="mx-auto p-2">Form registrasi</h2>
        </td>
    </tr>

    <tr>
        <td>
            <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">@</span>
            <input type="text" name="nama" id="nama" class="form-control" placeholder="Masukan Username" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </td>

        <!-- <td><label for="">Nama</label></td>
        <td>:</td>
        <td><input type="text" name="nama" id="nama" required></td> -->

    </tr>
    <tr>
        <td>
            <div class="input-group mb-3">
                <input type="text" name="email" id="email" class="form-control" placeholder="Masukan Email" aria-label="Recipient’s username" aria-describedby="basic-addon2">
                <span class="input-group-text" id="basic-addon2">@example.com</span>
            </div>
        </td>
        
        <!-- <td><label for="">Email</label></td>
        <td>:</td>
        <td><input type="email" name="email" id="email" required></td> -->
        
    </tr>
    <tr>
        
        <td>
            <div class="input-group mb-3">
                <input type="password" name="password" id="password" class="form-control" placeholder="Masukan Password" aria-label="Recipient’s username" aria-describedby="basic-addon2">
            </div>
        </td>

        <!-- <td><label for="">Password</label></td>
        <td>:</td>
        <td><input type="password" name="password" id="password" required></td> -->

    </tr>
    <tr>
        <td><button type="submit" name="submit" id="submit" class="btn btn-primary btn-lg mx-auto p-2" >Masuk</button></td>    </tr>
</table>


</form>
    
</body>
</html>