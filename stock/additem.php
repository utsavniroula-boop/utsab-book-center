<?php
include ("checklogin.php");
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
    <script src="jquery/jquery-3.6.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Stock</title>
    <link rel="icon" type="image/x-icon" href="../images/stock.png">
</head>

<body>

    <?php

    include ("stocksnav.php");

    ?>

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

    <div class="mt-5">
        <div class="col-11 pt-3 ps-3 pe-2 m-auto bg-white"
            style="border: 1px solid rgb(188, 180, 180); border-radius: 5px;">
            <div id="response" class="text-danger"></div>

            <form method="post" style="display: block;" action="additemvalidate.php" id="addItemForm" enctype="multipart/form-data">
                <label class="form-label fw-bold text-primary">सामान को नाम</label>
                <div class="row">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="name" name="name" maxlength="200"
                            placeholder="Geometry" title="Only letters and numbers allowed." autofocus required>
                    </div>
                </div>
                <div class="row">
                    <label class="form-label fw-bold text-primary">फोटो</label>
                    <div class="mb-1">
                        <input class="form-control" type="file" name="photo" id="photo" required>
                        <div class="form-check">
                            <input class="form-check-input" style="cursor:pointer;" type="checkbox" name="photocheck"
                                value="" checked id="photocheck">
                            <label class="form-check-label" style="cursor:pointer;" for="photocheck">
                                Use default picture
                            </label>
                        </div>
                        <div id="invalidextension" class="text-danger" style="font-weight:bold;"></div>
                    </div>
                </div>
                <label class="form-label fw-bold text-primary">विवरण</label>
                <div class="row">
                    <div class="mb-3 col">
                        <input type="text" id="symbol" class="form-control" placeholder="सिम्बोल" maxlength="50"
                            name="symbol" title="Only numbers allowed.">
                    </div>
                    <div class="mb-3 col">
                        <input type="text" id="quantity" class="form-control" placeholder="मात्रा" maxlength="50"
                            name="quantity" title="Only numbers allowed." pattern="^[0-9 ]+$">
                    </div>
                </div>
                <label class="form-label fw-bold text-primary">थप विवरण</label>
                <div class="row">
                    <div class="mb-3 col">
                        <input type="text" id="cost" class="form-control" placeholder="किनेको दाम" maxlength="50"
                            name="cost" title="Only numbers allowed." pattern="^[0-9. ]+$" required>
                    </div>
                    <div class="mb-3 col">
                        <input type="text" id="sell" class="form-control" placeholder="बेच्ने दाम" maxlength="50"
                            name="sell" title="Only numbers allowed." pattern="^[0-9. ]+$" required>
                    </div>
                    <div class="mb-3 col">
                        <input type="text" id="sell" class="form-control" placeholder="अन्तिम मूल्य" maxlength="50"
                            name="final" title="Only numbers allowed." pattern="^[0-9.- ]+$">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-2">दर्ता गर्नुहोस <span id="spinner"
                        style="display:none;" class="spinner-border spinner-border-sm" role="status"
                        aria-hidden="true"></span></button>
            </form>
        </div>
    </div>


    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            window.addEventListener("keyup", function (e) { if (e.keyCode == 27) history.back(); }, false);
            $('#photo').attr('disabled', true);
            $('#photocheck').change(function () {
                if ($('#photocheck:checked').length) {
                    $('#photo').attr('disabled', true); //If checked - Read only
                } else {
                    $('#photo').attr('disabled', false); //Not Checked - Normal
                }
            });

            $("#addItemForm").submit(function (e) {
                document.getElementById("spinner").style.display = "";
                e.preventDefault();

                var form_data = new FormData(this);

                $.ajax({
                    url: "additemvalidate.php",
                    type: 'POST',
                    data: form_data,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response == "success") {
                            window.location.replace("index.php");
                        } else if (response == "invalidextension") {
                            document.getElementById("spinner").style.display = "none";
                            document.getElementById("invalidextension").innerHTML = "Only jpg, jpeg and png files allowed.";
                        } else {
                            document.getElementById("spinner").style.display = "none";
                            document.getElementById("response").innerHTML = response;
                        }
                    }
                });
                return false;
            });
        });
    </script>
</body>

</html>