<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style=" margin-top: 0; background-color:#0d6efd;">
    <div class="container-fluid" style="font-family:'PT Serif', sans-serif ;">
        <a class="navbar-brand logo" href="index.php"><img src="images/necessary/logo-white.png" alt="logo" style="height: 50px;">
            <span class="hidden" style="text-transform: capitalize;">उत्सव पुस्तक पसल </span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0" style="font-family: 'PT Serif', serif; text-transform: capitalize;">
                <li class="nav-item">
                    <a class="nav-link navigation-bar-links" href="suppliers.php" style="font-size: 20px;">सप्लायर्स </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link navigation-bar-links active" href="index.php" style="font-size: 20px;">खाताहरु </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link navigation-bar-links" href="stock/" style="font-size: 20px;">स्टक </a>
                </li>
                <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" style="font-size: 20px; color:black;" href="logout.php" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span style="color:black; text-transform:capitalize;"><i class="bi bi-person-circle"></i> <?php echo $_SESSION['uname'] ?></span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown" id="navdropdown">
                            <li><button class="dropdown-item nav-dropdown logout" name="logout" id="logout">Log Out <i class="bi bi-box-arrow-right"></i></button></li>
                        </ul>
                    </li>
            </ul>
        </div>
    </div>
</nav>