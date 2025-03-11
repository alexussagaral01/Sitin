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
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="logo/ccs.png" type="image/x-icon">
    <title>Login</title>
    <style>
        body {
            background-image: linear-gradient(104.1deg, rgba(0,61,100,1) 13.6%, rgba(47,127,164,1) 49.4%, rgba(30,198,198,1) 93.3%);
            background-attachment: fixed;
        }
        
        .w3-input:focus::placeholder {
            opacity: 0;
        }

        .w3-input::placeholder {
            transition: opacity 0.3s ease;
        }

        label {
            font-family: 'Roboto', sans-serif;
        }

        .w3-display-container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;  
        }

        .logo {
            width: 100px;  
            height: auto;  
        }

        .w3-button {
            background-image: linear-gradient(104.1deg, rgba(0,61,100,1) 13.6%, rgba(47,127,164,1) 49.4%, rgba(30,198,198,1) 93.3%);
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 12px;
        }

        .w3-button:hover {
            background-image: linear-gradient(104.1deg, rgba(30,198,198,1) 13.6%, rgba(47,127,164,1) 49.4%, rgba(0,61,100,1) 93.3%);
        }

        .input-container {
            position: relative;
        }

        .input-container i {
            position: absolute;
            left: 10px; 
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }

        .w3-input {
            padding-left: 30px; 
            border-radius: 15px; 
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="script.js" defer></script>
</head>
<body>
    <div class="w3-display-container w3-animate-zoom">
        <div class="w3-display-middle w3-card w3-white w3-padding" style="max-width: 500px; width: 90%; padding: 30px; border-radius: 15px; border: 1px solid black; box-shadow: 0 0 20px 5px rgba(0, 0, 0, 0.4);">
            <div class="logo-container">
                <img src="logo/uc.png" alt="Logo" class="logo">
                <img src="logo/ccs.png" alt="Logo" class="logo">
            </div>
            <h2 class="w3-center" style="font-family: 'Roboto', sans-serif; font-weight: bold">CCS Sit-in Monitoring System</h2>
            <form id="loginForm" method="POST" action="">
                <div class="w3-margin-bottom input-container">
                    <label for="Username"></label>
                    <input type="text" id="Username" name="Username" class="w3-input w3-border w3-serif" placeholder="Username" required>
                    <i class="fa fa-user"></i>
                </div>

                <div class="w3-margin-bottom input-container">
                    <label for="Password"></label>
                    <input type="password" id="Password" name="Password" class="w3-input w3-border w3-serif" placeholder="Password" required>
                    <i class="fa fa-lock"></i>
                </div>

                <div class="w3-center">
                    <button  class="w3-button w3-blue" type="submit">Login</button>
                </div>

                <div class="w3-center w3-margin-top">
                    <p>Don't have an account? <a href="registration.php" class="w3-text-blue">Register</a></p>
                </div>

            </form>
        </div>
    </div>
</body>
</html>