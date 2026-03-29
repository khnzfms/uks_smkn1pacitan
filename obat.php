<?php
require "auth.php";

/* TAMBAH OBAT */
if (isset($_POST['tambah'])) {

    $nama = $_POST['nama_obat'];
    $stok = $_POST['stok'];
    $ket = $_POST['keterangan'];
    $tgl = date("Y-m-d");

    $stmt = $db->prepare("INSERT INTO obat(nama_obat,stok,keterangan,tgl_update) VALUES(?,?,?,?)");
    $stmt->execute([$nama, $stok, $ket, $tgl]);

    echo "<script>location='obat.php'</script>";
}

/* UPDATE OBAT */
if (isset($_POST['update'])) {

    $id = $_POST['id_obat'];
    $nama = $_POST['nama_obat'];
    $stok = $_POST['stok'];
    $ket = $_POST['keterangan'];
    $tgl = date("Y-m-d");

    $stmt = $db->prepare("UPDATE obat SET nama_obat=?,stok=?,keterangan=?,tgl_update=? WHERE id_obat=?");
    $stmt->execute([$nama, $stok, $ket, $tgl, $id]);

    echo "<script>location='obat.php'</script>";
}

/* HAPUS */
if (isset($_POST['hapus'])) {

    $id = $_POST['id_obat'];

    $stmt = $db->prepare("DELETE FROM obat WHERE id_obat=?");
    $stmt->execute([$id]);

    echo "<script>location='obat.php'</script>";
}

/* SEARCH */
if (isset($_GET['cari'])) {

    $cari = $_GET['cari'];

    $stmt = $db->prepare("SELECT * FROM obat WHERE nama_obat LIKE ?");
    $stmt->execute(["%$cari%"]);
    $obat = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {

    $obat = $db->query("SELECT * FROM obat ORDER BY nama_obat ASC")->fetchAll(PDO::FETCH_ASSOC);

}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Data Obat</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="container mt-4">

    <h3>Data Obat</h3>

    <a href="index.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>

    <!-- FORM TAMBAH OBAT -->

    <div class="card mb-4">

        <div class="card-header bg-success text-white">
            Tambah Obat
        </div>

        <div class="card-body">

            <form method="POST">

                <div class="row">

                    <div class="col-md-4">
                        <label>Nama Obat</label>
                        <input type="text" name="nama_obat" class="form-control" required>
                    </div>

                    <div class="col-md-2">
                        <label>Stok</label>
                        <input type="number" name="stok" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label>Keterangan</label>
                        <input type="text" name="keterangan" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="submit" name="tambah" class="btn btn-success w-100">Tambah</button>
                    </div>

                </div>

            </form>

        </div>
    </div>

    <!-- SEARCH -->

    <form method="GET" class="mb-3">

        <div class="input-group">

            <input type="text" name="cari" class="form-control" placeholder="Cari nama obat">

            <button class="btn btn-primary">Search</button>

            <a href="obat.php" class="btn btn-secondary">Reset</a>

        </div>

    </form>

    <!-- TABEL OBAT -->

    <table class="table table-bordered">

        <thead class="table-dark">

            <tr>
                <th>No</th>
                <th>Nama Obat</th>
                <th>Stok</th>
                <th>Keterangan</th>
                <th>Tanggal Update</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

            <?php $no = 1;
            foreach ($obat as $row) { ?>

                <tr>

                    <td><?= $no++ ?></td>
                    <td><?= $row['nama_obat'] ?></td>
                    <td><?= $row['stok'] ?></td>
                    <td><?= $row['keterangan'] ?></td>
                    <td><?= $row['tgl_update'] ?></td>

                    <td>

                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit"
                            onclick="editData('<?= $row['id_obat'] ?>','<?= $row['nama_obat'] ?>','<?= $row['stok'] ?>','<?= $row['keterangan'] ?>')">
                            Edit
                        </button>

                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapus"
                            onclick="hapusData('<?= $row['id_obat'] ?>','<?= $row['nama_obat'] ?>')">
                            Hapus
                        </button>

                    </td>

                </tr>

            <?php } ?>

        </tbody>

    </table>

    <!-- MODAL EDIT -->

    <div class="modal fade" id="modalEdit">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Edit Obat</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST">

                    <div class="modal-body">

                        <input type="hidden" name="id_obat" id="edit_id">

                        <div class="mb-3">
                            <label>Nama Obat</label>
                            <input type="text" name="nama_obat" id="edit_nama" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Stok</label>
                            <input type="number" name="stok" id="edit_stok" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Keterangan</label>
                            <input type="text" name="keterangan" id="edit_ket" class="form-control">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" name="update" class="btn btn-primary">Simpan</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- MODAL HAPUS -->

    <div class="modal fade" id="modalHapus">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Hapus Obat</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST">

                    <div class="modal-body">

                        <input type="hidden" name="id_obat" id="hapus_id">

                        <p>Yakin ingin menghapus <b id="hapus_nama"></b> ?</p>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" name="hapus" class="btn btn-danger">Hapus</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>

        function editData(id, nama, stok, ket) {

            document.getElementById("edit_id").value = id;
            document.getElementById("edit_nama").value = nama;
            document.getElementById("edit_stok").value = stok;
            document.getElementById("edit_ket").value = ket;

        }

        function hapusData(id, nama) {

            document.getElementById("hapus_id").value = id;
            document.getElementById("hapus_nama").innerText = nama;

        }

    </script>

</body>

</html>