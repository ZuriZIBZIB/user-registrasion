<?php
session_start();
require 'koneksi.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ── AJUKAN SARAN ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajukan'])) {
    $kategori_id = (int) $_POST['kategori_id'];
    $judul       = trim($_POST['judul']);
    $isi         = trim($_POST['isi']);

    if ($kategori_id && $judul && $isi) {
        $stmt = $pdo->prepare("
            INSERT INTO pengaduan (user_id, kategori_id, judul, isi)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $kategori_id, $judul, $isi]);
        $_SESSION['pesan'] = ['tipe' => 'sukses', 'teks' => 'Saran berhasil dikirim!'];
    } else {
        $_SESSION['pesan'] = ['tipe' => 'error', 'teks' => 'Semua kolom wajib diisi.'];
    }

    header("Location: dashboard.php");
    exit;
}

// ── HAPUS SARAN ───────────────────────────────────────
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];

    // Pastikan milik user ini dan masih menunggu
    $stmt = $pdo->prepare("
        SELECT id FROM pengaduan
        WHERE id = ? AND user_id = ? AND status = 'menunggu'
    ");
    $stmt->execute([$id, $user_id]);

    if ($stmt->fetch()) {
        $pdo->prepare("DELETE FROM pengaduan WHERE id = ?")->execute([$id]);
        $_SESSION['pesan'] = ['tipe' => 'sukses', 'teks' => 'Saran berhasil dihapus.'];
    } else {
        $_SESSION['pesan'] = ['tipe' => 'error', 'teks' => 'Saran tidak bisa dihapus.'];
    }

    header("Location: dashboard.php");
    exit;
}