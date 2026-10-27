<?php
    session_start();

    if (!$_SESSION['uname']) {
        $_SESSION['errormessage_nogreeting'] = " You'll have to login first.";
        header("Location:noticeadminlogin.php");
        exit(0);
    }
    
    session_destroy();
    unset($_SESSION['uname']);

    setcookie('usernameofshopmanagement','', time() - 86400);
    setcookie('passwordofshopmanagement', '', time() - 86400);

    header("Location: login.php")
?>