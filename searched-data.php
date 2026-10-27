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

    if (isset($_POST['submit-search'])) {
        $search_content = $_POST['searchcontent'] ;
    }else{
        header("Location: index.php");
    }

    $sql_get_total_amount_togive = "select transaction from customers where status='togive';";
    $sql_get_total_amount_togive_result = mysqli_query($conn,$sql_get_total_amount_togive);

    if($sql_get_total_amount_togive_result){
        $amounttogive = 0;
        while($rows_amount_togive=mysqli_fetch_array($sql_get_total_amount_togive_result)){
            $amounttogive = $amounttogive + $rows_amount_togive['transaction'];
        }
        $amounttogive = $amounttogive * (-1);
    }

    $sql_get_total_amount_totake = "select transaction from customers where status='totake';";
    $sql_get_total_amount_totake_result = mysqli_query($conn,$sql_get_total_amount_totake);

    if($sql_get_total_amount_totake_result){
        $amounttotake = 0;
        while($rows_amount_totake=mysqli_fetch_array($sql_get_total_amount_totake_result)){
            $amounttotake = $amounttotake + $rows_amount_totake['transaction'];
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

        $name_lowercase = strtolower($name);
        $name_lowercase_nospace = str_replace(" ", "", $name_lowercase);
        $name_lowercase_nospace_id_included = $name_lowercase_nospace . $id;
        $tablename = $name_lowercase_nospace_id_included;

        if($gave=="" && $took==""){
            $_SESSION['errormessage'] = " Cannot accept null values";
            header("Location: index.php");
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
        
        if($gave != "" && $took != "")
        {
            $sql_transaction_history_write = "insert into `" . $tablename . "`(status,amount,date,description) values('gave','$gave','$date','$description')";
            $sql_transaction_history_write_result = mysqli_query($conn, $sql_transaction_history_write);
            $sql_transaction_history_write2 = "insert into `" . $tablename . "`(status,amount,date,description) values('took','$took','$date','$description')";
            $sql_transaction_history_write_result2 = mysqli_query($conn, $sql_transaction_history_write2);

            $both_not_null = $sql_transaction_history_write_result  && $sql_transaction_history_write_result2 ;
        }
        
        $transaction = $gave - $took;

        $sql_transaction_read = "select transaction from customers where id = '$id'";
        $sql_transaction_read_result = mysqli_query($conn, $sql_transaction_read);
        $rows = mysqli_fetch_array($sql_transaction_read_result);

        $old_amount = $rows['transaction'];

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

        $sql_transaction_write = "update customers set transaction='$new_amount' where id = '$id'";
        $sql_transaction_write_result = mysqli_query($conn, $sql_transaction_write);

        if ($sql_transaction_write_result && $sql_status_write_result && ($sql_transaction_history_write_result || $both_not_null)) {
            $_SESSION['successmessage'] = " Transaction recorded successfully";
            header("Location: index.php");
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

    <div class="modal fade" id="confirm-logout" tabindex="-1">
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
                            <span class="input-group-text text-white bg-danger" id="basic-addon1">सामान बापत पाउनुपर्ने रकम रू</span>
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

    <!-- -------------------------------------------------------------------------- -->

    <div class="container-fluid">
        <div class="row">
            <div class="col" style="margin-top: 15px">
                <a href="newaccount.php" class="btn btn-primary">नयाँ खाता खोल्नुहोस </a>
            </div>
            <div class="col" style="margin-top: 15px">
                <button class="btn btn-danger">जम्मा पाउने : <?php echo $amounttotake; ?></button>
            </div>
            <div class="col" style="margin-top: 15px">
                <button class="btn btn-success">जम्मा दिनुपर्ने : <?php echo $amounttogive; ?></button>
            </div>
            <div class=" col-lg-6 col-md-6 col-xl-6 col-sm-12 col-12 text-end" style="margin-top: 15px;">
                <form action="searched-data.php" method="post">
                    <div class="input-group">
                        <input type="text" class="form-control border-primary" name="searchcontent" placeholder="Search Box" aria-label="searchbox">
                        <button class="btn btn-outline-primary" type="submit" name="submit-search"><i class="bi bi-search"></i> खोज्नुहोस</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include("message.php") ?>

    <div class="container-fluid mt-2 table-responsive">
        <table class="table">
            <thead class="text-start" style="background-color: #0d6efd; color: white;">
                <tr>
                    <th>नाम</th>
                    <th class="text-center">कारोबार</th>
                </tr>
            </thead>
            <tbody class="text-start">
                <?php
                $conn = new mysqli('localhost', 'root', 'root', 'utsabbookcenter');
                if ($conn->connect_error) {
                    die('Connection Failed : ' . $conn->connect_error);
                } else {
                    // $sql = "select * from customers order by id desc";
                    $sql = "select * from customers where name like '%".$search_content."%' ";
                    $result = mysqli_query($conn, $sql);
                }
                while ($rows = mysqli_fetch_array($result)) {
                ?>
                    <tr>
                        <?php
                        if ($rows['photo'] == null) {
                        ?>
                            <td><a href="profile.php?id=<?php echo $rows['id'] ?>" style="text-decoration: none; color:black;"><img src="customerphotos/default.jpg" height="38px" width="38px"> <?php echo $rows['name']; ?></a></td>
                        <?php
                        } else {
                        ?>
                            <td><a href="profile.php?id=<?php echo $rows['id'] ?>" style="text-decoration: none; color:black;"><img style="border-radius:50% ;" src="customerphotos/<?php echo $rows['photo'] ?>" height="38px" width="38px"> <?php echo $rows['name']; ?></a></td>
                        <?php
                        }
                        ?>
                        <?php
                        if ($rows['status'] == "totake") {
                        ?>
                            <td class="text-center"><button class="btn btn-danger new_transaction" value="<?php echo $rows['id']; ?>"><?php echo $rows['name']; ?> बाट पाउनुपर्ने : <?php echo $rows['transaction']; ?></button></td>
                        <?php
                        } else if ($rows['status'] == "togive") {
                        ?>
                            <td class="text-center"><button class="btn btn-success new_transaction" value="<?php echo $rows['id']; ?>"><?php echo $rows['name']; ?> लाई दिनुपर्ने : <?php

                                                                                                                                                    $amount = $rows['transaction'] * (-1);

                                                                                                                                                    echo $amount;
                                                                                                                                                    ?></button></td>
                        <?php
                        } else {
                        ?>
                            <td class="text-center"><button class="btn btn-primary new_transaction" value="<?php echo $rows['id']; ?>">सबै लेनदेन स्पष्ट छन् । नयाँ कारोबार दर्ता गर्न यहाँ थिच्नुहोस ।</button></td>
                        <?php
                        }
                        ?>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.logout').click(function(e) {
                e.preventDefault();

                $('#confirm-logout').modal('show');

            });
        });

        $(document).ready(function() {
            $('.new_transaction').click(function(e) {

                $('#new-transaction').modal('show');
                var customer_id = $(this).val();

                $tr = $(this).closest('tr');
                var data = $tr.children("td").map(function() {
                    return $(this).text();
                }).get();

                $('#name').val(data[0]);
                $('#customer_id').val(customer_id);
            });
        });
    </script>
</body>

</html>