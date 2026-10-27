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

    $sql_get_total_amount_togive = "select transaction from customers where status='togive';";
    $sql_get_total_amount_togive_result = mysqli_query($conn, $sql_get_total_amount_togive);

    if ($sql_get_total_amount_togive_result) {
        $amounttogive = 0;
        while ($rows_amount_togive = mysqli_fetch_array($sql_get_total_amount_togive_result)) {
            $amounttogive = $amounttogive + $rows_amount_togive['transaction'];
        }
        $amounttogive = $amounttogive * (-1);
    }

    $sql_get_total_amount_totake = "select transaction from customers where status='totake';";
    $sql_get_total_amount_totake_result = mysqli_query($conn, $sql_get_total_amount_totake);

    if ($sql_get_total_amount_totake_result) {
        $amounttotake = 0;
        while ($rows_amount_totake = mysqli_fetch_array($sql_get_total_amount_totake_result)) {
            $amounttotake = $amounttotake + $rows_amount_totake['transaction'];
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts</title>
</head>

<body>
    <?php include ("navbar.php") ?>

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
                        <input type="text" autofocus class="form-control border-primary" name="searchcontent"
                            placeholder="Search Box" id="searchbox" aria-label="searchbox">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include ("message.php") ?>

    <div class="container-fluid mt-2">
        <div class="bg-primary w-100 mb-2 p-2 text-white" style="font-size:20px;">
            <b id="totalAccountShow"></b>
        </div>

        <div id="search-results" style="display:none;">

        </div>
        <div id="all-accounts" class="text-start">

            <?php
            $conn = new mysqli('localhost', 'root', 'root', 'utsabbookcenter');
            if ($conn->connect_error) {
                die('Connection Failed : ' . $conn->connect_error);
            } else {
                $sql = "select id,name,transaction,status from customers where status <> 'clear' order by (transaction+0) desc";
                $result = mysqli_query($conn, $sql);

                $result2 = mysqli_query($conn, "select count(id) as numberOfAccounts from customers;");
                if ($result2) {
                    while ($row2 = mysqli_fetch_array($result2)) {
                        $totalAccounts = $row2['numberOfAccounts'];
                    }
                }
                ?>
                <span id="hiddenTotalAccounts" style="display:none;"><?php echo $totalAccounts; ?></span>
                <?php
            }
            while ($rows = mysqli_fetch_array($result)) {
                ?>
                <tr>
                    <?php
                    if ($rows['status'] == 'totake') {
                        ?>
                        <td>
                            <a style="text-decoration: none;" href="profile.php?id=<?php echo $rows['id']; ?>">
                                <div style="height: 80px; border-radius: 2px; background-color: #ffb3b9;" class="mb-1">
                                    <div class="customer-image row">
                                        <p style="text-transform:capitalize; height: 60px; color: black; margin-top: 5px; margin-left:15px; padding-top: 10px; font-size: 17px;"
                                            class="col-8">
                                            <b><?php echo $rows['name']; ?></b>
                                            <br><span style="font-size: 17px; font-weight: bold; color: red;">
                                                बाट रु. <?php echo $rows['transaction']; ?> पाउने
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </td>
                        <?php
                    } else if ($rows['status'] == 'togive') {
                        ?>
                            <td>
                                <a style="text-decoration: none;" href="profile.php?id=<?php echo $rows['id']; ?>">
                                    <div style="height: 80px; border-radius: 2px; background-color: #b3ffb6;" class="mb-1">
                                        <div class="customer-image row">
                                            <p style="text-transform:capitalize; height: 60px; color: black; margin-top: 5px; margin-left:15px; padding-top: 10px; font-size: 17px;"
                                                class="col-8">
                                                <b><?php echo $rows['name']; ?></b>
                                                <br><span style="font-size: 17px; font-weight: bold; color: green;">
                                                    लाई रु. <?php echo $rows['transaction'] * -1; ?> दिनुपर्ने
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </td>
                        <?php
                    } else {
                        ?>
                            <td>
                                <a style="text-decoration: none;" href="profile.php?id=<?php echo $rows['id']; ?>">
                                    <div style="height: 80px; border-radius: 2px; background-color: #b3b3ff;" class="mb-1">
                                        <div class="customer-image row">
                                            <p style="text-transform:capitalize; height: 60px; color: black; margin-top: 5px; margin-left:15px; padding-top: 10px; font-size: 17px;"
                                                class="col-8">
                                                <b><?php echo $rows['name']; ?></b>
                                                <br><span style="font-size: 17px; font-weight: bold; color: blue;">
                                                    उधारो / जम्मा छैन
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </td>
                        <?php
                    }
                    ?>
                </tr>
                <hr style="margin-top:5px; margin-bottom:5px;">
                <?php
            }
            ?>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="jquery/jquery-3.6.1.min.js"></script>
    <script>
        $(document).ready(function () {

            var numberOfAccounts = $('#hiddenTotalAccounts').html();
            $('#totalAccountShow').html("सबै खाताहरु : " + numberOfAccounts+" जना");

            document.addEventListener("keydown", function (event) {
                if (event.ctrlKey && event.key === "Enter") {
                    event.preventDefault();
                    window.location.assign("newaccount.php");
                }
            });

            $('#searchbox').keyup(function () {
                document.getElementById("all-accounts").style.display = "none";
                document.getElementById("search-results").style.display = "";

                let query = $(this).val();

                console.log(query);

                if (query !== '') {
                    $.ajax({
                        url: 'searchaccounts.php',
                        method: 'POST',
                        data: {
                            query: query
                        },
                        success: function (data) {
                            $('#search-results').html(data);
                        }
                    });
                } else {
                    $('#search-results').html('');
                    document.getElementById("all-accounts").style.display = "";
                }
            });

            $('.logout').click(function (e) {
                e.preventDefault();

                $('#confirm-logout').modal('show');

            });

        });
    </script>
</body>

</html>