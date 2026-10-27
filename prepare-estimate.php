<?php
session_start();

if (!$_SESSION['uname']) {
    $_SESSION['errormessage'] = " You'll have to login first.";
    header("Location:login.php");
    exit(0);
}

$from  = $_POST['from'];
$to = $_POST['to'];
$name = $_POST['name'];
$address = $_POST['address'];
$tablename = $_POST['tablename'];

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

<?php include("navbar.php"); ?>

    <div class="row mt-2 container-fluid">

        <table class="mt-3" style="width: 100%; border-collapse:collapse; margin-left: 10px;">
            <tr>
                <th style="width:100px; border: 1px solid; padding:1px 0px 1px 10px;">नं</th>
                <th style="border: 1px solid; padding:1px 0px 1px 10px;">विवरण</th>
                <th style="width:200px; border: 1px solid; padding:1px 0px 1px 10px;">रकम</th>
            </tr>

            <?php

            $conn = new mysqli('localhost', 'root', 'root', 'utsabbookcenter');
            if ($conn->connect_error) {
                die('Connection Failed : ' . $conn->connect_error);
            } else {

                $sql = "select * from $tablename where date between '$from' and '$to' and status='gave' and amount!='0' order by id desc";
                $sql_result = mysqli_query($conn, $sql);

                if ($sql_result) {

                    $number = 1;
                    $total_amount = 0;

                    while ($rows_getbill = mysqli_fetch_array($sql_result)) {


            ?>
                        <tr>
                            <td style="width:100px; border: 1px solid; padding:1px 0px 1px 10px;"><?php echo $number ?></td>
                            <td style="border: 1px solid; padding:1px 0px 1px 10px;"><?php echo $rows_getbill['description'] ?></td>
                            <td style="width:200px; border: 1px solid; padding:1px 0px 1px 10px;">रू <?php echo $rows_getbill['amount'] ?>.00</td>
                        </tr>
            <?php
                        $number = $number + 1;
                        $total_amount = $total_amount + $rows_getbill['amount'];
                    }
                }
            }

            ?>

        </table>

        <form action="print-estimate.php" method="post" class="mt-4">

            <input type="text" name="from" value="<?php echo $from; ?>" hidden>
            <input type="text" name="name" value="<?php echo $name; ?>" hidden>
            <input type="text" name="address" value="<?php echo $address; ?>" hidden>
            <input type="text" name="to" value="<?php echo $to; ?>" hidden>
            <input type="text" name="tablename" value="<?php echo $tablename; ?>" hidden>

            <div class="input-group mb-3">
                <span class="input-group-text text-white bg-primary" id="basic-addon1">जम्मा</span>
                <input type="text" value="<?php echo $total_amount; ?>" class="form-control border-primary" placeholder="" aria-label="total" name="total" aria-describedby="basic-addon1" readonly>
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text text-white bg-primary" id="basic-addon1">छुट</span>
                <input type="text" class="form-control border-primary" placeholder="छुट रकम लेख्नुहोस " aria-label="discount" name="discount" aria-describedby="basic-addon1" pattern="[0-9\s]+" title="Only numbers">
            </div>
            <button type="submit" class="btn btn-success">प्रिन्ट पेजमा जानुहोस</button>
        </form>
    </div>
</body>

</html>