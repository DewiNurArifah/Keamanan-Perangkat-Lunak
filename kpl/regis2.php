<?php

// registration.php

// Panggil config database
require_once "config.php";
 
// Mendefinisikan masing-masing variabel dengan nilai kosong
$name = $password = $confirm_password = "";
$name_err = $password_err = $confirm_password_err = "";
 
// Lakukan jika data sudah masuk kedalam form
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Cek apakah nama kosong
    if(empty(trim($_POST["name"]))){
        $name_err = "Silahkan masukan nama.";
    // Deteksi karakter yang tidak diizinkan dengan mengunakan regex
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["name"]))){
        $name_err = "Nama hanya boleh diisi dengan kombinasi kata, nomor atau underscore.";
    } else {
        // Mengkoneksikan dengan database
        $sql = "SELECT id FROM users WHERE name = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Digunakan untuk mengikat variabel ke marker parameter dari statement yang disiapkan.
            mysqli_stmt_bind_param($stmt, "s", $param_name);
            
            // Siapkan parameter nama
            $param_name = trim($_POST["name"]);
            
            // Lakukan eksekusi
            if(mysqli_stmt_execute($stmt)){

                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $name_err = "Nama ini sudah terdaftar!";
                } else{
                    $name = trim($_POST["name"]);
                }
            } else{
                // Menampilkan error jika statement gagal dieksekusi
                echo "Sepertinya terjadi error, silahkan coba lagi nanti.";
            }

            // Tutup statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validasi password
    if(empty(trim($_POST["password"]))){
        $password_err = "Silahkan masukan password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password harus diisi dengan maksimal 6 karakter.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validasi konfirmasi password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Silahkan konfimasi password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password tidak cocok.";
        }
    }
    
    // Cek error sebelum dieksekusi kedalam database
    if(empty($name_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Siapkan koneksi dengan database
        $sql = "INSERT INTO users (name, password) VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($conn, $sql)){

            // Digunakan untuk mengikat variabel ke marker parameter dari statement yang disiapkan.
            mysqli_stmt_bind_param($stmt, "ss", $param_name, $param_password);
            
            // Set parameter dari nama dan password
            $param_name = $name;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // merubah password menjadi hash code
            
            // Lakukan eksekusi
            if(mysqli_stmt_execute($stmt)){
                // Lakukan redirect jika berhasil
                header("location: login.php");
            } else{
                // Menampilkan error jika statement gagal dieksekusi
                echo "Sepertinya terjadi error, silahkan coba lagi nanti.";
            }

            // Tutup statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Tutup koneksi dengan database
    mysqli_close($conn);
}
?> 
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Regist demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="style/style_login.css">
  </head>
  <body>
    <div class="container-fluid">  
        <div class="row">  
            <div class="col-md-6 d-none d-md-block">  
                <img src="asset/illus_bg.png" class="img-fluid" style="min-width: 670px; height: 625px; margin-left:-20px;" />  
            </div>  
            <div class="col-md-6 bg-white my-3">  
                <div class="d-grid gap-2 col-6 mx-auto">
                    <img src="asset/logo.png" alt="logo_klambimu" class=" w-75" style="margin-left: 20px;">
                    <h5 class="pb-2 fw-bolder text-center" style="color: #948869;">Welcome Back to Our Website!</h5> 
                </div>
                <div class="pb-2 fw-bolder">
                    <h3>Create Account</h3></div>
                    <div class="form-style">  
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">  
                            <div class="form-group pb-3">
                                <label for="" class="fw-bolder" style="color: #948869;">Email</label>
                                <input type="text" placeholder="example@gmail.com" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>"> 
                                <span class="invalid-feedback"><?php echo $name_err; ?></span>   
                            </div>  
                            <div class="form-group pb-1"> 
                                <label class="fw-bolder" style="color: #948869;">Password</label>
                                <input type="password" name="password" placeholder="Enter your password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                                <span class="invalid-feedback"><?php echo $password_err; ?></span>  
                            </div>
                            <div class="form-group pb-1"> 
                                <label class="fw-bolder" style="color: #948869;">Confirm Password</label>
                                <input type="password" name="confirm_password" placeholder="Enter your password again" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                            </div>
                            <!-- <div class="d-grid gap-2 mr-4 pb-2 d-md-flex justify-content-md-end" style="padding-right: 30px;">
                                <a href="#" class="me-md-2">Forget Password?</a>
                            </div> -->
                            <div class="pb-2 "></div> 
                            <div class="d-flex col-6 mx-auto">
                                <input class="btn fw-bolder rounded" type="submit" value="Submit" style="background-color: #008037; color:white; margin-left: 60px;"></input>
                                <input class="btn fw-bolder rounded" type="reset" value="Reset" style="background-color: #eb4634; color:white; margin-left: 50px;"></input>
                            </div>
                        </form> 
                    <div class="pt-4 text-center">  
                        Already have an account? <a href="login.php">Login here</a>  
                    </div>  
                </div>  
            </div>  
        </div>  
    </div> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
  </body>
</html>