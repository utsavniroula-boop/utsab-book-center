<?php

if (isset($_SESSION['warningmessage'])) {
?>

    <div class="alert alert-warning alert-dismissible fade show" role="alert" style="width: auto; margin:10px;">
        <strong>Hey !</strong><?= $_SESSION['warningmessage'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php
    unset($_SESSION['warningmessage']);
}

if (isset($_SESSION['warningmessage_noexit'])) {
?>

    <div class="alert alert-warning alert-dismissible fade show" role="alert" style="width: auto; margin:10px;">
        <strong>Hey !</strong><?= $_SESSION['warningmessage_noexit'] ?>
    </div>
<?php
    unset($_SESSION['warningmessage_noexit']);
}

if (isset($_SESSION['errormessage'])) {
    ?>
    
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="width: auto; margin:10px;">
            <strong>Hello !</strong><?= $_SESSION['errormessage'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php
        unset($_SESSION['errormessage']);
}

if (isset($_SESSION['errormessage_nogreeting'])) {
    ?>
    
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="width: auto; margin:10px;">
            <strong>Hello !</strong><?= $_SESSION['errormessage_nogreeting'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php
        unset($_SESSION['errormessage_nogreeting']);
}

if (isset($_SESSION['successmessage'])) {
    ?>
    
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto; margin:10px;">
            <strong>Hey !</strong><?= $_SESSION['successmessage'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php
        unset($_SESSION['successmessage']);
}

if (isset($_SESSION['successmessage_nogreeting'])) {
    ?>
    
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto; margin:10px;">
            <strong>Hey !</strong><?= $_SESSION['successmessage_nogreeting'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php
        unset($_SESSION['successmessage_nogreeting']);
}

?>