<?php
session_start();

if (!$_SESSION['uname']) {
    $_SESSION['errormessage'] = " You'll have to login first.";
    header("Location:login.php");
    exit(0);
}

$conn = new mysqli('localhost', 'root', 'root', 'utsabbookcenter');
if ($conn->connect_error) {
    die('Connection Failed : ' . $conn->connect_error);
} else {
    if (isset($_POST['add_account']) && isset($_FILES['customer_photo'])) {

        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $contact = mysqli_real_escape_string($conn, $_POST['contact']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $additional_info = mysqli_real_escape_string($conn, $_POST['additional_info']);

        $img_name = $_FILES['customer_photo']['name'];
        $img_size = $_FILES['customer_photo']['size'];
        $error = $_FILES['customer_photo']['error'];
        $tmp_name = $_FILES['customer_photo']['tmp_name'];

        if ($error === 0) {
            $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
            $img_ex_lc = strtolower($img_ex);

            $allowed_ex = array("jpg", "jpeg", "png");

            if (in_array($img_ex_lc, $allowed_ex)) {
                $new_img_name = uniqid("CUSTOMER-", true) . '.' . $img_ex_lc;
                $upload_path = 'customerphotos/' . $new_img_name;
                move_uploaded_file($tmp_name, $upload_path);

                $sql = "insert into customers(name,contact,address,photo,additional_info) values('$name','$contact','$address','$new_img_name','$additional_info')";
                $execution_state = mysqli_query($conn, $sql);

                $get_id = "select * from customers order by id desc";
                $execution_state_getid = mysqli_query($conn, $get_id);

                if ($execution_state_getid) {
                    $row_getid = mysqli_fetch_array($execution_state_getid);
                }

                $get_id = $row_getid['id'];
                $name_lowercase = strtolower($name);
                $name_lowercase_nospace = str_replace(" ", "", $name_lowercase);
                $name_lowercase_nospace_id_included = $name_lowercase_nospace . $get_id;
                $tablename = $name_lowercase_nospace_id_included;

                $sql2 = "CREATE TABLE `" . $tablename . "` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `status` VARCHAR(45) NULL,
                    `amount` VARCHAR(45) NOT NULL,
                    `date` VARCHAR(45) NULL,
                    `description` VARCHAR(100) NULL,
                    PRIMARY KEY (`id`))";        
                $execution_state2 = mysqli_query($conn, $sql2);

                if ($execution_state && $execution_state2) {
                    $_SESSION['successmessage'] = " Customer added successfully.";
                    header("Location:index.php");
                    exit(0);
                }
            } else {
                $_SESSION['errormessage'] = " Unknown extension. Only .jpg .jpeg and .png are valid.";
            }
        }
    }
    if (isset($_POST['add_account'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $contact = mysqli_real_escape_string($conn, $_POST['contact']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $additional_info = mysqli_real_escape_string($conn, $_POST['additional_info']);

        $sql = "insert into customers(name,contact,address,additional_info) values('$name','$contact','$address','$additional_info')";
        $execution_state = mysqli_query($conn, $sql);

        $get_id = "select * from customers order by id desc";
        $execution_state_getid = mysqli_query($conn, $get_id);

        if ($execution_state_getid) {
            $row_getid = mysqli_fetch_array($execution_state_getid);
        }

        $get_id = $row_getid['id'];
        $name_lowercase = strtolower($name);
        $name_lowercase_nospace = str_replace(" ", "", $name_lowercase);
        $name_lowercase_nospace_id_included = $name_lowercase_nospace . $get_id;
        $tablename = $name_lowercase_nospace_id_included;
        echo $tablename;


        $sql2 = "CREATE TABLE `" . $tablename . "` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `status` VARCHAR(45) NULL,
            `amount` VARCHAR(45) NOT NULL,
            `date` VARCHAR(45) NULL,
            `description` VARCHAR(100) NULL,
            PRIMARY KEY (`id`))";

        $execution_state2 = mysqli_query($conn, $sql2);

        if ($execution_state && $execution_state2) {
            $_SESSION['successmessage'] = " Customer added successfully.";
            header("Location:index.php");
            exit(0);
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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts</title>
</head>

<body>
    <?php include("navbar.php") ?>

    <div class="modal" id="confirm-logout" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Log Out</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to log out ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <a href="logout.php" class="btn btn-danger">Yes</a>
                </div>
            </div>
        </div>
    </div>

    <form method="post" enctype="multipart/form-data" class="d-flex justify-content-center" style="background-color:#fff; border-radius:5px; padding:25px; flex-direction:column;">
        <?php include("message.php") ?>
        <h2>नया खाता बनाउनुहोस </h2>
        <div class="row mb-3">
            <label for="name" class="col-form-label" style="color: black;"><strong>नाम :</strong></label>
            <div>
                <input type="text" autofocus class="form-control" id="name" placeholder="ग्राहकको नाम" name="name" pattern="[A-Za-z\s]+" required>
            </div>
        </div>
        <div class="row mb-3">
            <label for="contact" class="col-form-label" style="color: black;"><strong>फोन नम्बर :</strong></label>
            <div>
                <input type="text" class="form-control" id="contact" placeholder="ग्राहकको फोन नम्बर" name="contact" required>
            </div>
        </div>
        <div class="row mb-3">
            <label for="address" class="col-form-label" style="color: black;"><strong>ठेगाना :</strong></label>
            <div>
                <input type="text" class="form-control" id="address" placeholder="ग्राहकको ठेगाना" name="address">
            </div>
        </div>

        <div class="input-group mb-3">
            <label class="input-group-text text-white bg-primary" for="photo">फोटो हाल्नुहोस</label>
            <input type="file" class="form-control" id="photo" name="customer_photo">
        </div>
        <div class="row mb-3">
            <label for="additional_info" class="col-form-label" style="color: black;"><strong>व्यक्तिगत विवरण :</strong></label>
            <div>
                <input type="text" class="form-control" id="additional_info" placeholder="व्यक्तिगत विवरण" name="additional_info">
            </div>
        </div>
        <button type="submit" name="add_account" class="btn btn-primary">Add Customer</button>
        <a href="index.php" class="mt-1 btn btn-danger">Cancel</a>
    </form>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {

            window.addEventListener("keyup", function (e) { if (e.keyCode == 27) history.back(); }, false);

            $('.logout').click(function(e) {
                e.preventDefault();

                $('#confirm-logout').modal('show');

            });
        });
    </script>
</body>

</html>