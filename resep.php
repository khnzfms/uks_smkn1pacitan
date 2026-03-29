<?php
require "auth.php";

if(!isset($_GET['id_kunjungan'])){
die("ID kunjungan tidak ditemukan");
}

$id_kunjungan = $_GET['id_kunjungan'];

$kunjungan = $db->query("
SELECT k.*, s.nama_siswa
FROM kunjungan k
JOIN siswa s ON k.id_siswa=s.id_siswa
WHERE k.id_kunjungan='$id_kunjungan'
")->fetch(PDO::FETCH_ASSOC);

$obat = $db->query("SELECT * FROM obat ORDER BY nama_obat ASC")->fetchAll(PDO::FETCH_ASSOC);

$cek = $db->prepare("SELECT * FROM resep WHERE id_kunjungan=?");
$cek->execute([$id_kunjungan]);
$resep = $cek->fetch(PDO::FETCH_ASSOC);

if(!$resep){

$stmt = $db->prepare("INSERT INTO resep(id_kunjungan,tanggal_resep) VALUES(?,NOW())");
$stmt->execute([$id_kunjungan]);

$id_resep = $db->lastInsertId();

}else{

$id_resep = $resep['id_resep'];

}

if(isset($_POST['tambah_obat'])){

$id_obat = $_POST['id_obat'];
$jumlah = $_POST['jumlah'];
$aturan = $_POST['aturan'];

$stmt = $db->prepare("
INSERT INTO detail_resep(id_resep,id_obat,jumlah,aturan_pakai)
VALUES(?,?,?,?)
");
$stmt->execute([$id_resep,$id_obat,$jumlah,$aturan]);

$stmt = $db->prepare("UPDATE obat SET stok = stok - ? WHERE id_obat=?");
$stmt->execute([$jumlah,$id_obat]);

echo "<script>location='resep.php?id_kunjungan=$id_kunjungan'</script>";
}

if(isset($_GET['hapus'])){

$id = $_GET['hapus'];

$stmt = $db->prepare("DELETE FROM detail_resep WHERE id_detail=?");
$stmt->execute([$id]);

echo "<script>location='resep.php?id_kunjungan=$id_kunjungan'</script>";
}

$detail = $db->query("
SELECT d.*, o.nama_obat
FROM detail_resep d
JOIN obat o ON d.id_obat=o.id_obat
WHERE d.id_resep='$id_resep'
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>

<title>Resep</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">

<h3>Resep Kunjungan</h3>

<a href="kunjungan.php" class="btn btn-secondary">
Kembali ke Kunjungan
</a>

</div>

<div class="alert alert-info">
<b>Nama Siswa:</b> <?= $kunjungan['nama_siswa'] ?><br>
<b>Tanggal:</b> <?= $kunjungan['tanggal_kunjungan'] ?>
</div>

<div class="card mb-4">

<div class="card-header bg-success text-white">
Tambah Obat
</div>

<div class="card-body">

<form method="POST">

<div class="mb-3">

<label>Obat</label>

<select name="id_obat" class="form-control" required>

<option value="">Pilih obat</option>

<?php foreach($obat as $o){ ?>

<option value="<?= $o['id_obat'] ?>">
<?= $o['nama_obat'] ?> (stok <?= $o['stok'] ?>)
</option>

<?php } ?>

</select>

</div>

<div class="mb-3">

<label>Jumlah</label>

<input type="number" name="jumlah" class="form-control" required>

</div>

<div class="mb-3">

<label>Aturan Pakai</label>

<input type="text" name="aturan" class="form-control" placeholder="Contoh: 3x1 setelah makan">

</div>

<button class="btn btn-success" name="tambah_obat">
Tambah Obat
</button>

</form>

</div>

</div>

<h4>Daftar Obat</h4>

<table class="table table-bordered">

<thead class="table-dark">

<tr>
<th>No</th>
<th>Obat</th>
<th>Jumlah</th>
<th>Aturan</th>
<th>Aksi</th>
</tr>

</thead>

<tbody>

<?php
$no=1;
foreach($detail as $d){
?>

<tr>

<td><?= $no++ ?></td>
<td><?= $d['nama_obat'] ?></td>
<td><?= $d['jumlah'] ?></td>
<td><?= $d['aturan_pakai'] ?></td>

<td>

<button class="btn btn-danger btn-sm"
data-bs-toggle="modal"
data-bs-target="#modalHapus"
onclick="setHapus('<?= $d['id_detail'] ?>')">
Hapus
</button>

</td>

</tr>

<?php } ?>

</tbody>

</table>

<!-- MODAL HAPUS -->

<div class="modal fade" id="modalHapus">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="GET">

                <div class="modal-body">

                    <input type="hidden" name="hapus" id="hapus_id">

                    <input type="hidden" name="id_kunjungan" value="<?= $id_kunjungan ?>">

                    <p>Yakin ingin menghapus obat ini?</p>

                </div>

                <div class="modal-footer">

                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

                    <button class="btn btn-danger">Hapus</button>

                </div>

            </form>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

function setHapus(id){
    document.getElementById("hapus_id").value = id
}

</script>

</body>
</html>