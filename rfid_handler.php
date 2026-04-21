<?php
include "db.php";
date_default_timezone_set('Asia/Manila'); 

if (isset($_GET['uid'])) {
    $raw_uid = $_GET['uid'];
    $clean_uid = strtoupper(str_replace(' ', '', $raw_uid));
    $safe_uid = mysqli_real_escape_string($conn, $clean_uid);
    
    $now = time(); 
    $today = date("Y-m-d");

    // 1. Find the student
    $st_res = mysqli_query($conn, "SELECT * FROM students WHERE UPPER(REPLACE(rfid_uid, ' ', '')) = '$safe_uid' LIMIT 1");
    $student = mysqli_fetch_assoc($st_res);

    if (!$student) { die("NOT_FOUND|Card not in system"); }

    $t_id = $student['teacher_id'];
    $s_id = $student['id'];
    $fullname = $student['fullname'];

    // 2. Fetch the "Gate Opening Time" you set in the manual settings
    $set_res = mysqli_query($conn, "SELECT class_start FROM settings WHERE teacher_id='$t_id'");
    $set = mysqli_fetch_assoc($set_res);
    
    $opening_time_str = $set['class_start'] ?? '07:00:00';
    $opening_timestamp = strtotime($today . " " . $opening_time_str);

    // --- GATE SECURITY LOGIC ---
    if ($now < $opening_timestamp) {
        // If it is 6:59 AM and the gate is set to 7:00 AM, this stops the process
        die("LOCKED|The gate is currently locked until " . date("h:i A", $opening_timestamp));
    }

    // 3. If gate is open, process attendance
    // (Optional: You can add 'Late' logic here if needed, otherwise it marks 'Present')
    $status = 'Present';
    $time_in = date("H:i:s");

    // Update Live Status
    mysqli_query($conn, "UPDATE students SET status = '$status', last_scan = NOW() WHERE id = '$s_id'");

    // Record in History if not already scanned today
    $check = mysqli_query($conn, "SELECT * FROM attendance WHERE student_id = '$s_id' AND date = '$today'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO attendance (student_id, teacher_id, date, time_in, status) 
                             VALUES ('$s_id', '$t_id', '$today', '$time_in', '$status')");
        echo "SUCCESS|$status|$fullname";
    } else {
        echo "ALREADY_SCANNED|$fullname";
    }
}
?>