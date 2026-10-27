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
    <div class="row mt-2 container-fluid">
            <h5 class="text-start">उत्सव पुस्तक पसल</h5>
            <h6 class="text-start">बाह्रदशी-३, झापा</h6>
            <h5 class="text-start">ESTIMATE BILL</h5>
            <div style="margin-top: 20px; font-size:18px;">
                ग्राहकको नाम : <?php echo $_POST['name']; ?> <br> ठेगाना : <?php echo $_POST['address']; ?> <br> मिति : <?php date_default_timezone_set('Asia/Kathmandu');
                                                                                                        $date = date('Y/m/d h:i:s A');
                                                                                                        echo $date; ?>
            </div>

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

        <table class="mt-3" style="width: auto; border-collapse:collapse; margin-left: 10px;">
            <tr>
                <th style="width:100px; border: 1px solid; padding:1px 0px 1px 10px;">जम्मा</th>
                <td style="width:200px; border: 1px solid; padding:1px 0px 1px 10px;">रू <?php echo $total_amount; ?>.00</td>
            </tr>
            <tr>
                <th style="width:100px; border: 1px solid; padding:1px 0px 1px 10px;">छुट</th>
                <td style="width:200px; border: 1px solid; padding:1px 0px 1px 10px;">रू <?php echo $_POST['discount']; ?>.00</td>
            </tr>
            <tr>
                <th style="width:100px; border: 1px solid; padding:1px 0px 1px 10px;">खुद रकम</th>
                <td style="width:200px; border: 1px solid; padding:1px 0px 1px 10px;">रू 
                
                <?php

                if($_POST['discount'] == ""){
                    echo $_POST['total'];
                }
                else{
                    echo $_POST['total']-$_POST['discount']; 
                }
                ?>.00</td>
            </tr>
        </table>

        <div class="row mt-4">
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6">
                <h5 class="text-start">_____________</h5>
                <h6 class="text-start">ग्राहकको दस्तखत</h6>
            </div>

            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6">
                <h5 class="text-end">_____________</h5>
                <h6 class="text-end">बिक्रेताको दस्तखत</h6>
            </div>
        </div>

    </div>
</body>

</html>