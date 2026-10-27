<?php
include ('conn.php');
include ("stock/checklogin.php");
if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $output = '';

    $sql = "SELECT name,id,photo,status,transaction FROM customers WHERE name LIKE '%$query%' OR contact LIKE '%$query%'";
    $result = mysqli_query($IndividualDatabase, $sql);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                if ($row['photo'] != '') {
                    if ($row['status'] == 'totake') {
                        $output .= '
                        <a style="text-decoration: none;" href="profile.php?id=' . $row['id'] . '">
                            <div style="height: 80px; border-radius: 2px; background-color: #ffb3b9;" class="mb-1">
                                <div class="customer-image row">
                                    <img style="width:61.53px; height:80px; padding:5px; border-radius:10px; margin-left:15px;" src="customerphotos/' . $row['photo'] . '">
                                    <p style="text-transform:capitalize; height: 60px; color: black; margin-top: 5px; margin-left:0px; padding-top: 10px; font-size: 17px;"
                                        class="col-8">
                                        <b>' . $row['name'] . '</b>
                                        <br><span style="font-size: 17px; font-weight: bold; color: red;">
                                            बाट रु.' . $row['transaction'] . ' पाउने 
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </a><hr style="margin-top:5px; margin-bottom:5px;">
                    ';
                    } else if ($row['status'] == 'togive') {
                        $output .= '
                        <a style="text-decoration: none;" href="profile.php?id=' . $row['id'] . '">
                            <div style="height: 80px; border-radius: 2px; background-color: #b3ffb6;" class="mb-1">
                                <div class="customer-image row">
                                    <img style="width:61.53px; height:80px; padding:5px; border-radius:10px; margin-left:15px;" src="customerphotos/' . $row['photo'] . '">
                                    <p style="text-transform:capitalize; height: 60px; color: black; margin-top: 5px; margin-left:0px; padding-top: 10px; font-size: 17px;"
                                        class="col-8">
                                        <b>' . $row['name'] . '</b>
                                        <br><span style="font-size: 17px; font-weight: bold; color: green;">
                                            लाई रु.' . $row['transaction'] * -1 . ' दिनुपर्ने
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </a><hr style="margin-top:5px; margin-bottom:5px;">
                    ';
                    } else {
                        $output .= '
                        <a style="text-decoration: none;" href="profile.php?id=' . $row['id'] . '">
                            <div style="height: 80px; border-radius: 2px; background-color: #b3b3ff;" class="mb-1">
                                <div class="customer-image row">
                                <img style="width:61.53px; height:80px; padding:5px; border-radius:10px; margin-left:15px;" src="customerphotos/' . $row['photo'] . '">
                                    <p style="text-transform:capitalize; height: 60px; color: black; margin-top: 5px; margin-left:0px; padding-top: 10px; font-size: 17px;"
                                        class="col-8">
                                        <b>' . $row['name'] . '</b>
                                        <br><span style="font-size: 17px; font-weight: bold; color: blue;">
                                        उधारो / जम्मा छैन
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </a><hr style="margin-top:5px; margin-bottom:5px;">
                    ';
                    }
                } else {
                    if ($row['status'] == 'totake') {
                        $output .= '
                        <a style="text-decoration: none;" href="profile.php?id=' . $row['id'] . '">
                            <div style="height: 80px; border-radius: 2px; background-color: #ffb3b9;" class="mb-1">
                                <div class="customer-image row">
                                    <img style="width:61.53px; height:80px; padding:5px; border-radius:10px; margin-left:15px;" src="customerphotos/default.jpg">
                                    <p style="text-transform:capitalize; height: 60px; color: black; margin-top: 5px; margin-left:0px; padding-top: 10px; font-size: 17px;"
                                        class="col-8">
                                        <b>' . $row['name'] . '</b>
                                        <br><span style="font-size: 17px; font-weight: bold; color: red;">
                                            बाट रु.' . $row['transaction'] . ' पाउने 
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </a><hr style="margin-top:5px; margin-bottom:5px;">
                    ';
                    } else if ($row['status'] == 'togive') {
                        $output .= '
                        <a style="text-decoration: none;" href="profile.php?id=' . $row['id'] . '">
                            <div style="height: 80px; border-radius: 2px; background-color: #b3ffb6;" class="mb-1">
                                <div class="customer-image row">
                                <img style="width:61.53px; height:80px; padding:5px; border-radius:10px; margin-left:15px;" src="customerphotos/default.jpg">
                                    <p style="text-transform:capitalize; height: 60px; color: black; margin-left:0px; padding-top: 10px; font-size: 17px;"
                                        class="col-8">
                                        <b>' . $row['name'] . '</b>
                                        <br><span style="font-size: 17px; font-weight: bold; color: green;">
                                            लाई रु.' . $row['transaction'] * -1 . ' दिनुपर्ने
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </a><hr style="margin-top:5px; margin-bottom:5px;">
                    ';
                    } else {
                        $output .= '
                        <a style="text-decoration: none;" href="profile.php?id=' . $row['id'] . '">
                            <div style="height: 80px; border-radius: 2px; background-color: #b3b3ff;" class="mb-1">
                                <div class="customer-image row">
                                <img style="width:61.53px; height:80px; padding:5px; border-radius:10px; margin-left:15px;" src="customerphotos/default.jpg">
                                    <p style="text-transform:capitalize; height: 60px; color: black; margin-left:0px; padding-top: 10px; font-size: 17px;"
                                        class="col-8">
                                        <b>' . $row['name'] . '</b>
                                        <br><span style="font-size: 17px; font-weight: bold; color: blue;">
                                        उधारो / जम्मा छैन
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </a><hr style="margin-top:5px; margin-bottom:5px;">
                    ';
                    }
                }
            }
        } else {
            $output .= '<h6 class="mt-2">No customers found</h6>';
        }
    } else {
        $output .= '<h6 class="mt-2">No customers found</h6>';
    }

    echo $output;
}
