<?php

include("checklogin.php");
if(isset($_POST['id'])){
    $id = $_POST['id'];
    $oldquantity = $_POST['oldquantity'];
    $arrivedquantity = $_POST['arrivedquantity'];

    include("conn.php");

    $newquantity = $oldquantity + $arrivedquantity ;
    $updateStock = "update stocks set quantity='$newquantity' where id='$id'";
    $isStockUpdated = mysqli_query($IndividualDatabase, $updateStock);

    if ($isStockUpdated) {
        $_SESSION['toasttitle'] = "<span class='text-success'><i class='bi bi-check-circle-fill'></i></span> <span class='text-success'>Operation Successful</span>";
        $_SESSION['toastmessage'] = "Quantity Added";
        echo "success";
    } else {
        $_SESSION['toasttitle'] = "<span class='text-warning'><i class='bi bi-exclamation-triangle-fill'></i></span> <span class='text-warning'>Operation Cancelled</span>";
        $_SESSION['toastmessage'] = "Unknown error occured.";
        header("Location: index.php?p=$id");
    }
}else{
    header("Location: index.php");
}