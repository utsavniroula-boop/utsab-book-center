<?php

include("conn.php");
include("checklogin.php");
if (isset($_GET['p'])) {
    if ($_GET['p'] != "") {

        $id = $_GET['p'];
        $getStockDetails = mysqli_query($IndividualDatabase, "select * from stocks where id='$id';");
        if ($getStockDetails) {
            while ($rows_StockDetails = mysqli_fetch_array($getStockDetails)) {

                $name = $rows_StockDetails['name'];
                $photo = $rows_StockDetails['image'];
                $symbol = $rows_StockDetails['symbol'];
                $costprice = $rows_StockDetails['costprice'];
                $sellingprice = $rows_StockDetails['sellingprice'];
                $quantity = $rows_StockDetails['quantity'];
                $finalprice = $rows_StockDetails['finalprice'];
            }
        }
    } else {
        header("Location: index.php");
    }
}

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
    <title>
        <?php echo $name; ?>
    </title>
    <link rel="icon" type="image/x-icon" href="../images/stock.png">
    <style>
        <?php if ($quantity <= 0) {
            ?>
            #itemImage {
                cursor: pointer;
                border-radius: 50%;
                object-fit: cover;
                aspect-ratio: 1/1;
                border: 4px solid;
            }

            <?php
        } else {
            ?>
            #itemImage {
                cursor: pointer;
                border-radius: 50%;
                object-fit: cover;
                aspect-ratio: 1/1;
                border: 4px solid;
            }

            <?php
        }
        ?>
    </style>
</head>

