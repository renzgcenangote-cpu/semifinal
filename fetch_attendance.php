<?php
session_start();
include "db.php";

// Set timezone
date_default_timezone_set('Asia/Manila');

// Get the logged-in teacher's ID
$teacher_id = $_SESSION['teacher_id'] ?? 1;
$today = date("Y-m-d");

// FIXED QUERY: This links students to attendance even if the record doesn't exist yet (LEFT JOIN)
// It checks both the 'id' and the 'student_id' string to ensure a match is found
$query = "SELECT 
            s.fullname, 
            s.student_id as id_num,
            a.status, 
            a.time_in 
          FROM students s 
          LEFT JOIN attendance a ON (s.id = a.student_id OR s.student_id = a.student_id) 
          AND a.date = '$today'
          WHERE s.teacher_id = '$teacher_id' 
          GROUP BY s.student_id
          ORDER BY a.time_in DESC, s.fullname ASC";

$res = mysqli_query($conn, $query);

if(mysqli_num_rows($res) > 0) {
    while($row = mysqli_fetch_assoc($res)) {
        // Default to 'Absent' if no scan record exists for today
        $status = $row['status'] ?? 'Absent';
        
        // Format the time or show placeholder
        $time_display = ($row['time_in'] && $row['time_in'] != '00:00:00') 
                        ? date("h:i:s A", strtotime($row['time_in'])) 
                        : "--:--:--";
        
        // Define colors for the badges
        $badge_class = "bg-danger-subtle text-danger border-danger"; // Default: Absent
        if ($status == 'Present') {
            $badge_class = 'bg-success-subtle text-success border-success';
        } elseif ($status == 'Late') {
            $badge_class = 'bg-warning-subtle text-warning-emphasis border-warning';
        }

        echo "<tr>
                <td>
                    <div class='d-flex align-items-center'>
                        <div class='avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center' style='width:35px; height:35px;'>
                            <i class='bi bi-person text-secondary'></i>
                        </div>
                        <div>
                            <div class='fw-600 text-dark'>{$row['fullname']}</div>
                            <div class='text-muted' style='font-size: 0.7rem;'>ID: {$row['id_num']}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class='badge rounded-pill border px-3 py-2 $badge_class' style='font-size: 0.75rem; font-weight: 700;'>
                        $status
                    </span>
                </td>
                <td>
                    <span class='text-muted small fw-bold'>
                        <i class='bi bi-clock me-1'></i>$time_display
                    </span>
                </td>
              </tr>";
    }
} else {
    echo "<tr>
            <td colspan='3' class='text-center py-5 text-muted'>
                <i class='bi bi-people opacity-25 d-block mb-2' style='font-size: 2rem;'></i>
                <small class='fw-bold'>No students found in your directory.</small>
            </td>
          </tr>";
}
?>