<?php
require "auth.php";

$petugas = $db->query("SELECT * FROM petugas ORDER BY nama_petugas ASC")->fetchAll(PDO::FETCH_ASSOC);
$siswa = $db->query("SELECT * FROM siswa ORDER BY nama_siswa ASC")->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['simpan'])) {

    $id_siswa = $_POST['id_siswa'];
    $id_petugas = $_POST['id_petugas'];
    $keluhan = $_POST['keluhan'];
    $tindakan = $_POST['tindakan'];
    $tanggal = $_POST['tanggal_kunjungan'];

    if ($id_siswa == "") {
        echo "<script>alert('Pilih siswa dari daftar terlebih dahulu');history.back();</script>";
        exit;
    }

    $stmt = $db->prepare("INSERT INTO kunjungan(id_siswa,id_petugas,keluhan,tindakan,tanggal_kunjungan) VALUES(?,?,?,?,?)");
    $stmt->execute([$id_siswa, $id_petugas, $keluhan, $tindakan, $tanggal]);

    $id_kunjungan = $db->lastInsertId();

    echo "<script>location='resep.php?id_kunjungan=$id_kunjungan'</script>";
}

if (isset($_POST['update'])) {

    $id = $_POST['id_kunjungan'];
    $id_siswa = $_POST['id_siswa'];
    $id_petugas = $_POST['id_petugas'];
    $keluhan = $_POST['keluhan'];
    $tindakan = $_POST['tindakan'];
    $tanggal = $_POST['tanggal_kunjungan'];

    $stmt = $db->prepare("UPDATE kunjungan SET id_siswa=?,id_petugas=?,keluhan=?,tindakan=?,tanggal_kunjungan=? WHERE id_kunjungan=?");
    $stmt->execute([$id_siswa, $id_petugas, $keluhan, $tindakan, $tanggal, $id]);

    echo "<script>location='kunjungan.php'</script>";
}

if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    $stmt = $db->prepare("SELECT id_resep FROM resep WHERE id_kunjungan=?");
    $stmt->execute([$id]);
    $resep = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resep) {

        $id_resep = $resep['id_resep'];

        $stmt = $db->prepare("DELETE FROM detail_resep WHERE id_resep=?");
        $stmt->execute([$id_resep]);

        $stmt = $db->prepare("DELETE FROM resep WHERE id_resep=?");
        $stmt->execute([$id_resep]);
    }

    $stmt = $db->prepare("DELETE FROM kunjungan WHERE id_kunjungan=?");
    $stmt->execute([$id]);

    echo "<script>location='kunjungan.php'</script>";
}

