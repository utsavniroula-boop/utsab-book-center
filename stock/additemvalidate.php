<?php
include("conn.php");
include("checklogin.php");

$id = uniqid("item-", true);
$name = $_POST['name'];
$symbol = $_POST['symbol'];
$quantity = $_POST['quantity'];
$cost = $_POST['cost'];
$sell = $_POST['sell'];
$final = $_POST['final'];


if (isset($_POST['photocheck'])) {

    $query_addItem = "insert into stocks(id,name,symbol,quantity,image,costprice,sellingprice,finalprice) values('$id','$name','$symbol','$quantity','','$cost','$sell','$final')";
    $isItemAdded = mysqli_query($IndividualDatabase, $query_addItem);

    if ($isItemAdded) {
                $_SESSION['toasttitle'] = "<span class='text-success'><i class='bi bi-check-circle-fill'></i></span> <span class='text-success'>Operation   Successful</span>";
                $_SESSION['toastmessage'] = "Item added successfully.";
        echo "success";
    } else {
        echo "Some unknown error occured.";
    }
} else {

    if (isset($_FILES['photo'])) {

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

            $query_addItem = "insert into stocks(id,name,symbol,quantity,image,costprice,sellingprice,finalprice) values('$id','$name','$symbol','$quantity','$new_img_name','$cost','$sell','$final')";
            $isItemAdded = mysqli_query($IndividualDatabase, $query_addItem);

            if ($isItemAdded) {
                $_SESSION['toasttitle'] = "<span class='text-success'><i class='bi bi-check-circle-fill'></i></span> <span class='text-success'>Operation   Successful</span>";
                $_SESSION['toastmessage'] = "$name added successfully.";
                echo "success";
            } else {
                echo "Some unknown error occured.";
            }
        } else {
            echo "invalidextension";
        }
    }
}
