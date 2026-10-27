<?php
include("conn.php");
include("checklogin.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@600&family=Oswald:wght@700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stocks</title>
    <link rel="icon" type="image/x-icon" href="../images/stock.png">
    <style>
        #addItemTag:hover {
            color: white;
            transition: 0.1s;
            cursor: pointer;
        }

        .fixed-btn2 {
            position: fixed;
            bottom: 1%;
            right: 15px;
            cursor: pointer;
        }
    </style>
</head>

<body style="background-color:#ffd50069;">



    <?php

    include("stocksnav.php");

    ?>

    <div class="mt-2 container-fluid" style="font-family: 'Geologica', sans-serif;">

        <?php
        if (isset($_SESSION['toasttitle'])) {
            ?>
            <div class="position-fixed top-1 end-0 p-1" style="z-index: 11;">
                <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">
                            <?php echo $_SESSION['toasttitle']; ?>
                        </strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        <?php echo $_SESSION['toastmessage']; ?>
                    </div>
                </div>
            </div>

            <?php
            unset($_SESSION['toasttitle']);
            unset($_SESSION['toastmessage']);
        }
        ?>

        <?php

        $sql_countItems = mysqli_query($IndividualDatabase, "select name from stocks;");
        if ($sql_countItems) {
            $totalItems = mysqli_num_rows($sql_countItems);
        }

        $query_getStocksToDisplay = "select * from stocks order by id desc limit 25;";
        $areStocksGotToDisplay = mysqli_query($IndividualDatabase, $query_getStocksToDisplay);

        if ($areStocksGotToDisplay) {

            if (mysqli_num_rows($areStocksGotToDisplay) > 0) {

                ?>
                <div>
                    <a href="additem.php" class="col btn btn-primary text-white">नयाँ सामान दर्ता</a>
                    <button class="btn col btn-primary">कुल प्रकार : <?php echo $totalItems; ?></button>
                </div>
                <h6 style="font-family:'Geologica', sans-serif; text-transform: capitalize; display:flex; justify-content:space-between; align-items:center;"
                    class="bg-primary text-white pt-2 pe-2 pb-2 ps-2 mt-2">
                    <div style="display:flex; flex-direction: row; justify-content:center; align-items:center;">
                        <label for="searchbox">खोज्नुहोस</label>
                        <input type="text" style="width:200px; font-size:small; margin-left:10px;" id="searchbox"
                            class="form-control" autofocus placeholder="नाम अथवा सिम्बोल">
                    </div>
                </h6>
                <div id="search-results" style="display:none;"></div>
                <div id="all-stocks" style="display:block;">
                    <?php

                    while ($rows_Stocks = mysqli_fetch_array($areStocksGotToDisplay)) {
                        ?>

                        <a style="text-decoration: none;" href="details.php?p=<?php echo $rows_Stocks['id']; ?>">
                            <div style="height: 80px; border-radius: 2px; background-color: #b8c8ec;" class="mb-1">
                                <div class="customer-image row">
                                    <i style="font-size:40px;"
                                        class="bi bi-box-seam-fill text-<?php echo $primarycolor ?> col-3 col-sm-2 mt-2 col-md-1 col-lg-1 col-xl-1 ms-3"></i>
                                    <p style="text-transform:capitalize; height: 60px; color: black; margin-left: -40px; margin-top: 5px; padding-top: 5px; font-size: 15px;"
                                        class="col-8">
                                        <?php echo $rows_Stocks['name']; ?>

                                        <?php

                                        if ($rows_Stocks['symbol'] != '') {
                                            echo "[ " . $rows_Stocks['symbol'] . " ]";
                                        } else {

                                        }

                                        ?>

                                        <br><span style="font-size: 17px; color: blue;">
                                            बेच्ने दाम :
                                            <?php echo $rows_Stocks['sellingprice']; ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </a>
                        <div style="margin-top: -10px; margin-bottom: -10px;">
                            <hr>
                        </div>
                        <?php
                    } ?>
                </div>
                <?php
            } else {
                ?>
                <a href="additem.php" style="color:black; text-decoration:none;">
                    <div id="addItemTag" style="display:flex; justify-content:center; align-items:center;"
                        class="bg-primary text-white pt-2 pb-2">नयां समान दर्ता&nbsp;<i class="bi bi-bag-plus-fill"></i></div>
                </a>
                <?php
            }
        }

        ?>

    </div>


    <script src="jquery/jquery-3.6.1.min.js"></script>
    <script>
        $(document).ready(function () {

            document.addEventListener("keydown", function (event) {
                if (event.ctrlKey && event.key === "Enter") {
                    event.preventDefault();
                    window.location.assign("additem.php");
                }
            });

            $('#searchbox').keyup(function () {
                document.getElementById("all-stocks").style.display = "none";
                document.getElementById("search-results").style.display = "";

                let query = $(this).val();

                if (query !== '') {
                    $.ajax({
                        url: 'search.php',
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
                    document.getElementById("all-stocks").style.display = "block";
                }
            });
        });

        var toastDiv = document.getElementById('toast')
        var toast = new bootstrap.Toast(toastDiv);
        toast.show()
    </script>
</body>

</html>