<?php
session_start();
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['time_out'])) {
    $sitin_id = $_POST['sitin_id'];
    
    // Update the sit-in record with time-out and status
    $stmt = $conn->prepare("UPDATE curr_sitin SET TIME_OUT = NOW(), STATUS = 'Completed' WHERE SITIN_ID = ?");
    $stmt->bind_param("i", $sitin_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Student has been timed-out successfully.";
    } else {
        $_SESSION['error'] = "Error timing-out student.";
    }
}

header("Location: admin_sitin.php");
exit();
