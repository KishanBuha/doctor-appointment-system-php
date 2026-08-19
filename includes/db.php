<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "doctor_appointment";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add this line to set the time zone to India Standard Time
date_default_timezone_set('Asia/Kolkata');
?>