<?php
require "auth.php";

$return = isset($_GET['return']) ? $_GET['return'] : '';

if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_siswa'];
    $kelas = $_POST['kelas'];
    $jk = $_POST['jenis_kelamin'];
    $tgl = $_POST['tanggal_lahir'];

    $stmt = $db->prepare("INSERT INTO siswa(nama_siswa,kelas,jenis_kelamin,tanggal_lahir) VALUES(?,?,?,?)");
    $stmt->execute([$nama,$kelas,$jk,$tgl]);

    if($return=="kunjungan"){
        echo "<script>location='kunjungan.php'</script>";
    }else{
        echo "<script>location='siswa.php'</script>";
    }
}

if (isset($_POST['update'])) {
    $id = $_POST['id_siswa'];
    $nama = $_POST['nama_siswa'];
    $kelas = $_POST['kelas'];
    $jk = $_POST['jenis_kelamin'];
    $tgl = $_POST['tanggal_lahir'];

    $stmt = $db->prepare("UPDATE siswa SET nama_siswa=?,kelas=?,jenis_kelamin=?,tanggal_lahir=? WHERE id_siswa=?");
    $stmt->execute([$nama,$kelas,$jk,$tgl,$id]);

    echo "<script>location='siswa.php'</script>";
}

if (isset($_POST['hapus'])) {
    $id = $_POST['id_siswa'];

    $stmt = $db->prepare("DELETE FROM siswa WHERE id_siswa=?");
    $stmt->execute([$id]);

    echo "<script>location='siswa.php'</script>";
}

if (isset($_GET['cari'])) {

    $cari = $_GET['cari'];

    $stmt = $db->prepare("SELECT * FROM siswa WHERE nama_siswa LIKE ?");
    $stmt->execute(["%$cari%"]);
    $siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {

    $siswa = $db->query("SELECT * FROM siswa ORDER BY nama_siswa ASC")->fetchAll(PDO::FETCH_ASSOC);

}
?>

<!DOCTYPE html>
<html>

<head>

<title>Data Siswa</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="container mt-4">

<h3>Data Siswa</h3>

<?php if($return=="kunjungan"){ ?>

<a href="kunjungan.php" class="btn btn-secondary mb-3">Kembali ke Kunjungan</a>

<?php } else { ?>

<a href="index.php" class="btn btn-secondary mb-3">Kembali ke Beranda</a>

<?php } ?>

<div class="card mb-4">

<div class="card-header bg-success text-white">
Tambah Siswa
</div>

<div class="card-body">

<form method="POST">

<div class="row">

<div class="col-md-3">
<label>Nama Siswa</label>
<input type="text" name="nama_siswa" class="form-control" required>
</div>

<div class="col-md-2">
<label>Kelas</label>
<input type="text" name="kelas" class="form-control" placeholder="Contoh: XI RPL 1" required>
</div>

<div class="col-md-3">
<label>Jenis Kelamin</label>
<select name="jenis_kelamin" class="form-control" required>
<option value="">Pilih</option>
<option>Laki-laki</option>
<option>Perempuan</option>
</select>
</div>

<div class="col-md-2">
<label>Tanggal Lahir</label>
<input type="date" name="tanggal_lahir" class="form-control" required>
</div>

<div class="col-md-2">
<label>&nbsp;</label>
<button type="submit" name="tambah" class="btn btn-success w-100">
Tambah
</button>
</div>

</div>

</form>

</div>
</div>

<form method="GET" class="mb-3">

<div class="input-group">

<input type="text" name="cari" class="form-control" placeholder="Cari nama siswa">

<button class="btn btn-primary">Search</button>

<a href="siswa.php" class="btn btn-secondary">Reset</a>

</div>

</form>

<table class="table table-bordered">

<thead class="table-dark">

<tr>
<th>No</th>
<th>Nama</th>
<th>Kelas</th>
<th>Jenis Kelamin</th>
<th>Tanggal Lahir</th>
<th>Umur</th>
<th>Aksi</th>
</tr>

</thead>

<tbody>

<?php
$no = 1;

foreach ($siswa as $row) {

$umur = date("Y") - date("Y", strtotime($row['tanggal_lahir']));
?>

<tr>

<td><?= $no++ ?></td>
<td><?= $row['nama_siswa'] ?></td>
<td><?= $row['kelas'] ?></td>
<td><?= $row['jenis_kelamin'] ?></td>
<td><?= $row['tanggal_lahir'] ?></td>
<td><?= $umur ?> tahun</td>

<td>

<button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit"
onclick="editData('<?= $row['id_siswa'] ?>','<?= $row['nama_siswa'] ?>','<?= $row['kelas'] ?>','<?= $row['jenis_kelamin'] ?>','<?= $row['tanggal_lahir'] ?>')">
Edit
</button>

<button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapus"
onclick="hapusData('<?= $row['id_siswa'] ?>','<?= $row['nama_siswa'] ?>')">
Hapus
</button>

</td>

</tr>

<?php } ?>

</tbody>

</table>

<div class="modal fade" id="modalEdit">

<div class="modal-dialog">

<div class="modal-content">

<div class="modal-header">
<h5>Edit Siswa</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form method="POST">

<div class="modal-body">

<input type="hidden" name="id_siswa" id="edit_id">

<div class="mb-3">
<label>Nama</label>
<input type="text" name="nama_siswa" id="edit_nama" class="form-control">
</div>

<div class="mb-3">
<label>Kelas</label>
<input type="text" name="kelas" id="edit_kelas" class="form-control">
</div>

<div class="mb-3">
<label>Jenis Kelamin</label>
<select name="jenis_kelamin" id="edit_jk" class="form-control">
<option>Laki-laki</option>
<option>Perempuan</option>
</select>
</div>

<div class="mb-3">
<label>Tanggal Lahir</label>
<input type="date" name="tanggal_lahir" id="edit_tgl" class="form-control">
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
<h5>Konfirmasi Hapus</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form method="POST">

<div class="modal-body">

<input type="hidden" name="id_siswa" id="hapus_id">

<p>Yakin ingin menghapus siswa <b id="hapus_nama"></b> ?</p>

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

function editData(id,nama,kelas,jk,tgl){

document.getElementById("edit_id").value=id
document.getElementById("edit_nama").value=nama
document.getElementById("edit_kelas").value=kelas
document.getElementById("edit_jk").value=jk
document.getElementById("edit_tgl").value=tgl

}

function hapusData(id,nama){

document.getElementById("hapus_id").value=id
document.getElementById("hapus_nama").innerText=nama

}

</script>

</body>
</html>