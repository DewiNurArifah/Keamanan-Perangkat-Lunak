<?php

// reset-password.php

// Inisialisasi session
session_start();
 
// Cek apakah user sudah login, jika tidak alihkan ke halaman login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
 
// Panggil config database
require_once "config.php";
 
// Definisi variabel dengan nilai kosong
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
 
// Proses data jika user submit form
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validasi password
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Silahkan masukan password baru anda.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Silahkan isi password baru dengan total maksimal 6 karakter";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validasi konfirmasi password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Silahkan konfirmasi password baru anda.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password tidak sesuai.";
        }
    }
        
    // Cek input jika tidak ada error
    if(empty($new_password_err) && empty($confirm_password_err)){

        // Koneksikan dengan database
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){

            // Ikatkan variabel kedalam statement sebagai parameter 
            mysqli_stmt_bind_param($stmt, "si", $param_password, $param_id);
            
            // Set parameter
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            
            // Lakukan eksekusi
            if(mysqli_stmt_execute($stmt)){

                // Password berhasil terupdate, hapus session dan alihkan ke halaman login
                session_destroy();
                header("location: login.php");
                exit();
            } else{
                echo "Sepertinya error, silahkan coba lagi nanti.";
            }

            // Tutup statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Tutup koneksi
    mysqli_close($conn);
}
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; background: #efefef;}
        .wrapper{ width: 380px; padding: 20px; margin:0 auto; display: block; margin-top: 60px; background: #fff;}
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Reset Password</h2>
        <p>Silahkan isi form dibawah untuk menganti password lama dengan yang baru.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
            <div class="form-group">
                <label>Password baru</label>
                <input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>">
                <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Konfirmasi password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-link ml-2" href="home2.php">Cancel</a>
            </div>
        </form>
    </div>    
</body>
</html> 