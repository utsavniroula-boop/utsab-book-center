<?php

include("checklogin.php");
$id = $_POST['id'];
$name = $_POST['name'];
$symbol = $_POST['symbol'];
$quantity = $_POST['quantity'];
$sellingprice = $_POST['sellingprice'];
$costprice = $_POST['costprice'];
$finalprice = $_POST['finalprice'];

$oldimage = $_POST['oldimage'];

include("conn.php");

if (isset($_POST['photocheck'])) {

    $updateStock = "update stocks set name='$name',symbol='$symbol',quantity='$quantity',sellingprice='$sellingprice',costprice='$costprice',finalprice='$finalprice' where id='$id'";
    $isStockUpdated = mysqli_query($IndividualDatabase, $updateStock);

    if ($isStockUpdated) {
                $_SESSION['toasttitle'] = "<span class='text-success'><i class='bi bi-check-circle-fill'></i></span> <span class='text-success'>Operation Successful</span>";
                $_SESSION['toastmessage'] = "Item Info Updated";
                echo "success";
            } else {
                $_SESSION['toasttitle'] = "<span class='text-warning'><i class='bi bi-exclamation-triangle-fill'></i></span> <span class='text-warning'>Operation Cancelled</span>";
                $_SESSION['toastmessage'] = "Unknown error occured.";
                header("Location: index.php?p=$id");
            }
} else {

    if (isset($_FILES['photo'])) {

        if($oldimage != ""){
            unlink('files/' . $oldimage);
        }

        $imagename = $_FILES['photo']['name'];
        $imagesize = $_FILES['photo']['size'];
        $tmp_name = $_FILES['photo']['tmp_name'];

        $img_ex = pathinfo($imagename, PATHINFO_EXTENSION);
        $img_ex_lc = strtolower($img_ex);

        $allowed_ex = array("jpg","jpeg", "png");

        if (in_array($img_ex_lc, $allowed_ex)) {

            $new_img_name = uniqid("ITEM-" . "-", true) . '.' . $img_ex_lc;
            $upload_path = 'files/' . $new_img_name;
            move_uploaded_file($tmp_name, $upload_path);

            $updateStock = "update stocks set name='$name',symbol='$symbol',quantity='$quantity',image='$new_img_name',sellingprice='$sellingprice',costprice='$costprice',finalprice='$finalprice' where id='$id'";
            $isStockUpdated = mysqli_query($IndividualDatabase, $updateStock);

            if ($isStockUpdated) {
                $_SESSION['toasttitle'] = "<span class='text-success'><i class='bi bi-check-circle-fill'></i></span> <span class='text-success'>Operation Successful</span>";
                $_SESSION['toastmessage'] = "Item Info Updated";
                echo "success";
            } else {
                $_SESSION['toasttitle'] = "<span class='text-warning'><i class='bi bi-exclamation-triangle-fill'></i></span> <span class='text-warning'>Operation Cancelled</span>";
                $_SESSION['toastmessage'] = "Unknown error occured.";
                header("Location: index.php?p=$id");
            }
        } else {
            echo "invalidextension";
        }
    }
}
