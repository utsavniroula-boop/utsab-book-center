<?php

session_start();
if (isset($_SESSION["uname"])){
} else {
    $_SESSION['toasttitle'] = "Login Required.";
    $_SESSION['toastmessage'] = "You need to login as a user.";
    header("Location: ../login.php");
}