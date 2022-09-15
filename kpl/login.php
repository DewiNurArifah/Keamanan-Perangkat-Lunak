<?php

// login.php

// Inisialisasi session
session_start();
 
// Cek jika user sudah login, jika sudah maka akan dialihkan ke home
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: home2.php");
    exit;
}
 
// Tambahkan config database
require_once "config.php";
 
// Mendefinisikan masing-masing variabel dengan nilai kosong
$name = $password = "";
$name_err = $password_err = $login_err = "";
 
// Proses data jika form sudah disubmit
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Cek jika nama kosong
    if(empty(trim($_POST["name"]))){
        $name_err = "Silahkan isi nama anda.";
    } else{
        $name = trim($_POST["name"]);
    }
    
    // Cek jika password kosong
    if(empty(trim($_POST["password"]))){
        $password_err = "Silahkan isi password anda.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validasi nama dan password
    if(empty($name_err) && empty($password_err)){

        // Hubungkan dengan database
        $sql = "SELECT id, name, password FROM users WHERE name = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){

            // Ikatkan variabel kedalam statement sebagai parameter 
            mysqli_stmt_bind_param($stmt, "s", $param_name);
            
            // Set parameter
            $param_name = $name;
            
            // Lakukan eksekusi
            if(mysqli_stmt_execute($stmt)){

                // Simpan hasil
                mysqli_stmt_store_result($stmt);
                
                // Cek jika nama sudah digunakan, jika iya lakukan validasi selanjutnya
                if(mysqli_stmt_num_rows($stmt) == 1){   

                    // Ikatkan hasil variabel
                    mysqli_stmt_bind_result($stmt, $id, $name, $hashed_password);

                    if(mysqli_stmt_fetch($stmt)){
                        
                        // Lakukan validasi password
                        if(password_verify($password, $hashed_password)){

                            // Jika password berhasil, mulai kedalam session
                            session_start();
                            
                            // Masukan data kedalam varibel session
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["name"] = $name;                            
                            
                            // Alihkan user ke halaman home
                            header("location: home2.php");
                        } else{
                            // Menampilkan error jika password tidak sesuai
                            $login_err = "Password tidak sesuai.";
                        }
                    }
                } else {
                    // Menampilkan error jika nama atau password tidak sesuai
                    $login_err = "Nama atau password tidak sesuai.";
                }
            } else {
                echo "Sepertinya error, silahkan coba lagi nanti!";
            }

            // Tutup statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Tutup koneksi
    mysqli_close($conn);
}
?> 
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>login demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="style/style_login.css">
  </head>
  <body>
    <div class="container-fluid">  
        <div class="row ">  
            <div class="col-md-6 d-none d-md-block">  
                <img src="asset/illus_bg.png" class="img-fluid" style="min-width: 670px; height: 625px; margin-left:-20px;" />  
            </div>  
            <div class="col-md-6 bg-white my-5">  
                <div class="d-grid gap-2 col-6 mx-auto">
                    <img src="asset/logo.png" alt="logo_klambimu" class="pb-2 w-75" style="margin-left: 20px;">
                    <h5 class="pb-2 fw-bolder text-center" style="color: #948869;">Welcome Back to Our Website!</h5>  
                </div>
                <div class="pb-2 fw-bolder">
                    <h3>Login</h3></div>
                    <?php 
                        if(!empty($login_err)){
                            echo '<div class="alert alert-danger">' . $login_err . '</div>';
                        }        
                    ?>
                    <div class="form-style">  
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">  
                            <div class="form-group pb-3">
                                <label class="fw-bolder" style="color: #948869;">Username</label>
                                <input type="text" placeholder="example123"  name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                                <span class="invalid-feedback"><?php echo $name_err; ?></span>    
                            </div>  
                            <div class="form-group pb-1"> 
                                <label class="fw-bolder" style="color: #948869;">Password</label>
                                <input type="password" name="password" placeholder="Enter your password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                                <span class="invalid-feedback"><?php echo $password_err; ?></span>
                            </div>  
                            <div class="d-grid gap-2 mr-4 pb-2 d-md-flex justify-content-md-end" style="padding-right: 30px;">
                                <a href="#" class="me-md-2">Forget Password?</a>
                            </div>
                            <div class="pb-2 "></div> 
                            <div class="d-grid gap-2 col-6 mx-auto">
                                <input class="btn fw-bolder rounded" type="submit" value="Login" style="background-color: #008037; color:white;"></input>
                            </div>
                        </form> 
                    <div class="pt-4 text-center">  
                        Don't have an account? <a href="regis2.php">Registration here</a>  
                    </div>  
                </div>  
            </div>  
        </div>  
    </div> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
  </body>
</html>