if (isset($_GET['cari'])) {

    $cari = $_GET['cari'];

    $stmt = $db->prepare("
SELECT k.*,s.nama_siswa,p.nama_petugas
FROM kunjungan k
JOIN siswa s ON k.id_siswa=s.id_siswa
JOIN petugas p ON k.id_petugas=p.id_petugas
WHERE s.nama_siswa LIKE ?
ORDER BY k.tanggal_kunjungan DESC
");

    $stmt->execute(["%$cari%"]);
    $kunjungan = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {

    $kunjungan = $db->query("
SELECT k.*,s.nama_siswa,p.nama_petugas
FROM kunjungan k
JOIN siswa s ON k.id_siswa=s.id_siswa
JOIN petugas p ON k.id_petugas=p.id_petugas
ORDER BY k.tanggal_kunjungan DESC
")->fetchAll(PDO::FETCH_ASSOC);

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Data Kunjungan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<h3>Data Kunjungan UKS</h3>

<a href="index.php" class="btn btn-secondary mb-3">Kembali ke Beranda</a>

<div class="card mb-4">
<div class="card-header bg-success text-white">Tambah Kunjungan</div>

<div class="card-body">

<form method="POST">

<div class="mb-3">
<label>Nama Siswa</label>

<input type="text" id="searchSiswa" class="form-control" placeholder="Ketik nama siswa">
<input type="hidden" name="id_siswa" id="id_siswa">

<div id="hasilSiswa" class="list-group"></div>

<div id="tambahBox" style="display:none" class="mt-2">
<span class="text-danger">Belum ada data siswa</span><br>
<a href="siswa.php?return=kunjungan" class="btn btn-sm btn-success mt-1">
Tambah Siswa
</a>
</div>

</div>

<div class="mb-3">
<label>Petugas</label>
<select name="id_petugas" class="form-control" required>

<option value="">Pilih petugas</option>

<?php foreach ($petugas as $p) { ?>

<option value="<?= $p['id_petugas'] ?>">
<?= $p['nama_petugas'] ?>
</option>

<?php } ?>

</select>
</div>

<div class="mb-3">
<label>Keluhan</label>
<textarea name="keluhan" class="form-control" required></textarea>
</div>

<div class="mb-3">
<label>Tindakan</label>
<textarea name="tindakan" class="form-control" required></textarea>
</div>

<div class="mb-3">
<label>Tanggal Kunjungan</label>
<input type="date" name="tanggal_kunjungan" class="form-control" value="<?= date('Y-m-d') ?>" required>
</div>

<button class="btn btn-success" name="simpan">
Simpan & Tambah Resep
</button>

</form>

</div>
</div>

<h4>Riwayat Kunjungan</h4>

<form method="GET" class="mb-3">
<div class="input-group">
<input type="text" name="cari" class="form-control" placeholder="Cari nama siswa...">
<button class="btn btn-primary">Search</button>
<a href="kunjungan.php" class="btn btn-secondary">Reset</a>
</div>
</form>

<table class="table table-bordered">

<thead class="table-dark">

<tr>
<th>No</th>
<th>Tanggal</th>
<th>Siswa</th>
<th>Keluhan</th>
<th>Tindakan</th>
<th>Petugas</th>
<th>Aksi</th>
</tr>

</thead>

<tbody>

<?php
$no = 1;
foreach ($kunjungan as $k) {
?>

<tr>

<td><?= $no++ ?></td>
<td><?= $k['tanggal_kunjungan'] ?></td>
<td><?= $k['nama_siswa'] ?></td>
<td><?= $k['keluhan'] ?></td>
<td><?= $k['tindakan'] ?></td>
<td><?= $k['nama_petugas'] ?></td>

<td>

<button class="btn btn-warning btn-sm"
data-bs-toggle="modal"
data-bs-target="#modalEdit"
onclick="setEdit(
'<?= $k['id_kunjungan'] ?>',
'<?= $k['id_siswa'] ?>',
'<?= $k['id_petugas'] ?>',
'<?= $k['keluhan'] ?>',
'<?= $k['tindakan'] ?>',
'<?= $k['tanggal_kunjungan'] ?>'
)">
Edit
</button>

<button class="btn btn-danger btn-sm"
data-bs-toggle="modal"
data-bs-target="#modalHapus"
onclick="setHapus('<?= $k['id_kunjungan'] ?>')">
Hapus
</button>

<a href="resep.php?id_kunjungan=<?= $k['id_kunjungan'] ?>"
class="btn btn-success btn-sm">
Resep
</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>


<!-- MODAL EDIT -->

<div class="modal fade" id="modalEdit">
<div class="modal-dialog">
<div class="modal-content">

<form method="POST">

<div class="modal-header">
<h5>Edit Kunjungan</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<input type="hidden" name="id_kunjungan" id="edit_id">

<div class="mb-2">
<label>Siswa</label>
<select name="id_siswa" id="edit_siswa" class="form-control">

<?php foreach ($siswa as $s) { ?>

<option value="<?= $s['id_siswa'] ?>">
<?= $s['nama_siswa'] ?>
</option>

<?php } ?>

</select>
</div>

<div class="mb-2">
<label>Petugas</label>
<select name="id_petugas" id="edit_petugas" class="form-control">

<?php foreach ($petugas as $p) { ?>

<option value="<?= $p['id_petugas'] ?>">
<?= $p['nama_petugas'] ?>
</option>

<?php } ?>

</select>
</div>

<div class="mb-2">
<label>Keluhan</label>
<textarea name="keluhan" id="edit_keluhan" class="form-control"></textarea>
</div>

<div class="mb-2">
<label>Tindakan</label>
<textarea name="tindakan" id="edit_tindakan" class="form-control"></textarea>
</div>

<div class="mb-2">
<label>Tanggal</label>
<input type="date" name="tanggal_kunjungan" id="edit_tanggal" class="form-control">
</div>

</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
<button class="btn btn-primary" name="update">Simpan</button>
</div>

</form>

</div>
</div>
</div>


<!-- MODAL HAPUS -->

<div class="modal fade" id="modalHapus">
<div class="modal-dialog">
<div class="modal-content">

<form method="GET">

<div class="modal-header">
<h5>Konfirmasi Hapus</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<input type="hidden" name="hapus" id="hapus_id">
<p>Yakin ingin menghapus data kunjungan ini?</p>
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

function setEdit(id,siswa,petugas,keluhan,tindakan,tanggal){

document.getElementById("edit_id").value=id
document.getElementById("edit_siswa").value=siswa
document.getElementById("edit_petugas").value=petugas
document.getElementById("edit_keluhan").value=keluhan
document.getElementById("edit_tindakan").value=tindakan
document.getElementById("edit_tanggal").value=tanggal

}

function setHapus(id){
document.getElementById("hapus_id").value=id
}

let siswa = <?= json_encode($siswa) ?>;

document.getElementById("searchSiswa").addEventListener("keyup",function(){

let keyword=this.value.toLowerCase()
let hasil=document.getElementById("hasilSiswa")

hasil.innerHTML=""

let ditemukan=false

siswa.forEach(function(s){

if(s.nama_siswa.toLowerCase().includes(keyword)){

ditemukan=true

let item=document.createElement("a")

item.classList.add("list-group-item","list-group-item-action")

item.innerText=s.nama_siswa

item.onclick=function(){

document.getElementById("searchSiswa").value=s.nama_siswa
document.getElementById("id_siswa").value=s.id_siswa
hasil.innerHTML=""
document.getElementById("tambahBox").style.display="none"

}

hasil.appendChild(item)

}

})

if(!ditemukan && keyword!=""){
document.getElementById("tambahBox").style.display="block"
}else{
document.getElementById("tambahBox").style.display="none"
}

})

</script>

</body>
</html>