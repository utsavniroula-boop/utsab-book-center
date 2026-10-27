<?php
session_start();
ob_start();

$conn = new mysqli('localhost', 'root', 'root', 'utsabbookcenter');
if ($conn->connect_error) {
    die('Connection Failed : ' . $conn->connect_error);
} else {
    if (isset($_POST['sign_in'])) {
        $uname = $_POST['username'];
        $passw = $_POST['password'];
        $username = mysqli_real_escape_string($conn, $uname);
        $password = mysqli_real_escape_string($conn, $passw);

        $sql = "select * from login where uname = '$username' and passw = '$password'";
        $result = mysqli_query($conn, $sql);
        $count = mysqli_num_rows($result);

        if (isset($_POST['rememberme'])) {
            setcookie('usernameofshopmanagement', $username, time() + 86400);
            setcookie('passwordofshopmanagement', $password, time() + 86400);

            if ($count >= 1) {
                $_SESSION['uname'] = $username;
                header("Location:index.php");
            } else {
                $_SESSION['errormessage'] = " Wrong Username or Password.";
            }
        } else {
            if ($count >= 1) {
                $_SESSION['uname'] = $username;
                header("Location:index.php");
            } else {
                $_SESSION['errormessage'] = " Wrong Username or Password.";
            }
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <script src="jquery/jquery-3.6.1.min.js"></script>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/fonts.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body style="background-color: #0d6efd;">
    <h2 class="text-center text-white" style="margin-top:25px; text-transform:uppercase;">Utsab Book Center</h2>
    <h5 class="text-center text-white">Bahradashi-3, Rajgadh</h5>
    <form method="post" class="col-xl-6 col-lg-6 col-md-6 col-sm-10 col-10 d-flex justify-content-center" style="background-color:#fff; border-radius:5px; padding:50px; margin:auto; margin-top:6%; flex-direction:column;">
        <?php include("message.php") ?>
        <h2>Login to your account</h2><br>
        <div class="row mb-3">
            <label for="username" class="col-sm-2 col-form-label" style="color: black;">Username</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" value="<?php if(isset($_COOKIE['usernameofshopmanagement'])) { echo $_COOKIE['usernameofshopmanagement']; }?>" id="username" name="username" required>
            </div>
        </div>
        <div class="row mb-3">
            <label for="password" class="col-sm-2 col-form-label" style="color: black;">Password</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" value="<?php if(isset($_COOKIE['passwordofshopmanagement'])) { echo $_COOKIE['passwordofshopmanagement']; }?>" id="password" name="password" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-10">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="rememberme" name="rememberme">
                    <label class="form-check-label" for="rememberme">
                        Remember Me
                    </label>
                </div>
            </div>
        </div>
        <input type="submit" value="Sign In" class="btn btn-primary" name="sign_in">
    </form>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>