<?php
require "auth.php";

if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_petugas'];
    $jabatan = $_POST['jabatan'];

    $stmt = $db->prepare("INSERT INTO petugas(nama_petugas,jabatan) VALUES(?,?)");
    $stmt->execute([$nama, $jabatan]);

    echo "<script>location='petugas.php'</script>";
}

if (isset($_POST['update'])) {
    $id = $_POST['id_petugas'];
    $nama = $_POST['nama_petugas'];
    $jabatan = $_POST['jabatan'];

    $stmt = $db->prepare("UPDATE petugas SET nama_petugas=?,jabatan=? WHERE id_petugas=?");
    $stmt->execute([$nama, $jabatan, $id]);

    echo "<script>location='petugas.php'</script>";
}

if (isset($_POST['hapus'])) {
    $id = $_POST['id_petugas'];

    $stmt = $db->prepare("DELETE FROM petugas WHERE id_petugas=?");
    $stmt->execute([$id]);

    echo "<script>location='petugas.php'</script>";
}

if (isset($_GET['cari'])) {
    $cari = $_GET['cari'];
    $stmt = $db->prepare("SELECT * FROM petugas WHERE nama_petugas LIKE ?");
    $stmt->execute(["%$cari%"]);
    $petugas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $petugas = $db->query("SELECT * FROM petugas ORDER BY nama_petugas ASC")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>

<head>

<title>Data Petugas</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="container mt-4">

<h3>Data Petugas UKS</h3>

<div class="d-flex justify-content-between mb-3">

<button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
Tambah Petugas
</button>

<a href="index.php" class="btn btn-secondary">
Kembali ke Beranda
</a>

</div>

<form method="GET" class="mb-3">

<div class="input-group">

<input type="text" name="cari" class="form-control" placeholder="Cari petugas">

<button class="btn btn-primary">Search</button>

<a href="petugas.php" class="btn btn-secondary">Reset</a>

</div>

</form>

<table class="table table-bordered">

<thead class="table-dark">

<tr>
<th>No</th>
<th>Nama Petugas</th>
<th>Jabatan</th>
<th>Aksi</th>
</tr>

</thead>

<tbody>

<?php 
$no = 1;
foreach ($petugas as $row) { 
?>

<tr>

<td><?= $no++ ?></td>

<td><?= $row['nama_petugas'] ?></td>

<td><?= $row['jabatan'] ?></td>

<td>

<button class="btn btn-warning"
data-bs-toggle="modal"
data-bs-target="#modalEdit"
onclick="editData('<?= $row['id_petugas'] ?>','<?= $row['nama_petugas'] ?>','<?= $row['jabatan'] ?>')">
Edit
</button>

<button class="btn btn-danger"
data-bs-toggle="modal"
data-bs-target="#modalHapus"
onclick="hapusData('<?= $row['id_petugas'] ?>','<?= $row['nama_petugas'] ?>')">
Hapus
</button>

</td>

</tr>

<?php } ?>

</tbody>

</table>

<div class="modal fade" id="modalTambah">

<div class="modal-dialog">

<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Tambah Petugas</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form method="POST">

<div class="modal-body">

<div class="mb-3">
<label>Nama Petugas</label>
<input type="text" name="nama_petugas" class="form-control">
</div>

<div class="mb-3">
<label>Jabatan</label>
<input type="text" name="jabatan" class="form-control">
</div>

</div>

<div class="modal-footer">

<button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

<button type="submit" name="tambah" class="btn btn-success">Simpan</button>

</div>

</form>

</div>
</div>
</div>

<div class="modal fade" id="modalEdit">

<div class="modal-dialog">

<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Edit Petugas</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form method="POST">

<div class="modal-body">

<input type="hidden" name="id_petugas" id="edit_id">

<div class="mb-3">
<label>Nama Petugas</label>
<input type="text" name="nama_petugas" id="edit_nama" class="form-control">
</div>

<div class="mb-3">
<label>Jabatan</label>
<input type="text" name="jabatan" id="edit_jabatan" class="form-control">
</div>

</div>

<div class="modal-footer">

<button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

<button type="submit" name="update" class="btn btn-primary">Simpan</button>

</div>

</form>

</div>
</div>
</div>

<div class="modal fade" id="modalHapus">

<div class="modal-dialog">

<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Konfirmasi Hapus</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form method="POST">

<div class="modal-body">

<input type="hidden" name="id_petugas" id="hapus_id">

<p>Yakin ingin menghapus petugas <b id="hapus_nama"></b> ?</p>

</div>

<div class="modal-footer">

<button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

<button type="submit" name="hapus" class="btn btn-danger">Hapus</button>

</div>

</form>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

function editData(id, nama, jabatan) {

document.getElementById("edit_id").value = id;
document.getElementById("edit_nama").value = nama;
document.getElementById("edit_jabatan").value = jabatan;

}

function hapusData(id, nama) {

document.getElementById("hapus_id").value = id;
document.getElementById("hapus_nama").innerText = nama;

}

</script>

</body>

</html>