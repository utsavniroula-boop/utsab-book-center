<?php
include("conn.php");
include("checklogin.php");

if (isset($_POST['itemid'])) {
    if ($_POST['itemid'] != "") {

        $photo = $_POST['photo'];
        $itemid = $_POST['itemid'];

        $deleteItem = mysqli_query($IndividualDatabase, "delete from stocks where id='$itemid';");

        if ($deleteItem) {
            if ($photo != "") {
                unlink("files/" . $photo);
            }
            $_SESSION['toasttitle'] = "<span class='text-danger'><i class='bi bi-trash3-fill'></i></span> <span class='text-danger'>Operation Successful</span>";
            $_SESSION['toastmessage'] = "Item Deleted Successfully.";
            echo "success";
        } else {
            $_SESSION['toasttitle'] = "<span class='text-warning'><i class='bi bi-exclamation-triangle-fill'></i></span> <span class='text-warning'>Operation Cancelled</span>";
            $_SESSION['toastmessage'] = "Unknown error occured.";
            echo "Cannot delete at this moment.";
        }
    } else {
        header("Location: index.php");
    }
} else {
    header("Location: index.php");
}
