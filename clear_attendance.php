<?php
session_start();
include "db.php";

if(!isset($_SESSION['user'])) exit;

// Get Teacher ID
$teacher_username = $_SESSION['user'];
$teacher = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM users WHERE username='$teacher_username'"));
$teacher_id = $teacher['id'];
$today = date('Y-m-d');

// Delete only TODAY'S attendance for THIS teacher
$sql = "DELETE FROM attendance WHERE teacher_id = '$teacher_id' AND date = '$today'";

if(mysqli_query($conn, $sql)) {
    header("Location: dashboard.php?msg=cleared");
} else {
    echo "Error clearing records: " . mysqli_error($conn);
}
?>