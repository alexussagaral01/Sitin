<?php
session_start();
require '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Prepare and execute the delete statement
    $stmt = $conn->prepare("DELETE FROM announcement WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "Announcement deleted successfully.";
    } else {
        $_SESSION['message'] = "Error deleting announcement.";
    }
    
    $stmt->close();
}

// Redirect back to dashboard
header("Location: admin_dashboard.php");
exit();
?>
