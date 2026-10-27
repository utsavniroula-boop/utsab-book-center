<?php

$servername = "localhost";
$username = "root";
$dbname = "utsabbookcenter";
$password = "root";

$IndividualDatabase = new mysqli($servername, $username, $password, $dbname);

if ($IndividualDatabase->connect_error) {
  die("Connection failed: " . $IndividualDatabase->connect_error);
}