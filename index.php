<?php
require "auth.php";

// statistik
$jml_siswa = $db->query("SELECT COUNT(*) FROM siswa")->fetchColumn();
$jml_petugas = $db->query("SELECT COUNT(*) FROM petugas")->fetchColumn();
$jml_obat = $db->query("SELECT COUNT(*) FROM obat")->fetchColumn();
$jml_kunjungan = $db->query("SELECT COUNT(*) FROM kunjungan")->fetchColumn();

// kunjungan terbaru
$kunjungan_terbaru = $db->query("
SELECT k.*, s.nama_siswa
FROM kunjungan k
JOIN siswa s ON k.id_siswa=s.id_siswa
ORDER BY k.id_kunjungan DESC
LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>

<title>Dashboard UKS</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>

body{
background:#f1f8f4;
}

.header{
background:#2e7d32;
color:white;
padding:15px 20px;
border-radius:10px;
}

.stat-box{
background:#e8f5e9;
padding:15px;
border-radius:10px;
text-align:center;
border-left:5px solid #2e7d32;
}

.menu-box{
background:white;
padding:20px;
border-radius:12px;
text-align:center;
transition:0.2s;
border:1px solid #e0e0e0;
}

.menu-box:hover{
transform:translateY(-4px);
box-shadow:0 6px 15px rgba(0,0,0,0.1);
border-color:#2e7d32;
}

</style>

</head>

<body>

<div class="container mt-4">

<!-- HEADER -->
<div class="header mb-4 d-flex justify-content-between align-items-center">

<div>
<h5 class="mb-0">Sistem Informasi UKS</h5>
<medium>SMK Negeri 1 Pacitan</medium>
</div>

<div class="d-flex align-items-center">
<small class="me-2">Halo, <?= $_SESSION['nama'] ?> 👋</small>

<button class="btn btn-light btn-sm"
data-bs-toggle="modal"
data-bs-target="#modalLogout">
Logout
</button>
</div>

</div>

<!-- STATISTIK -->
<div class="row mb-4">

<div class="col-md-3">
<div class="stat-box">
<i class="fa-solid fa-user-graduate"></i>
<h5><?= $jml_siswa ?></h5>
<small>Total Siswa</small>
</div>
</div>

<div class="col-md-3">
<div class="stat-box">
<i class="fa-solid fa-user-nurse"></i>
<h5><?= $jml_petugas ?></h5>
<small>Total Petugas</small>
</div>
</div>

<div class="col-md-3">
<div class="stat-box">
<i class="fa-solid fa-pills"></i>
<h5><?= $jml_obat ?></h5>
<small>Total Obat</small>
</div>
</div>

<div class="col-md-3">
<div class="stat-box">
<i class="fa-solid fa-notes-medical"></i>
<h5><?= $jml_kunjungan ?></h5>
<small>Total Kunjungan</small>
</div>
</div>

</div>

<!-- MENU -->
<div class="row g-3 mb-4">

<div class="col-md-3">
<a href="siswa.php" class="text-decoration-none text-dark">
<div class="menu-box">
<i class="fa-solid fa-user-graduate"></i>
<h6>Data Siswa</h6>
</div>
</a>
</div>

<div class="col-md-3">
<a href="petugas.php" class="text-decoration-none text-dark">
<div class="menu-box">
<i class="fa-solid fa-user-nurse"></i>
<h6>Data Petugas</h6>
</div>
</a>
</div>

<div class="col-md-3">
<a href="obat.php" class="text-decoration-none text-dark">
<div class="menu-box">
<i class="fa-solid fa-pills"></i>
<h6>Data Obat</h6>
</div>
</a>
</div>

<div class="col-md-3">
<a href="kunjungan.php" class="text-decoration-none text-dark">
<div class="menu-box">
<i class="fa-solid fa-notes-medical"></i>
<h6>Kunjungan</h6>
</div>
</a>
</div>

</div>

<!-- KUNJUNGAN -->
<div class="card">
<div class="card-header bg-success text-white">
Kunjungan Terbaru
</div>

<div class="card-body p-0">
<table class="table table-bordered mb-0">

<thead class="table-light">
<tr>
<th>No</th>
<th>Siswa</th>
<th>Tanggal</th>
<th>Keluhan</th>
</tr>
</thead>

<tbody>
<?php $no=1; foreach($kunjungan_terbaru as $k){ ?>
<tr>
<td><?= $no++ ?></td>
<td><?= $k['nama_siswa'] ?></td>
<td><?= $k['tanggal_kunjungan'] ?></td>
<td><?= $k['keluhan'] ?></td>
</tr>
<?php } ?>
</tbody>

</table>
</div>
</div>

<!-- MODAL LOGOUT -->
<div class="modal fade" id="modalLogout">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5>Konfirmasi Logout</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
Yakin ingin logout dari sistem?
</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
<a href="logout.php" class="btn btn-danger">Logout</a>
</div>

</div>
</div>
</div>

<div class="text-center mt-4">
<small class="text-muted">
© <?= date("Y") ?> Sistem Informasi UKS | SMKN 1 Pacitan
</small>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>