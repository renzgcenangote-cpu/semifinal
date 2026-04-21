<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "attendance";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Keep the status update logic
$master_interval = 1; 
$reset_sql = "UPDATE students SET status = 'Absent' 
              WHERE last_scan < (NOW() - INTERVAL $master_interval MINUTE) 
              AND status = 'Present'";
mysqli_query($conn, $reset_sql);
?>