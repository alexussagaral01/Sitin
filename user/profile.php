<?php
session_start();
require '../db.php'; // Updated path

$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$firstName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'Guest';

if ($userId) {
    $stmt = $conn->prepare("SELECT IDNO, LAST_NAME, FIRST_NAME, MID_NAME, COURSE, YEAR_LEVEL, EMAIL, ADDRESS, UPLOAD_IMAGE, SESSION FROM users WHERE STUD_NUM = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($idNo, $lastName, $dbFirstName, $midName, $course, $yearLevel, $email, $address, $userImage, $session);
    $stmt->fetch();
    $stmt->close();
    
    $profileImage = !empty($userImage) ? '../images/' . $userImage : "../images/image.jpg";
    $fullName = trim("$dbFirstName $midName $lastName");
} else {
    $profileImage = "../images/image.jpg";
    $idNo = '';
    $fullName = '';
    $yearLevel = '';
    $course = '';
    $email = '';
    $address = '';
    $session = '';
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="../logo/ccs.png" type="image/x-icon"> <!-- Updated path -->
    <title>Profile</title>
    <style>
        body {
        background-image: linear-gradient(104.1deg, rgba(0,61,100,1) 13.6%, rgba(47,127,164,1) 49.4%, rgba(30,198,198,1) 93.3%);
        background-attachment: fixed;
        }

        .logo, .student-info img {
        width: 150px; 
        height: 150px; 
        display: block;
        margin-left: auto;
        margin-right: auto;
        border: 1px solid black;
        border-radius: 50%; 
        object-fit: cover; 
        }

        .sidenav {
        height: 100%;
        width: 250px;
        position: fixed;
        z-index: 1;
        top: 0;
        left: -250px;
        background-color: white; 
        overflow-x: hidden;
        padding-top: 20px;
        font-family: 'Roboto', sans-serif;
        font-size: 18px;
        transition: 0.3s;
        }

        .sidenav.show {
        left: 0;
        }

        .sidenav a {
        padding: 8px 8px 8px 16px;
        text-decoration: none;
        font-size: 15px;
        color: black; 
        display: flex;
        align-items: center;
        position: relative;
        padding-left: 16px; 
        }

        .sidenav a i {
        font-size: 15px; 
        margin-right: 10px; 
        }

        .sidenav a:hover {
        background-color: black; 
        color: white; 
        transform: scale(1.05); 
        transition: transform 0.3s, background-color 0.3s, color 0.3s; 
        }

        .sidenav a:hover i {
        color: white; 
        }

        .sidenav a::before {
        content: '';
        position: absolute;
        left: -10px; 
        top: 0;
        bottom: 0;
        width: 5px; 
        background-color: transparent;
        transition: background-color 0.3s;
        }

        .sidenav a:hover::before {
        background-color: black; 
        }

        .user-name {
        color: #000; 
        text-align: center;
        font-family: 'Roboto', sans-serif;
        font-size: 22px;
        font-weight: bold; 
        }

        .container {
        display: inline-block;
        cursor: pointer;
        position: absolute;
        top: 15px; 
        left: 25px; 
        }

        .bar1, .bar2, .bar3 {
        width: 35px;
        height: 5px;
        background-color: black; 
        margin: 6px 0;
        transition: 0.4s;
        }

        .closebtn {
            position: absolute;
            top: 0; 
            right: 0; 
            padding: 10px 15px;
            font-size: 36px;
            cursor: pointer;
            color: #818181;
        }

        .closebtn:hover {
            color: #000; 
        }

        .header {
            text-align: center;
            background-color: white; 
            color: black; 
            font-family: 'Roboto', sans-serif;
            font-size: 25px; 
            font-weight: bold; 
            padding: 10px; 
        }

        .logout-section {
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
        }

        .logout-section a {
            color:  black;
        }

        .logout-section a:hover {
            background-color: black; 
            color: white; 
        }

        .logout-section a:hover i {
            color: white; 
        }

        .student-info {
            background-color: white; 
            border-radius: 15px;
            padding: 10px; 
            width: 100%; 
            max-width: 400px; 
            margin: 50px auto; 
            text-align: center; 
            font-family: 'Roboto', sans-serif; 
            box-shadow: 0 0 20px 5px rgba(0, 0, 0, 0.4);
            border: 1px solid black;
            transition: transform 0.3s;
            max-width: 500px;
        }
        .student-info:hover {
            transform: scale(1.05)
        }
        .student-info h2 {
            background-color: #003d64;
            color: white;
            padding: 15px;
            margin: -10px -10px 10px -10px;
            border-radius: 15px 15px 0 0; 
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .student-info p {
            margin: 5px 0; 
            font-size: 18px; 
        }
        .student-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-info th, .student-info td {
            text-align: left;
            padding: 8px;
            vertical-align: middle;
        }
        .student-info th {
            width: 30%;
            display: flex;
            align-items: center;
        }
        .student-info th i {
            margin-right: 10px;
        }

    </style>
</head>
<body>
    <div class="header">
        CCS SIT-IN MONITORING SYSTEM
    </div>
    <div class="container" onclick="toggleNav(this)">
        <div class="bar1"></div>
        <div class="bar2"></div>
        <div class="bar3"></div>
    </div>
    <div class="sidenav" id="mySidenav">
        <span class="closebtn" onclick="closeNav()">&times;</span>
        <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Logo" class="logo">
        <p class="user-name"><?php echo htmlspecialchars($firstName); ?></p>
        <a href="dashboard.php"><i class="fas fa-home"></i> HOME</a>
        <a href="profile.php"><i class="fas fa-user"></i> PROFILE</a>
        <a href="edit.php"><i class="fas fa-edit"></i> EDIT</a>
        <a href="history.php"><i class="fas fa-history"></i> HISTORY</a>
        <a href="reservation.php"><i class="fas fa-calendar-alt"></i> RESERVATION</a>

        <div class="logout-section">
            <a href="../login.php"><i class="fas fa-sign-out-alt"></i> LOG OUT</a>
        </div>
    </div>
    <div class="student-info">
        <h2>Student Information</h2>    
        <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Student Image" style="width: 150px; height: auto; display: block; margin: 0 auto 20px; border: 1px solid black;">
        <table>
            <tr>
                <th><i class="fas fa-id-card"></i> ID NUMBER:</th>
                <td><?php echo htmlspecialchars($idNo); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-user"></i> NAME:</th>
                <td><?php echo htmlspecialchars($fullName); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-graduation-cap"></i> YEAR LEVEL:</th>
                <td><?php echo htmlspecialchars($yearLevel); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-book"></i> COURSE:</th>
                <td><?php echo htmlspecialchars($course); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-envelope"></i> EMAIL:</th>
                <td><?php echo htmlspecialchars($email); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-home"></i> ADDRESS:</th>
                <td><?php echo htmlspecialchars($address); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-clock"></i> SESSION:</th>
                <td><?php echo htmlspecialchars($session); ?></td>
            </tr>
        </table>
    </div>
    
    <script>
        function toggleNav(x) {
        x.classList.toggle("change");
        document.getElementById("mySidenav").classList.toggle("show");
        }

        function closeNav() {
            document.getElementById("mySidenav").classList.remove("show");
            document.querySelector(".container").classList.remove("change");
        }
    </script>
</body>
</html>