<?php
session_start();
require 'koneksi.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data user
$stmt = $pdo->prepare("SELECT nama, kelas FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil semua kategori
$kategori = $pdo->query("SELECT id, nama FROM kategori ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);

// Ambil semua saran milik user ini
$stmt = $pdo->prepare("
    SELECT p.id, p.judul, p.isi, p.status, p.created_at, k.nama AS kategori
    FROM pengaduan p
    LEFT JOIN kategori k ON k.id = p.kategori_id
    WHERE p.user_id = ?
    ORDER BY p.created_at DESC
");
$stmt->execute([$user_id]);
$saranList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ringkasan
$total    = count($saranList);
$menunggu = count(array_filter($saranList, fn($s) => $s['status'] === 'menunggu'));
$diproses = count(array_filter($saranList, fn($s) => $s['status'] === 'diproses'));
$selesai  = count(array_filter($saranList, fn($s) => $s['status'] === 'selesai'));

// Ambil detail jika ada ?lihat=id
$detail = null;
if (isset($_GET['lihat'])) {
    $stmt = $pdo->prepare("
        SELECT p.*, k.nama AS kategori
        FROM pengaduan p
        LEFT JOIN kategori k ON k.id = p.kategori_id
        WHERE p.id = ? AND p.user_id = ?
    ");
    $stmt->execute([(int)$_GET['lihat'], $user_id]);
    $detail = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Ambil pesan sesi
$pesan = $_SESSION['pesan'] ?? null;
unset($_SESSION['pesan']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Siswa</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: sans-serif;
      font-size: 15px;
      background: #f4f4f4;
      color: #222;
    }

    /* HEADER */
    header {
      background: #1a56db;
      color: white;
      padding: 14px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header h1 { font-size: 17px; }

    /* LAYOUT */
    main {
      max-width: 820px;
      margin: 28px auto;
      padding: 0 16px;
    }

    /* CARD */
    .card {
      background: white;
      border: 1px solid #ddd;
      border-radius: 6px;
      padding: 20px;
      margin-bottom: 20px;
    }

    .card h2 {
      font-size: 15px;
      margin-bottom: 14px;
      padding-bottom: 8px;
      border-bottom: 1px solid #eee;
    }

    /* FORM */
    label {
      display: block;
      font-size: 13px;
      color: #555;
      margin-top: 12px;
      margin-bottom: 4px;
    }

    input[type="text"], select, textarea {
      width: 100%;
      padding: 8px 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
      font-family: sans-serif;
    }

    textarea { resize: vertical; min-height: 80px; }

    /* TOMBOL */
    button, .tombol {
      display: inline-block;
      cursor: pointer;
      border: none;
      border-radius: 4px;
      padding: 8px 16px;
      font-size: 13px;
      text-decoration: none;
    }

    .btn-primary { background: #1a56db; color: white; margin-top: 14px; }
    .btn-danger  { background: #e53e3e; color: white; padding: 5px 10px; font-size: 12px; }
    .btn-info    { background: #0891b2; color: white; padding: 5px 10px; font-size: 12px; }
    .btn-putih   { background: white; color: #1a56db; border: 1px solid #1a56db; font-size: 12px; padding: 5px 12px; }
    .btn-logout  { background: rgba(255,255,255,.2); color: white; border: 1px solid rgba(255,255,255,.4); font-size: 12px; padding: 5px 12px; }

    /* TABEL */
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th { background: #f0f4ff; text-align: left; padding: 9px 10px; border-bottom: 2px solid #ddd; }
    td { padding: 9px 10px; border-bottom: 1px solid #eee; vertical-align: middle; }
    tr:last-child td { border-bottom: none; }

    /* BADGE STATUS */
    .status {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 99px;
      font-size: 11px;
      font-weight: bold;
    }
    .status.menunggu { background: #fef3c7; color: #92400e; }
    .status.diproses { background: #dbeafe; color: #1e40af; }
    .status.selesai  { background: #d1fae5; color: #065f46; }
    .status.ditolak  { background: #fee2e2; color: #991b1b; }

    /* NOTIFIKASI */
    .notif {
      padding: 10px 14px;
      border-radius: 4px;
      margin-bottom: 14px;
      font-size: 13px;
    }
    .notif.sukses { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
    .notif.error  { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

    /* RINGKASAN */
    .ringkasan { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; }
    .kotak { flex: 1; min-width: 110px; background: white; border: 1px solid #ddd; border-radius: 6px; padding: 14px; text-align: center; }
    .kotak .angka { font-size: 28px; font-weight: bold; color: #1a56db; }
    .kotak .label { font-size: 12px; color: #666; margin-top: 2px; }

    /* DETAIL BOX */
    .detail-box {
      background: #f8faff;
      border: 1px solid #c7d8ff;
      border-radius: 6px;
      padding: 16px;
      margin-bottom: 20px;
    }
    .detail-box h3 { font-size: 15px; margin-bottom: 6px; }
    .detail-box .meta { font-size: 12px; color: #888; margin-bottom: 10px; }
    .detail-box p { font-size: 13px; color: #444; line-height: 1.7; }

    .kosong { text-align: center; padding: 24px; color: #999; font-size: 13px; }
  </style>
</head>
<body>

<header>
  <h1>📢 Pengaduan Sekolah</h1>
  <div style="display:flex;align-items:center;gap:12px;">
    <span style="font-size:13px;opacity:.85;">
      <?= htmlspecialchars($user['nama']) ?> · <?= htmlspecialchars($user['kelas']) ?>
    </span>
    <a href="logout.php" class="tombol btn-logout">Keluar</a>
  </div>
</header>

<main>

  <!-- RINGKASAN -->
  <div class="ringkasan">
    <div class="kotak">
      <div class="angka"><?= $total ?></div>
      <div class="label">Total Saran</div>
    </div>
    <div class="kotak">
      <div class="angka" style="color:#d97706"><?= $menunggu ?></div>
      <div class="label">Menunggu</div>
    </div>
    <div class="kotak">
      <div class="angka" style="color:#1a56db"><?= $diproses ?></div>
      <div class="label">Diproses</div>
    </div>
    <div class="kotak">
      <div class="angka" style="color:#059669"><?= $selesai ?></div>
      <div class="label">Selesai</div>
    </div>
  </div>

  <!-- DETAIL SARAN (muncul kalau ada ?lihat=id) -->
  <?php if ($detail): ?>
  <div class="detail-box">
    <h3><?= htmlspecialchars($detail['judul']) ?></h3>
    <div class="meta">
      <?= htmlspecialchars($detail['kategori']) ?> ·
      <?= date('d M Y', strtotime($detail['created_at'])) ?> ·
      <span class="status <?= $detail['status'] ?>"><?= $detail['status'] ?></span>
    </div>
    <p><?= nl2br(htmlspecialchars($detail['isi'])) ?></p>
    <a href="dashboard.php" class="tombol btn-putih" style="margin-top:12px;">✕ Tutup</a>
  </div>
  <?php endif; ?>

  <!-- NOTIFIKASI -->
  <?php if ($pesan): ?>
  <div class="notif <?= $pesan['tipe'] ?>"><?= htmlspecialchars($pesan['teks']) ?></div>
  <?php endif; ?>

  <!-- FORM AJUKAN SARAN -->
  <div class="card">
    <h2>✏️ Ajukan Saran / Pengaduan</h2>
    <form action="proses.php" method="POST">
      <label for="kategori_id">Kategori</label>
      <select name="kategori_id" id="kategori_id" required>
        <option value="">-- Pilih Kategori --</option>
        <?php foreach ($kategori as $k): ?>
        <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama']) ?></option>
        <?php endforeach; ?>
      </select>

      <label for="judul">Judul</label>
      <input type="text" name="judul" id="judul" placeholder="Singkat & jelas..." required>

      <label for="isi">Isi Saran / Pengaduan</label>
      <textarea name="isi" id="isi" placeholder="Jelaskan masalah atau saranmu..." required></textarea>

      <button type="submit" name="ajukan" class="tombol btn-primary">Kirim Saran</button>
    </form>
  </div>

  <!-- DAFTAR SARAN -->
  <div class="card">
    <h2>📋 Saran Saya</h2>
    <?php if (empty($saranList)): ?>
      <div class="kosong">Belum ada saran yang diajukan.</div>
    <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Judul</th>
          <th>Kategori</th>
          <th>Status</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($saranList as $i => $s): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><?= htmlspecialchars($s['judul']) ?></td>
          <td><?= htmlspecialchars($s['kategori'] ?? '-') ?></td>
          <td><span class="status <?= $s['status'] ?>"><?= $s['status'] ?></span></td>
          <td><?= date('d M Y', strtotime($s['created_at'])) ?></td>
          <td style="display:flex;gap:6px;align-items:center;">
            <a href="dashboard.php?lihat=<?= $s['id'] ?>" class="tombol btn-info">Lihat</a>
            <?php if ($s['status'] === 'menunggu'): ?>
            <a href="proses.php?hapus=<?= $s['id'] ?>"
               class="tombol btn-danger"
               onclick="return confirm('Yakin ingin menghapus saran ini?')">Hapus</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>

</main>
</body>
</html>