<body>

    <div class="modal fade" id="DeleteItemModel" tabindex="-1" aria-labelledby="edit-data-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">समान हटाउनुहोस</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="DeleteItemForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        के तपाई यो समान को रेकर्ड मेटाउन चहानु हुन्छ ?
                        <input type="text" name="itemid" value="<?php echo $id ?>" hidden>
                        <input type="text" name="photo" value="<?php echo $photo ?>" hidden>
                        <div id="responseDelete" class="text-danger"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">रद्द गर्नुहोस</button>
                        <button type="submit" class="btn btn-danger">मेटाउनुहोस <span id="spinnerDelete"
                                style="display:none;" class="spinner-border spinner-border-sm" role="status"
                                aria-hidden="true"></span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="AddItemModel" tabindex="-1" aria-labelledby="edit-data-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">मात्रा थप</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="AddItemForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        थप हुने मात्रा लेख्नुहोस
                        <input type="text" name="id" value="<?php echo $id ?>" hidden>
                        <input type="text" name="oldquantity" value="<?php echo $quantity ?>" hidden>
                        <input class="form-control mt-2" type="text" name="arrivedquantity" title="Numbers only"
                            required pattern="^[0-9 ]+$" placeholder="थप हुने मात्रा">
                        <div id="responseAddModal" class="text-danger"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">रद्द गढ़ुहोस</button>
                        <button type="submit" class="btn btn-primary">थप गर्नुहोस <span id="spinnerAdd"
                                style="display:none;" class="spinner-border spinner-border-sm" role="status"
                                aria-hidden="true"></span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    $stockactive = "active";
    include("stocksnav.php");
    ?>

    <div class="mt-2 container-fluid" style="font-family: 'Geologica', sans-serif;">

        <?php

        if ($photo != "") {
            ?>

            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12 col-12 text-center mt-2 pt-xl-5 pt-lg-5 pt-md-5"
                        style="font-size: medium; font-family: 'Geologica', sans-serif;">
                        <a href="files/<?php echo $photo; ?>" targe="_blank"><img style="object-fit:contain;" id="itemImage" src="files/<?php echo $photo; ?>"
                            class="clickable-row col-xl-6 col-lg-8 col-md-5 col-sm-4 col-6 p-1 border-primary"
                            alt="Profile Picture"></a>
                        <p class="mt-3"><span style="color: black; font-weight:bold; font-size: larger">
                                <?php echo $name; ?>
                            </span><br>सिम्बोल :
                            <?php echo $symbol; ?><br>किनेको दाम :
                            <?php echo $costprice; ?><br>बेच्ने दाम :
                            <?php echo $sellingprice; ?><br>अन्तिम मूल्य :
                            <?php echo $finalprice; ?><br>स्टक मा भएको मात्रा :
                            <?php echo $quantity; ?><br>

                        </p>

                        <?php
        } else {
            ?>

                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12 col-12 text-center mt-2 pt-xl-5 pt-lg-5 pt-md-5"
                                    style="font-size: medium; font-family: 'Geologica', sans-serif;">
                                    <div id="itemImage" class="col-xl-6 col-lg-8 col-md-5 col-sm-4 col-6 p-1 border-primary"
                                        style="margin:auto; display:flex; justify-content:center; align-items:center;"><i
                                            style="font-size:80px;" class="bi bi-box-seam-fill text-primary"></i></div>
                                    <p class="mt-3"><span style="color: black; font-weight:bold; font-size: larger">
                                            <?php echo $name; ?>
                                            </span><br>सिम्बोल :
                            <?php echo $symbol; ?><br>किनेको दाम :
                            <?php echo $costprice; ?><br>बेच्ने दाम :
                            <?php echo $sellingprice; ?><br>अन्तिम मूल्य :
                            <?php echo $finalprice; ?><br>स्टक मा भएको मात्रा :
                            <?php echo $quantity; ?><br>

                                    </p>

                                    <?php
        }
        ?>
                                <button class="btn btn-danger" id="DeleteItemButton" style="font-size:small;">सामान हटाउनुहोस</button>
                                <button class="btn btn-primary" id="AddItemButton" style="font-size:small;">थप मात्रा दर्ता</button>
                            </div>
                            <div style="height: 480px;"
                                class="table-responsive table-wrapper-scroll-y my-custom-scrollbar m-auto col-xl-9 col-lg-9 col-md-8 col-sm-12 col-12 mt-2">

                                <h6 style="font-family:'Geologica', sans-serif; text-transform: uppercase;"
                                    class="mt-3 bg-primary text-white text-center pt-1 pb-1">
                                    सामान विवरण परिवर्तन</h6>
                                <form id="UpdateStockInfoForm" style="margin-top: -15px; font-family:'', sans-serif;">
                                    <input type="text" name="oldimage" value="<?php echo $photo; ?>" hidden>
                                    <input type="text" name="id" value="<?php echo $id; ?>" hidden>

                                    <div class="row">
                                        <div class="row gy-2">
                                            <div id="response" class="text-danger"></div>
                                            <label for="" class="fw-bold text-primary">नाम</label>
                                            <div class="col-12">
                                                <input type="text" class="form-control" placeholder="नाम"
                                                    value="<?php echo $name; ?>" name="name" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="row mt-1 gy-2">
                                            <div class="col-6">
                                                <label for="" class="fw-bold text-primary">सिम्बोल</label>
                                                <input type="text" class="form-control" placeholder="सिम्बोल"
                                                    name="symbol" value="<?php echo $symbol; ?>">
                                            </div>
                                            <div class="col-6">
                                                <label for="" class="fw-bold text-primary">मात्रा</label>
                                                <input type="text" class="form-control" placeholder="मात्रा"
                                                    name="quantity" value="<?php echo $quantity; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="row mt-1 gy-2">
                                            <div class="col-4">
                                                <label for="" class="fw-bold text-primary">किनेको दाम</label>
                                                <input type="text" class="form-control" placeholder="किनेको दाम"
                                                    name="costprice" value="<?php echo $costprice; ?>" required>
                                            </div>
                                            <div class="col-4">
                                                <label for="" class="fw-bold text-primary">बेच्ने दाम</label>
                                                <input type="text" class="form-control" placeholder="बेच्ने दाम"
                                                    name="sellingprice" value="<?php echo $sellingprice; ?>" required>
                                            </div>
                                            <div class="col-4">
                                                <label for="" class="fw-bold text-primary">बेच्ने अन्तिम मूल्य</label>
                                                <input type="text" class="form-control" placeholder="बेच्ने अन्तिम मूल्य"
                                                    name="finalprice" value="<?php echo $finalprice; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="row col mt-1 gy-2">
                                            <label for="formFile" class="fw-bold text-primary">फोटो <span
                                                    id="invalidextension" class="text-danger"></span></label>
                                            <div class="col-12">
                                                <input class="form-control" type="file" name="photo" id="photo"
                                                    required>
                                                <input class="form-check-input" style="cursor:pointer;" type="checkbox"
                                                    name="photocheck" value="" id="photocheck">
                                                <label class="form-check-label" style="cursor:pointer;"
                                                    for="photocheck">
                                                    फोटो परिवर्तन नगर्ने
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="mt-2 btn btn-primary w-100">परिवर्तन गर्नुहोस <span id="spinner"
                                            style="display:none;" class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span></button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

                <script src="jquery/jquery-3.6.1.min.js"></script>
                <script>

                    $(document).ready(function () {
                        window.addEventListener("keyup", function (e) { if (e.keyCode == 27) history.back(); }, false);
                        $('#DeleteItemButton').click(function (e) {
                            $('#DeleteItemModel').modal('show');
                        });

                        $('#AddItemButton').click(function (e) {
                            $('#AddItemModel').modal('show');
                        });
                        $('#photo').attr('disabled', true);
                        $('#photocheck').attr('checked', true);
                        $('#photocheck').change(function () {
                            if ($('#photocheck:checked').length) {
                                $('#photo').attr('disabled', true); //If checked - Read only
                            } else {
                                $('#photo').attr('disabled', false); //Not Checked - Normal
                            }
                        });

                        $("#UpdateStockInfoForm").submit(function (e) {
                            document.getElementById("spinner").style.display = "";
                            document.getElementById("invalidextension").style.display = "none";
                            e.preventDefault();

                            var form_data = new FormData(this);

                            $.ajax({
                                url: "updatestockvalidate.php",
                                type: 'POST',
                                data: form_data,
                                processData: false,
                                contentType: false,
                                success: function (response) {
                                    if (response == "success") {
                                        window.location.replace("index.php?p=<?php echo $id; ?>");
                                    } else if (response == "invalidextension") {
                                        document.getElementById("spinner").style.display = "none";
                                        document.getElementById("invalidextension").style.display = "";
                                        document.getElementById("invalidextension").innerHTML = "Only jpg, jpeg and png files allowed.";
                                    } else {
                                        document.getElementById("spinner").style.display = "none";
                                        document.getElementById("response").innerHTML = response;
                                    }
                                }
                            });
                            return false;
                        });

                        $("#DeleteItemForm").submit(function (e) {
                            document.getElementById("spinnerDelete").style.display = "";
                            e.preventDefault();

                            var deleteItemData = new FormData(this);

                            $.ajax({
                                url: "deleteitemvalidate.php",
                                type: 'POST',
                                data: deleteItemData,
                                processData: false,
                                contentType: false,
                                success: function (response) {
                                    if (response == "success") {
                                        window.location.replace("index.php");
                                    } else {
                                        document.getElementById("spinnerDelete").style.display = "none";
                                        document.getElementById("responseDelete").innerHTML = response;
                                    }
                                }
                            });
                            return false;
                        });

                        $("#AddItemForm").submit(function (e) {
                            document.getElementById("spinnerAdd").style.display = "";
                            e.preventDefault();

                            var addArrivalData = new FormData(this);

                            $.ajax({
                                url: "newarrivalvalidate.php",
                                type: 'POST',
                                data: addArrivalData,
                                processData: false,
                                contentType: false,
                                success: function (response) {
                                    if (response == "success") {
                                        window.location.replace("index.php");
                                    } else {
                                        document.getElementById("spinnerAdd").style.display = "none";
                                        window.location.replace("index.php");
                                    }
                                }
                            });
                            return false;
                        });

                    });

                </script>
</body>

</html>