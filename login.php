<?php

session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['Username'];
    $password = $_POST['Password'];

    // Check if admin is logging in
    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['admin'] = true;
        echo json_encode(["status" => "success", "message" => "Admin login successful!"]);
        exit;
    }

    $sql = "SELECT STUD_NUM, PASSWORD_HASH, FIRST_NAME FROM users WHERE USER_NAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($userId, $password_hash, $first_name);
    $stmt->fetch();

    header('Content-Type: application/json');
    if (password_verify($password, $password_hash)) {
        $_SESSION['user_id'] = $userId; 
        $_SESSION['first_name'] = $first_name; 
        echo json_encode(["status" => "success", "message" => "Login successful!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid username or password."]);
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="logo/ccs.png" type="image/x-icon">
    <title>Login</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="script.js" defer></script>
</head>
<body class="h-screen flex items-center justify-center" style="background-image: linear-gradient(111.3deg, rgba(74,105,187,1) 9.6%, rgba(205,77,204,1) 93.6%);">
    <div class="fixed bg-white p-8 rounded-lg shadow-lg max-w-md w-full" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <div class="flex justify-center items-center gap-5 mb-6">
            <img src="logo/uc.png" alt="Logo" class="w-24">
            <img src="logo/ccs.png" alt="Logo" class="w-24">
        </div>
        <h2 class="text-center text-2xl font-bold mb-6">CCS Sit-in Monitoring System</h2>
        <form id="loginForm" method="POST" action="">
            <div class="mb-4 relative">
                <label for="Username" class="sr-only">Username</label>
                <input type="text" id="Username" name="Username" class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-400" placeholder="Username" required>
                <i class="fa fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>

            <div class="mb-4 relative">
                <label for="Password" class="sr-only">Password</label>
                <input type="password" id="Password" name="Password" class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-400" placeholder="Password" required>
                <i class="fa fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>

            <div class="text-center">
                <button class="bg-gradient-to-r from-purple-700 to-pink-500 text-white py-2 px-4 rounded-lg hover:from-pink-500 hover:to-purple-700 hover:text-black" type="submit">Login</button>
            </div>

            <div class="text-center mt-4">
                <p>Don't have an account? <a href="registration.php" class="text-blue-500">Register</a></p>
            </div>
        </form>
    </div>
</body>
</html>