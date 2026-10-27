<?php
session_start();
if (!$_SESSION['uname']) {
    $_SESSION['errormessage'] = " You'll have to login first.";
    header("Location:login.php");
    exit(0);
}

$id = $_GET['id'];
$conn = new mysqli('localhost', 'root', 'root', 'utsabbookcenter');
if ($conn->connect_error) {
    die('Connection Failed : ' . $conn->connect_error);
} else {

    $sql_getname = "select * from customers where id = $id";
    $sql_getname_result = mysqli_query($conn, $sql_getname);

    if ($sql_getname_result) {
        $rows_display = mysqli_fetch_array($sql_getname_result);
        $name = $rows_display['name'];
        $print_name = $rows_display['name'];
        $print_address = $rows_display['address'];
    }
}

$conn = new mysqli('localhost', 'root', 'root', 'utsabbookcenter');
if ($conn->connect_error) {
    die('Connection Failed : ' . $conn->connect_error);
} else {

    if (isset($_FILES['edit_photo'])) {

        $edit_image_attached = true;

        $edit_img_name = $_FILES['edit_photo']['name'];
        $edit_img_size = $_FILES['edit_photo']['size'];
        $edit_error = $_FILES['edit_photo']['error'];
        $edit_tmp_name = $_FILES['edit_photo']['tmp_name'];

        $edit_id = mysqli_real_escape_string($conn, $_POST['edit_photo_id']);


        if ($edit_error === 0) {
            $edit_img_ex = pathinfo($edit_img_name, PATHINFO_EXTENSION);
            $edit_img_ex_lc = strtolower($edit_img_ex);

            $edit_allowed_ex = array("jpg", "jpeg", "png");

            if (in_array($edit_img_ex_lc, $edit_allowed_ex)) {
                $edit_new_img_name = uniqid("IMG-", true) . '.' . $edit_img_ex_lc;
                $edit_upload_path = 'customerphotos/' . $edit_new_img_name;
                move_uploaded_file($edit_tmp_name, $edit_upload_path);

                $sql_get_old_photo = "select photo from customers where id='$edit_id' ";
                $execution_state_get_old_photo = mysqli_query($conn, $sql_get_old_photo);
                if ($execution_state_get_old_photo) {
                    while ($rows_getphoto = mysqli_fetch_array($execution_state_get_old_photo)) {

                        $file_pointer = $rows_getphoto['photo'];
                        unlink("customerphotos/" . $file_pointer);
                    }
                }

                $sql_edit_photo = "update customers set photo='$edit_new_img_name' where id='$edit_id' ";
                $execution_state_edit_photo = mysqli_query($conn, $sql_edit_photo);

                if ($execution_state_edit_photo) {
                    $_SESSION['successmessage'] = " Photo changed successfully.";
                    header("Location:index.php");
                    exit(0);
                }
            } else {
                $_SESSION['errormessage'] = " Unknown extension. Only .jpg .jpeg and .png are valid.";
            }
        }
    }

    if (isset($_POST['edit_customer_profile'])) {
        $id = $_POST['edit_profile_id'];
        $name = $_POST['edit_profile_name'];
        $oldname = $_POST['edit_profile_oldname'];
        $address = $_POST['edit_profile_address'];
        $contact = $_POST['edit_profile_contact'];
        $additional_info = $_POST['edit_profile_additional_info'];

        $oldname_lowercase_to_edit_profile = strtolower($oldname);
        $oldname_lowercase_nospace_to_edit_profile = str_replace(" ", "", $oldname_lowercase_to_edit_profile);
        $oldname_lowercase_nospace_id_included_to_edit_profile = $oldname_lowercase_nospace_to_edit_profile . $id;
        $tablename_to_edit_profile = $oldname_lowercase_nospace_id_included_to_edit_profile;

        $newname_lowercase_to_edit_profile = strtolower($name);
        $newname_lowercase_nospace_to_edit_profile = str_replace(" ", "", $newname_lowercase_to_edit_profile);
        $newname_lowercase_nospace_id_included_to_edit_profile = $newname_lowercase_nospace_to_edit_profile . $id;
        $new_table_name = $newname_lowercase_nospace_id_included_to_edit_profile;

        $sql_profile_update = "update customers set name='$name',address='$address',contact='$contact',additional_info='$additional_info' where id = '$id'";
        $sql_profile_update_result = mysqli_query($conn, $sql_profile_update);

        $sql_change_tablename = "alter table $tablename_to_edit_profile rename to $new_table_name;";
        $sql_change_tablename_result = mysqli_query($conn, $sql_change_tablename);

        if ($sql_profile_update_result && $sql_change_tablename_result) {
            $_SESSION['successmessage'] = " Profile updated successfully.";
            header("Location:index.php");
            exit(0);
        }
    }

    if (isset($_POST['transaction_panel'])) {
        $id = $_POST['customer_id'];
        $name = $_POST['name'];
        $gave = $_POST['yougave'];
        $took = $_POST['youtook'];
        $description = $_POST['description'];

        date_default_timezone_set('Asia/Kathmandu');
        $date = date('Y/m/d h:i:s A');
        $dateOnly = date('Y/m/d');

        $name_lowercase = strtolower($name);
        $name_lowercase_nospace = str_replace(" ", "", $name_lowercase);
        $name_lowercase_nospace_id_included = $name_lowercase_nospace . $id;
        $tablename = $name_lowercase_nospace_id_included;

        if ($gave == "" && $took == "") {
            $_SESSION['errormessage'] = " Cannot accept null values";
            header("Location: profile.php?id=$id");
            exit(0);
        }

        if ($gave != "" && $took != "") {
            $_SESSION['errormessage'] = " Cannot accept both values";
            header("Location: profile.php?id=$id");
            exit(0);
        }

        if ($gave == "") {
            $gave = 0;

            $sql_transaction_history_write = "insert into `" . $tablename . "`(status,amount,date,description) values('took','$took','$date','$description')";
            $sql_transaction_history_write_result = mysqli_query($conn, $sql_transaction_history_write);
        }

        if ($took == "") {
            $took = 0;

            $sql_transaction_history_write = "insert into `" . $tablename . "`(status,amount,date,description) values('gave','$gave','$date','$description')";
            $sql_transaction_history_write_result = mysqli_query($conn, $sql_transaction_history_write);
        }

        $transaction = $gave - $took;

        $sql_transaction_read = "select transaction from customers where id = '$id'";
        $sql_transaction_read_result = mysqli_query($conn, $sql_transaction_read);
        $rows = mysqli_fetch_array($sql_transaction_read_result);

        if($rows['transaction'] != null){
            $old_amount = $rows['transaction'];
        }else{
            $old_amount = 0;
        }

        $new_amount = $old_amount - $took + $gave;

        if ($new_amount < 0) {
            $sql_status_write = "update customers set status='togive' where id = '$id'";
            $sql_status_write_result = mysqli_query($conn, $sql_status_write);
        } else if ($new_amount > 0) {
            $sql_status_write = "update customers set status='totake' where id = '$id'";
            $sql_status_write_result = mysqli_query($conn, $sql_status_write);
        } else {
            $sql_status_write = "update customers set status='clear' where id = '$id'";
            $sql_status_write_result = mysqli_query($conn, $sql_status_write);
        }

        $sql_transaction_write = "update customers set transaction='$new_amount',lasttransaction='$dateOnly' where id = '$id'";
        $sql_transaction_write_result = mysqli_query($conn, $sql_transaction_write);

        if ($sql_transaction_write && $sql_status_write_result && $sql_transaction_history_write_result) {
            $_SESSION['successmessage'] = " Transaction recorded successfully";
            header("Location: profile.php?id=$id");
            exit(0);
        }
    }

    if (isset($_POST['delete_customer'])) {

        $delete_account_id = $_POST['delete_profile_id'];

        $sql_delete_account = "delete from customers where id='$delete_account_id';";
        $sql_delete_account_result = mysqli_query($conn, $sql_delete_account);

        if ($sql_delete_account_result) {
            $_SESSION['successmessage'] = " Account deleted successfully";
            header("Location: index.php");
            exit(0);
        }
    }

    if (isset($_POST['edit_old_transaction'])) {

        $edit_customer_id = $_POST['customer_id_toedit'];
        $edit_id = $_POST['edit_id'];

        $new_amount = $_POST['edit_amount'];
        $new_desc = $_POST['edit_desc'];

        $sql_getname = "select * from customers where id = $edit_customer_id";
        $sql_getname_result = mysqli_query($conn, $sql_getname);

        if ($sql_getname_result) {
            $rows_delete_transaction = mysqli_fetch_array($sql_getname_result);
            $name = $rows_delete_transaction['name'];
        }

        $name_lowercase_toedit = strtolower($name);
        $name_lowercase_nospace_toedit = str_replace(" ", "", $name_lowercase_toedit);
        $name_lowercase_nospace_id_included_toedit = $name_lowercase_nospace_toedit . $edit_customer_id;
        $tablename_toedit = $name_lowercase_nospace_id_included_toedit;

        $sql_get_old_amount_edit = "select * from $tablename_toedit where id='$edit_id'";
        $sql_get_old_amount_edit_result = mysqli_query($conn, $sql_get_old_amount_edit);

        if ($sql_get_old_amount_edit_result) {
            $rows_edit = mysqli_fetch_array($sql_get_old_amount_edit_result);
            $old_amount = $rows_edit['amount'];
            $old_status = $rows_edit['status'];
        }

        if ($old_status == "gave") {

            $sql_change_old_amount_edit = "update $tablename_toedit set amount='$new_amount',description='$new_desc' where id='$edit_id'";
            $sql_change_old_amount_edit_result = mysqli_query($conn, $sql_change_old_amount_edit);

            $sql_get_old_totalAmount_edit = "select * from customers where id='$edit_customer_id'";
            $sql_get_old_totalAmount_edit_result = mysqli_query($conn, $sql_get_old_totalAmount_edit);

            if ($sql_get_old_totalAmount_edit_result) {
                $rows_edit = mysqli_fetch_array($sql_get_old_totalAmount_edit_result);
                $old_totalAmount = $rows_edit['transaction'];
                $old_totalStatus = $rows_edit['status'];
            }

            $new_totalAmount = ($old_totalAmount - $old_amount) + $new_amount ;
            if($new_totalAmount < 0){
                $new_totalStatus = "togive";
            }
            else if($new_totalAmount  == 0){
                $new_totalStatus = "clear";
            }
            else{
                $new_totalStatus = "totake";
            }

            $sql_change_old_totalAmount_edit = "update customers set transaction='$new_totalAmount',status='$new_totalStatus' where id='$edit_customer_id'";
            $sql_change_old_totalAmount_edit_result = mysqli_query($conn, $sql_change_old_totalAmount_edit);

            if ($sql_change_old_totalAmount_edit_result) {
                $_SESSION['successmessage'] = " Transaction updated successfully";
                header("Location: profile.php?id=$edit_customer_id");
            }

        } else {

            $sql_change_old_amount_edit = "update $tablename_toedit set amount='$new_amount',description='$new_desc' where id='$edit_id'";
            $sql_change_old_amount_edit_result = mysqli_query($conn, $sql_change_old_amount_edit);

            $sql_get_old_totalAmount_edit = "select * from customers where id='$edit_customer_id'";
            $sql_get_old_totalAmount_edit_result = mysqli_query($conn, $sql_get_old_totalAmount_edit);

            if ($sql_get_old_totalAmount_edit_result) {
                $rows_edit = mysqli_fetch_array($sql_get_old_totalAmount_edit_result);
                $old_totalAmount = $rows_edit['transaction'];
                $old_totalStatus = $rows_edit['status'];
            }

            $new_totalAmount = ($old_totalAmount + $old_amount) - $new_amount ;
            if($new_totalAmount < 0){
                $new_totalStatus = "togive";
            }
            else if($new_totalAmount  == 0){
                $new_totalStatus = "clear";
            }
            else{
                $new_totalStatus = "totake";
            }

            $sql_change_old_totalAmount_edit = "update customers set transaction='$new_totalAmount',status='$new_totalStatus' where id='$edit_customer_id'";
            $sql_change_old_totalAmount_edit_result = mysqli_query($conn, $sql_change_old_totalAmount_edit);

            if ($sql_change_old_totalAmount_edit_result) {
                $_SESSION['successmessage'] = " Transaction updated successfully";
                header("Location: profile.php?id=$edit_customer_id");
            }

        }
    }

    if (isset($_POST['delete_old_transaction'])) {

        $delete_customer_id = $_POST['customer_id_todelete'];
        $delete_id = $_POST['delete_id'];

        $sql_getname = "select * from customers where id = $delete_customer_id";
        $sql_getname_result = mysqli_query($conn, $sql_getname);

        if ($sql_getname_result) {
            $rows_delete_transaction = mysqli_fetch_array($sql_getname_result);
            $name = $rows_delete_transaction['name'];
        }

        $name_lowercase_todelete = strtolower($name);
        $name_lowercase_nospace_todelete = str_replace(" ", "", $name_lowercase_todelete);
        $name_lowercase_nospace_id_included_todelete = $name_lowercase_nospace_todelete . $delete_customer_id;
        $tablename_todelete = $name_lowercase_nospace_id_included_todelete;

        $sql_make_changes = "select * from `" . $tablename_todelete . "` where id = '$delete_id'";
        $sql_make_changes_result = mysqli_query($conn, $sql_make_changes);

        $sql_delete_record = "delete from `" . $tablename_todelete . "` where id = '$delete_id'";
        $sql_delete_record_result = mysqli_query($conn, $sql_delete_record);

        if ($sql_delete_record_result) {
            while ($fetched_delete_data = mysqli_fetch_array($sql_make_changes_result)) {
                $transaction_type = $fetched_delete_data['status'];
                $transaction_amount = $fetched_delete_data['amount'];

                $sql_get_old_amount = "select * from customers where id = '$delete_customer_id'";
                $sql_get_old_amount_result = mysqli_query($conn, $sql_get_old_amount);

                while ($old_amount = mysqli_fetch_array($sql_get_old_amount_result)) {
                    $oldamount = $old_amount['transaction'];
                }

                if ($transaction_type == "took") {
                    $newamount = $oldamount + $transaction_amount;
                    echo $newamount;
                } else if ($transaction_type == "gave") {
                    $newamount = $oldamount - $transaction_amount;
                    echo $newamount;
                } else {
                    header("Location: index.php");
                }

                if ($newamount < 0) {
                    $sql_make_changes_on_maintable = "update customers set status='togive',transaction='$newamount' where id='$delete_customer_id'";
                    $sql_make_changes_on_maintable_result = mysqli_query($conn, $sql_make_changes_on_maintable);

                    if ($sql_make_changes_on_maintable_result) {
                        $_SESSION['successmessage'] = " Transaction deleted successfully";
                        header("Location: profile.php?id=$delete_customer_id");
                    }
                } else if ($newamount > 0) {
                    $sql_make_changes_on_maintable = "update customers set status='totake',transaction='$newamount' where id='$delete_customer_id'";
                    $sql_make_changes_on_maintable_result = mysqli_query($conn, $sql_make_changes_on_maintable);

                    if ($sql_make_changes_on_maintable_result) {
                        $_SESSION['successmessage'] = " Transaction deleted successfully";
                        header("Location: profile.php?id=$delete_customer_id");
                    }
                } else {
                    $sql_make_changes_on_maintable = "update customers set status='clear',transaction='$newamount' where id='$delete_customer_id'";
                    $sql_make_changes_on_maintable_result = mysqli_query($conn, $sql_make_changes_on_maintable);

                    if ($sql_make_changes_on_maintable_result) {
                        $_SESSION['successmessage'] = " Transaction deleted successfully";
                        header("Location: profile.php?id=$delete_customer_id");
                    }
                }
            }
        }

        $sql_delete = "delete * from '$tablename_todelete' where id = '$delete_id'";
        $sql_delete_result = mysqli_query($conn, $sql_delete);
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
    <title><?php echo $name; ?></title>
</head>

<body>
    <?php include("navbar.php") ?>
    <?php include("message.php") ?>

    <!-- ------------------------------------------------------------------------------------------------- -->

    <div class="modal fade" id="new-transaction" tabindex="-1" aria-labelledby="edit-data-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="new-transaction-modal">नयाँ कारोबार</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="text" name="customer_id" id="customer_id" class="customer_id" hidden>
                        <div class="input-group mb-3">
                            <span class="input-group-text text-white bg-primary" id="basic-addon1">ग्राहकको नाम</span>
                            <input type="text" class="form-control border-primary" id="name" name="name" aria-label="Name" aria-describedby="basic-addon1" readonly>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text text-white bg-danger focusabc" id="basic-addon1">सामान बापत पाउनुपर्ने रकम रू</span>
                            <input type="text" class="form-control border-danger" placeholder="" aria-label="yougave" name="yougave" aria-describedby="basic-addon1" pattern="[0-9\s]+">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text text-white bg-success" id="basic-addon1">पाएको रकम रू</span>
                            <input type="text" class="form-control border-success" placeholder="" aria-label="youtook" name="youtook" aria-describedby="basic-addon1" pattern="[0-9\s]+">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text text-white bg-primary" id="basic-addon1">विवरण</span>
                            <input type="text" class="form-control border-primary" name="description" placeholder="" aria-label="descripion" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="transaction_panel" class="btn btn-success transaction_panel">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ------------------------------------------------------------------------------------ -->

    <div class="modal fade" id="edit_photo" tabindex="-1" aria-labelledby="edit-data-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit-photo-modal">फोटो परिवर्तन</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="text" name="edit_photo_id" id="edit_photo_id" class="edit_id" hidden>
                        <div class="input-group mb-3">
                            <label class="input-group-text text-white bg-primary" for="photo">नाम</label>
                            <input type="text" class="form-control" id="edit_photo_name" name="edit_photo_name" disabled>
                        </div>
                        <div class="input-group mb-3">
                            <label class="input-group-text bg-warning" for="photo">Change Photo</label>
                            <input type="file" class="form-control" id="edit_photo" name="edit_photo" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_customer" class="btn btn-success edit_customer">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ------------------------------------------------------------------------------------ -->


    <div class="modal fade" id="edit_profile" tabindex="-1" aria-labelledby="edit-data-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit-profile-modal">खाताको विवरण परिबर्तन</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="text" name="edit_profile_id" id="edit_profile_id" class="edit_profile_id" hidden>
                        <input type="text" name="edit_profile_oldname" id="edit_profile_oldname" class="edit_profile_oldname" hidden>
                        <div class="input-group mb-3">
                            <label class="input-group-text text-white bg-primary" for="name">नाम</label>
                            <input type="text" class="form-control" id="edit_profile_name" name="edit_profile_name">
                        </div>
                        <div class="input-group mb-3">
                            <label class="input-group-text text-white bg-primary" for="address">ठेगाना</label>
                            <input type="text" class="form-control" id="edit_profile_address" name="edit_profile_address">
                        </div>
                        <div class="input-group mb-3">
                            <label class="input-group-text text-white bg-primary" for="contact">फोन नम्बर</label>
                            <input type="text" class="form-control" id="edit_profile_contact" name="edit_profile_contact">
                        </div>
                        <div class="input-group mb-3">
                            <label class="input-group-text text-white bg-primary" for="additional_info">व्यक्तिगत विवरण</label>
                            <input type="text" class="form-control" id="edit_profile_additional_info" name="edit_profile_additional_info">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_customer_profile" class="btn btn-success edit_customer">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ------------------------------------------------------------------------------------ -->

    <div class="modal fade" id="delete-transaction" tabindex="-1" aria-labelledby="edit-data-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit-photo-modal">रेकर्ड मेटाउनुहोस्</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        यस ग्राहकको सबै लेनदेन तदनुसार परिवर्तन हुनेछ ।<br>के तपाई पक्का यो कारोबार मेटाउन चहानुहुन्छ ?<br><br>
                        <input type="text" name="delete_id" id="delete_id" class="delete_id" hidden>
                        <input type="text" name="customer_id_todelete" id="customer_id_todelete" class="customer_id_todelete" hidden>
                        <div class="input-group mb-3">
                            <label class="input-group-text text-white bg-danger" for="photo">विवरण</label>
                            <input type="text" class="form-control" id="delete_desc" name="delete_desc" disabled>
                        </div>
                        <div class="input-group mb-3">
                            <label class="input-group-text text-white bg-danger" for="photo">कारोबार रकम</label>
                            <input type="text" class="form-control" id="delete_amount" name="delete_amount" disabled>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_old_transaction" class="btn btn-danger delete_old_transaction">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ------------------------------------------------------------------------------------ -->

    <div class="modal fade" id="edit-transaction" tabindex="-1" aria-labelledby="edit-data-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit-photo-modal">रेकर्ड परिवर्तन गर्नुहोस्</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        यस ग्राहकको सबै लेनदेन तदनुसार परिवर्तन हुनेछ ।<br>के तपाई पक्का यो कारोबार परिवर्तन गर्न चहानुहुन्छ ?<br><br>
                        <input type="text" name="edit_id" id="edit_id" class="edit_id" hidden>
                        <input type="text" name="customer_id_toedit" id="customer_id_toedit" class="customer_id_toedit" hidden>
                        <div class="input-group mb-3">
                            <label class="input-group-text text-white bg-danger" for="photo">विवरण</label>
                            <input type="text" class="form-control" id="edit_desc" name="edit_desc">
                        </div>
                        <div class="input-group mb-3">
                            <label class="input-group-text text-white bg-danger" for="photo">कारोबार रकम</label>
                            <input type="text" class="form-control" id="edit_amount" name="edit_amount" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_old_transaction" class="btn btn-warning edit_old_transaction">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ------------------------------------------------------------------------------------ -->

    <div class="modal fade" id="delete_profile" tabindex="-1" aria-labelledby="edit-data-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edelete_profile-modal">खाता मेटाउनुहोस्</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="text" name="delete_profile_id" id="delete_profile_id" class="delete_profile_id" hidden>
                        <p>डिलिट गरेमा <b><span id="delete_profile_name"></span></b> को सबै लेनदेन, खाता मेटिने छ र जम्मा लेनदेन बाट यो खाता को रकम पनि मेटिने छ <br>के तपाई पक्का यो खाता मेटाउन चाहनुहुन्छ ? </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_customer" class="btn btn-danger delete_customer">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ------------------------------------------------------------------------------------ -->

    <div class="container-fluid mt-3">
        <a href="index.php" class="btn btn-primary mb-2"><i class="bi bi-arrow-left"></i> Go Back</a>
        <button class="btn btn-success new_transaction mb-2" value="<?php echo $rows_display['id']; ?>">नयाँ कारोबार लेख्नुहोस</button>
        <button class="btn btn-primary print_estimate mb-2" value="<?php echo $rows_display['id']; ?>">एस्टिमेट प्रिन्ट गर्नुहोस्</button>
        <div class="row">
            <?php
            if ($rows_display['photo'] != "") {
            ?>
                <img class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12" style="object-fit:contain; border-radius: 25px;" src="customerphotos/<?php echo $rows_display['photo']; ?>" width="300px" height="300px">
            <?php
            } else {
            ?>
                <img class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12" style="object-fit:contain; border-radius: 25px;" src="customerphotos/default.jpg" width="300px" height="300px">
            <?php
            }
            ?>
            <div class="col-lg-6 col-xl-6 col-md-6 col-sm-12 col-12 mt-3">
                <div class="input-group mb-3">
                    <span class="input-group-text bg-primary text-white" id="basic-addon1">नाम</span>
                    <input type="text" class="form-control border-primary" name="name_displayed" id="name_displayed" value="<?php echo $rows_display['name']; ?>" aria-label="Name" aria-describedby="basic-addon1" readonly>
                    <input type="text" class="form-control border-primary" name="id_displayed" id="id_displayed" value="<?php echo $rows_display['id']; ?>" aria-label="Name" aria-describedby="basic-addon1" readonly hidden>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text bg-primary text-white" id="basic-addon1">फोन नम्बर</span>
                    <input type="text" class="form-control border-primary" id="contact_displayed" value="<?php echo $rows_display['contact']; ?>" aria-label="Name" aria-describedby="basic-addon1" readonly>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text bg-primary text-white" id="basic-addon1">ठेगाना</span>
                    <input type="text" class="form-control border-primary" id="address_displayed" value="<?php echo $rows_display['address']; ?>" aria-label="Name" aria-describedby="basic-addon1" readonly>
                </div>
                <?php
                if ($rows_display['status'] == "totake") {
                ?>
                    <div class="input-group mb-3">
                        <span class="input-group-text text-white bg-danger" id="basic-addon1"><?php echo $rows_display['name']; ?> बाट पाउनुपर्ने</span>
                        <input type="text" class="form-control border-danger" value="<?php echo $rows_display['transaction']; ?>" aria-label="Name" aria-describedby="basic-addon1" readonly>
                    </div>
                <?php
                } else if ($rows_display['status'] == "togive") {
                ?>
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-success text-white" id="basic-addon1"><?php echo $rows_display['name']; ?> लाइ दिनुपर्ने </span>
                        <input type="text" class="form-control border-success" value="<?php

                                                                                        $amount = $rows_display['transaction'] * (-1);

                                                                                        echo $amount;
                                                                                        ?>" aria-label="Name" aria-describedby="basic-addon1" readonly>
                    </div>
                <?php
                } else {
                ?>
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-warning" id="basic-addon1">पाउनुपर्ने / दिनुपर्ने</span>
                        <input type="text" class="form-control border-warning" value="<?php echo $rows_display['transaction']; ?>" aria-label="Name" aria-describedby="basic-addon1" readonly>
                    </div>
                <?php
                }
                ?>
                <div class="input-group mb-3">
                    <?php
                    if ($rows_display['additional_info'] != "") {
                    ?>
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-primary text-white" id="basic-addon1">ब्यक्तिग विवरण</span>
                            <input type="text" class="form-control border-primary" id="additional_info_displayed" value="<?php echo $rows_display['additional_info']; ?>" aria-label="Name" aria-describedby="basic-addon1" readonly>
                        </div>
                    <?php
                    } else {
                    ?>
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-primary text-white" id="basic-addon1">ब्यक्तिग विवरण</span>
                            <input type="text" class="form-control border-primary" id="additional_info_displayed" value="" aria-label="Name" aria-describedby="basic-addon1" readonly>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="modal fade" id="print_estimate" tabindex="-1" aria-labelledby="edit-data-modal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="delete_profile-modal">एस्टिमेट प्रिन्ट गर्नुहोस्</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="prepare-estimate.php" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="text" name="print_id" id="print_id" class="print_id" hidden>
                            <p>प्रिन्ट गर्ने लेनदेन को समय छान्नुहोस् |</p>

                            <input type="text" name="name" value="<?php echo $name; ?>" id="name" hidden>
                            <input type="text" name="address" value="<?php echo $print_address; ?>" id="address" hidden>

                            <div class="input-group mb-3">
                                <select class="form-select" name="from" id="from" required>
                                    <option value="" selected disabled>मिति छान्नुहोस्</option>
                                    <?php

                                    $conn = new mysqli('localhost', 'root', 'root', 'utsabbookcenter');
                                    if ($conn->connect_error) {
                                        die('Connection Failed : ' . $conn->connect_error);
                                    } else {

                                        $name_lowercase = strtolower($rows_display['name']);
                                        $name_lowercase_nospace = str_replace(" ", "", $name_lowercase);
                                        $name_lowercase_nospace_id_included = $name_lowercase_nospace . $rows_display['id'];
                                        $tablename = $name_lowercase_nospace_id_included;

                                        $sql = "select * from $tablename where status='gave' order by date desc";
                                        $sql_result = mysqli_query($conn, $sql);

                                        if ($sql_result) {
                                            while ($rows_getdate = mysqli_fetch_array($sql_result)) {

                                    ?>
                                                <option value="<?php echo $rows_getdate['date'] ?>"><?php echo $rows_getdate['date'] ?></option>
                                    <?php

                                            }
                                        }
                                    }

                                    ?>
                                </select>
                                <input type="text" name="tablename" value="<?php echo $tablename; ?>" id="name" hidden>
                                <span class="input-group-text bg-warning" id="basic-addon2">देखि</span>
                            </div>
                            <div class="input-group mb-3">
                                <select class="form-select" name="to" id="to" required>
                                    <option value="" disabled selected>मिति छान्नुहोस्</option>
                                    <?php

                                    $conn = new mysqli('localhost', 'root', 'root', 'utsabbookcenter');
                                    if ($conn->connect_error) {
                                        die('Connection Failed : ' . $conn->connect_error);
                                    } else {

                                        $name_lowercase = strtolower($rows_display['name']);
                                        $name_lowercase_nospace = str_replace(" ", "", $name_lowercase);
                                        $name_lowercase_nospace_id_included = $name_lowercase_nospace . $rows_display['id'];
                                        $tablename = $name_lowercase_nospace_id_included;

                                        $sql = "select * from $tablename where status='gave' order by date desc";
                                        $sql_result = mysqli_query($conn, $sql);

                                        if ($sql_result) {
                                            while ($rows_getdate = mysqli_fetch_array($sql_result)) {

                                    ?>
                                                <option value="<?php echo $rows_getdate['date'] ?>"><?php echo $rows_getdate['date'] ?></option>
                                    <?php

                                            }
                                        }
                                    }

                                    ?>
                                </select>
                                <span class="input-group-text text-white bg-success" id="basic-addon2">सम्म</span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="delete_customer" class="btn btn-primary delete_customer">Print</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <button class="btn btn-primary edit_photo mt-1" value="<?php echo $rows_display['id']; ?>"><i class="bi bi-camera-fill"></i> Edit Photo</button>
        <button class="btn btn-warning edit_profile mt-1"><i class="bi bi-pencil-square"></i> Edit Profile</button>
        <div class="transactions table-responsive mt-3">
            <h4 style="font-weight: bold;"><?php echo $name; ?> को हिसाब</h4>
            <table class="table table-bordered col-lg-9 col-xl-9">
                <tbody>
                    <thead class="bg-primary">
                        <tr style="color: white;">
                            <th scope="col">मिति</th>
                            <th scope="col">विवरण</th>
                            <th style="display: none;" scope="col">Amount</th>
                            <th scope="col">कारोबार विवरण</th>
                        </tr>
                    </thead>
                    <?php
                    $get_id = $rows_display['id'];
                    $name_lowercase = strtolower($name);
                    $name_lowercase_nospace = str_replace(" ", "", $name_lowercase);
                    $name_lowercase_nospace_id_included = $name_lowercase_nospace . $get_id;
                    $tablename = $name_lowercase_nospace_id_included;

                    $sql_get_transactions = "select * from `" . $tablename . "` order by id desc;";
                    $sql_get_transactions_results = mysqli_query($conn, $sql_get_transactions);

                    if ($sql_get_transactions_results) {
                        while ($rows = mysqli_fetch_array($sql_get_transactions_results)) {

                            if ($rows['status'] == "gave") {
                    ?>
                                <tr class="">
                                    <td><?php echo $rows['date']; ?></td>
                                    <td><?php echo $rows['description']; ?></td>
                                    <td style="display:none;"><?php echo $rows['amount']; ?></td>
                                    <td style="display:flex; flex-direction:row; align-items:center;">
                                        <input type="text" id="amount_hidden" value="<?php echo $rows['amount']; ?>" hidden>
                                        <button value="<?php echo $rows_display['id']; ?>" class="btn btn-danger">दिएको रकम रू : <?php echo $rows['amount']; ?></button>
                                        <button class="btn btn-danger delete_transaction_record" value="<?php echo $rows['id']; ?>" style="margin-left:10px;"><i class="bi bi-trash3-fill"></i></button>
                                        <button class="btn btn-warning edit_transaction_record" value="<?php echo $rows['id']; ?>" style="margin-left:10px;">Edit</button>
                                    </td>
                                </tr> <?php
                                    } else {

                                        ?>

                                <tr class="">
                                    <td><?php echo $rows['date']; ?></td>
                                    <td><?php echo $rows['description']; ?></td>
                                    <td style="display:none;"><?php echo $rows['amount']; ?></td>
                                    <td style="display:flex; flex-direction:row; align-items:center;">
                                        <input type="text" id="amount_hidden" value="<?php echo $rows['amount']; ?>" hidden>
                                        <button value="<?php echo $rows_display['id']; ?>" class="btn btn-success">पाएको रकम रू : <?php echo $rows['amount']; ?></button>
                                        <button class="btn btn-danger delete_transaction_record" value="<?php echo $rows['id']; ?>" style="margin-left:10px;"><i class="bi bi-trash3-fill"></i></button>
                                        <button class="btn btn-warning edit_transaction_record" value="<?php echo $rows['id']; ?>" style="margin-left:10px;">Edit</button>
                                    </td>
                                </tr>

                    <?php
                                    }
                                }
                            }
                    ?>


                </tbody>
            </table>
            <button class="mb-2 btn btn-danger delete_profile" value="<?php echo $rows_display['id']; ?>"><i class="bi bi-trash3-fill"></i> Delete Account</button>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {

            document.addEventListener("keydown", function (event) {
                if (event.ctrlKey && event.key === "Enter") {
                    event.preventDefault();
                    $('#new-transaction').modal('show');
                var customer_id = $(this).val();

                var name = document.getElementById('name_displayed').value;

                $('#name').val(name);
                $('#customer_id').val(customer_id);
                }
            });

            $('.new_transaction').click(function(e) {

                $('#new-transaction').modal('show');
                var customer_id = $(this).val();

                var name = document.getElementById('name_displayed').value;

                $('#name').val(name);
                $('#customer_id').val(customer_id);
            });
        });

        $(document).ready(function() {
            $('.delete_transaction_record').click(function(e) {

                var customer_id_todelete = document.getElementById('id_displayed').value;

                $('#delete-transaction').modal('show');
                var delete_id = $(this).val();

                $delete_details = $(this).closest('tr');
                var data = $delete_details.children("td").map(function() {
                    return $(this).text();
                }).get();

                $('#delete_id').val(delete_id);
                $('#customer_id_todelete').val(customer_id_todelete);
                $('#delete_desc').val(data[1]);
                $('#delete_amount').val(data[2]);
            });
        });

        $(document).ready(function() {
            $('.edit_transaction_record').click(function(e) {

                var customer_id_todelete = document.getElementById('id_displayed').value;

                $('#edit-transaction').modal('show');
                var delete_id = $(this).val();

                $delete_details = $(this).closest('tr');
                var data = $delete_details.children("td").map(function() {
                    return $(this).text();
                }).get();

                $('#edit_id').val(delete_id);
                $('#customer_id_toedit').val(customer_id_todelete);
                $('#edit_desc').val(data[1]);
                $('#edit_amount').val(data[2]);
            });
        });

        $(document).ready(function() {
            $('.edit_photo').click(function(e) {

                $('#edit_photo').modal('show');

                var edit_id = document.getElementById('id_displayed').value;
                var edit_photo_name = document.getElementById('name_displayed').value;

                $('#edit_photo_id').val(edit_id);
                $('#edit_photo_name').val(edit_photo_name);
            });
        });

        $(document).ready(function() {
            $('.delete_profile').click(function(e) {

                $('#delete_profile').modal('show');

                var delete_id = document.getElementById('id_displayed').value;
                var delete_account_name = document.getElementById('name_displayed').value;

                $('#delete_profile_id').val(delete_id);
                $('#delete_profile_name').text(delete_account_name);
            });
        });

        $(document).ready(function() {
            $('.print_estimate').click(function(e) {

                $('#print_estimate').modal('show');

                var print_id = document.getElementById('id_displayed').value;

                $('#print_id').val(print_id);
            });
        });

        $(document).ready(function() {
            $('.edit_profile').click(function(e) {

                $('#edit_profile').modal('show');

                var id = document.getElementById("id_displayed").value;
                var name = document.getElementById("name_displayed").value;
                var oldname = document.getElementById("name_displayed").value;
                var address = document.getElementById("address_displayed").value;
                var contact = document.getElementById("contact_displayed").value;
                var additional_info = document.getElementById("additional_info_displayed").value;

                $('#edit_profile_id').val(id);
                $('#edit_profile_name').val(name);
                $('#edit_profile_oldname').val(oldname);
                $('#edit_profile_address').val(address);
                $('#edit_profile_contact').val(contact);
                $('#edit_profile_additional_info').val(additional_info);

            });
        });
    </script>
</body>

</html>