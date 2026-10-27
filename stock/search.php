<?php
include ('conn.php');
include ("checklogin.php");
if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $output = '';

    $sql = "SELECT id,name,symbol,sellingprice FROM stocks WHERE name LIKE '%$query%' OR symbol LIKE '%$query%'";
    $result = mysqli_query($IndividualDatabase, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row["symbol"] != '') {

                $output .= '<a style="text-decoration: none;" href="details.php?p=' . $row['id'] . '">
                <div style="height: 80px; border-radius: 2px; background-color: #b8c8ec;" class="mb-1">
                <div class="customer-image row">
                <i style="font-size:40px;" class="bi bi-box-seam-fill text-primary col-3 col-sm-2 mt-2 col-md-1 col-lg-1 col-xl-1 ms-3"></i>
                <p style="height: 60px; text-transform:capitalize; color: black; margin-left: -40px; margin-top: 5px; padding-top: 5px; font-size: 15px;" class="col-8">' . $row['name'] . ' [ ' . $row["symbol"] . ' ]<br><span style="font-size: 17px; color: blue;">बेच्ने दाम : ' . $row['sellingprice'] . '</span></p>
                </div>
                </div>
                </a>
                <div style="margin-top: -10px; margin-bottom: -10px;">
                <hr>
                </div>';
            } else {
                $output .= '<a style="text-decoration: none;" href="details.php?p=' . $row['id'] . '">
                <div style="height: 80px; border-radius: 2px; background-color: #b8c8ec;" class="mb-1">
                <div class="customer-image row">
                <i style="font-size:40px;" class="bi bi-box-seam-fill text-primary col-3 col-sm-2 mt-2 col-md-1 col-lg-1 col-xl-1 ms-3"></i>
                <p style="height: 60px; text-transform:capitalize; color: black; margin-left: -40px; margin-top: 5px; padding-top: 5px; font-size: 15px;" class="col-8">' . $row['name'] .'<br><span style="font-size: 17px; color: blue;">बेच्ने दाम : ' . $row['sellingprice'] . '</span></p>
                </div>
                </div>
                </a>
                <div style="margin-top: -10px; margin-bottom: -10px;">
                <hr>
                </div>';
            }
        }
    } else {
        $output .= '<p>No stocks found</p>';
    }

    echo $output;
}
