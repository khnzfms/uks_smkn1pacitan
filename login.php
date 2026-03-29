<?php
require "config.php";

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM petugas WHERE nama_petugas=?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user){

        if(password_verify($password, $user['password'])){

            $_SESSION['login'] = true;
            $_SESSION['nama'] = $user['nama_petugas'];

            echo "<script>location='index.php'</script>";

        }else{
            $error = "Password salah!";
        }

    }else{
        $error = "User tidak ditemukan!";
    }

}
?>

<!DOCTYPE html>
<html>

<head>

<title>Login UKS</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>

body{
height:100vh;
margin:0;
display:flex;
font-family:sans-serif;
}

/* KIRI */
.left{
width:50%;
background:linear-gradient(135deg,#2e7d32,#66bb6a);
color:white;
display:flex;
flex-direction:column;
justify-content:center;
align-items:center;
padding:40px;
text-align:center;
}

.left h2{
font-weight:bold;
}

/* KANAN */
.right{
width:50%;
display:flex;
justify-content:center;
align-items:center;
background:#f4f9f6;
}

.login-box{
background:white;
padding:30px;
border-radius:15px;
width:350px;
box-shadow:0 10px 25px rgba(0,0,0,0.15);
}

.login-box h4{
text-align:center;
margin-bottom:20px;
color:#2e7d32;
}

.form-control{
border-radius:10px;
}

.btn-login{
background:#2e7d32;
color:white;
border-radius:10px;
}

.btn-login:hover{
background:#1b5e20;
}

.input-group{
position:relative;
}

.icon{
position:absolute;
top:10px;
left:10px;
color:#888;
}

.input-group input{
padding-left:35px;
}

.toggle-pass{
position:absolute;
right:10px;
top:10px;
cursor:pointer;
color:#888;
}

</style>

</head>

<body>

<!-- KIRI -->
<div class="left">

<h1><i class="fa-solid fa-heart-pulse"></i></h1>

<h2>Selamat Datang</h2>
<h4>UKS SMK Negeri 1 Pacitan</h4>

<p>
Sistem informasi untuk mencatat kunjungan siswa, pengelolaan obat,  
dan pelayanan kesehatan sekolah secara digital.
</p>

</div>

<!-- KANAN -->
<div class="right">

<div class="login-box">

<h4>Login</h4>

<?php if(isset($error)){ ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php } ?>

<form method="POST">

<div class="mb-3 input-group">
<i class="fa-solid fa-user icon"></i>
<input type="text" name="username" class="form-control" placeholder="Username" required>
</div>

<div class="mb-3 input-group">
<i class="fa-solid fa-lock icon"></i>
<input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
<i class="fa-solid fa-eye toggle-pass" onclick="togglePassword()"></i>
</div>

<button class="btn btn-login w-100" name="login">
Login
</button>

</form>

</div>

</div>

<script>

function togglePassword(){
let pass = document.getElementById("password");

if(pass.type === "password"){
    pass.type = "text";
}else{
    pass.type = "password";
}
}

</script>

</body>
</html>