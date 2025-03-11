<?php
session_start();
require '../db.php';

$firstName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'Guest';
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($userId) {
    $stmt = $conn->prepare("SELECT IDNO, LAST_NAME, FIRST_NAME, MID_NAME, COURSE, YEAR_LEVEL, EMAIL, ADDRESS, UPLOAD_IMAGE FROM users WHERE STUD_NUM = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($idNo, $lastName, $dbFirstName, $midName, $course, $yearLevel, $email, $address, $userImage);
    $stmt->fetch();
    $stmt->close();
    
    $profileImage = !empty($userImage) ? '../images/' . $userImage : "../images/image.jpg";
} else {
    $profileImage = "../images/image.jpg";
    $idNo = '';
    $lastName = '';
    $dbFirstName = '';
    $midName = '';
    $course = '';
    $yearLevel = '';
    $email = '';
    $address = '';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idno = $_POST['Idno'];
    $lastname = $_POST['Lastname'];
    $firstname = $_POST['Firstname'];
    $midname = $_POST['Midname'];
    $course = $_POST['Course'];
    $year_level = $_POST['Year_Level'];
    $email = $_POST['Email'];
    $address = $_POST['Address'];
    
    $uploadImagePath = $userImage; 
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($fileInfo, $_FILES['profileImage']['tmp_name']);
        finfo_close($fileInfo);
        
        if (in_array($fileType, $allowedTypes)) {
            $targetDir = "images/";
            
            $fileName = uniqid() . '_' . basename($_FILES["profileImage"]["name"]);
            $targetFile = $targetDir . $fileName;
            
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            
            if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetFile)) {
                $uploadImagePath = $fileName;
                $_SESSION['profile_image'] = $targetFile; 
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Failed to upload image."
                ]);
                exit;
            }
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid file type. Only JPEG, JPG, and PNG files are allowed."
            ]);
            exit;
        }
    }

    try {
       
        $stmt = $conn->prepare("UPDATE users SET IDNO = ?, LAST_NAME = ?, FIRST_NAME = ?, MID_NAME = ?, COURSE = ?, YEAR_LEVEL = ?, EMAIL = ?, ADDRESS = ?, UPLOAD_IMAGE = ? WHERE STUD_NUM = ?");
        $stmt->bind_param("issssssssi", $idno, $lastname, $firstname, $midname, $course, $year_level, $email, $address, $uploadImagePath, $userId);
        
        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Profile updated successfully."
            ]);
        } else {
            throw new Exception("Failed to update profile");
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }
    
    $conn->close();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="script.js"></script>
    <link rel="icon" href="../logo/ccs.png" type="image/x-icon">
    <title>Edit</title>
    <style>
        body {
            background-image: linear-gradient(104.1deg, rgba(0,61,100,1) 13.6%, rgba(47,127,164,1) 49.4%, rgba(30,198,198,1) 93.3%);
            background-attachment: fixed;
            font-family: 'Roboto', sans-serif; 
        }

        .logo, .profile-image {
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

        .display-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 50px; 
        }

        .card {
            background-color: white;
            padding: 15px;
            border-radius: 15px;
            border: 1px solid black;
            box-shadow: 0 0 20px 5px rgba(0, 0, 0, 0.4);
            max-width: 600px;
            width: 90%;
            font-family: 'Roboto', sans-serif; 
        }

        .card h2 {
            background-color: #003d64;
            color: white;
            padding: 15px;
            margin: -15px -15px 15px -15px;
            border-radius: 15px 15px 0 0; 
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .card input, .card select {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border-radius: 15px;
            border: 1px solid black;
            box-sizing: border-box; 
        }

        .card button {
            background-image: linear-gradient(104.1deg, rgba(0,61,100,1) 13.6%, rgba(47,127,164,1) 49.4%, rgba(30,198,198,1) 93.3%);
            color: white;
            border: none;
            padding: 10px 20px; 
            text-align: center;
            text-decoration: none;
            display: block; 
            font-size: 16px; 
            margin: 20px auto; 
            cursor: pointer;
            border-radius: 12px;
            width: auto; 
        }

        .card button:hover {
            background-image: linear-gradient(104.1deg, rgba(30,198,198,1) 13.6%, rgba(47,127,164,1) 49.4%, rgba(0,61,100,1) 93.3%);
            color: black; 
        }

        input:focus::placeholder {
            opacity: 0;
        }

        input::placeholder {
            transition: opacity 0.3s ease;
        }

        .profile-image {
            width: 150px;
            height: auto;
            display: block;
            margin: 20px auto;
            border: 1px solid black;
            cursor: pointer;
        }

        .profile-image:hover {
            opacity: 0.8;
        }

        .hidden-input {
            display: none;
        }

        .input-container {
            position: relative;
            width: calc(100% - 20px);
            margin: 10px 0;
        }
        .input-container i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #000;
        }
        .input-container input, .input-container select {
            width: 100%;
            padding: 10px 10px 10px 40px;
            border-radius: 15px;
            border: 1px solid black;
            box-sizing: border-box;
        }
        .readonly-input {
            background-color: #e9ecef;
            cursor: not-allowed;
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

    <div class="display-container">
        <div class="card">
            <h2>Edit Student Profile</h2>
            <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Student Image" class="profile-image">
            <input type="file" id="fileInput" name="profileImage" class="hidden-input" form="editForm">
            <form id="editForm" method="POST" action="">
                <div class="input-container">
                    <i class="fas fa-id-card"></i>
                    <input type="text" id="Idno" name="Idno" placeholder="ID Number" value="<?php echo htmlspecialchars($idNo); ?>" readonly class="readonly-input">
                </div>
                <div class="input-container">
                    <i class="fas fa-user"></i>
                    <input type="text" id="Lastname" name="Lastname" placeholder="Last Name" value="<?php echo htmlspecialchars($lastName); ?>" required>
                </div>
                <div class="input-container">
                    <i class="fas fa-user"></i>
                    <input type="text" id="Firstname" name="Firstname" placeholder="First Name" value="<?php echo htmlspecialchars($dbFirstName); ?>" required>
                </div>
                <div class="input-container">
                    <i class="fas fa-user"></i>
                    <input type="text" id="Midname" name="Midname" placeholder="Middle Name" value="<?php echo htmlspecialchars($midName); ?>">
                </div>
                <div class="input-container">
                    <i class="fas fa-book"></i>
                    <select id="Course" name="Course" required>
                        <option value="" disabled>Select a Course</option>
                        <option value="BS IN ACCOUNTANCY" <?php if ($course == 'BS IN ACCOUNTANCY') echo 'selected'; ?>>BS IN ACCOUNTANCY</option>
                        <option value="BS IN BUSINESS ADMINISTRATION" <?php if ($course == 'BS IN BUSINESS ADMINISTRATION') echo 'selected'; ?>>BS IN BUSINESS ADMINISTRATION</option>
                        <option value="BS IN CRIMINOLOGY" <?php if ($course == 'BS IN CRIMINOLOGY') echo 'selected'; ?>>BS IN CRIMINOLOGY</option>
                        <option value="BS IN CUSTOMS ADMINISTRATION" <?php if ($course == 'BS IN CUSTOMS ADMINISTRATION') echo 'selected'; ?>>BS IN CUSTOMS ADMINISTRATION</option>
                        <option value="BS IN INFORMATION TECHNOLOGY" <?php if ($course == 'BS IN INFORMATION TECHNOLOGY') echo 'selected'; ?>>BS IN INFORMATION TECHNOLOGY</option>
                        <option value="BS IN COMPUTER SCIENCE" <?php if ($course == 'BS IN COMPUTER SCIENCE') echo 'selected'; ?>>BS IN COMPUTER SCIENCE</option>
                        <option value="BS IN OFFICE ADMINISTRATION" <?php if ($course == 'BS IN OFFICE ADMINISTRATION') echo 'selected'; ?>>BS IN OFFICE ADMINISTRATION</option>
                        <option value="BS IN SOCIAL WORK" <?php if ($course == 'BS IN SOCIAL WORK') echo 'selected'; ?>>BS IN SOCIAL WORK</option>
                        <option value="BACHELOR OF SECONDARY EDUCATION" <?php if ($course == 'BACHELOR OF SECONDARY EDUCATION') echo 'selected'; ?>>BACHELOR OF SECONDARY EDUCATION</option>
                        <option value="BACHELOR OF ELEMENTARY EDUCATION" <?php if ($course == 'BACHELOR OF ELEMENTARY EDUCATION') echo 'selected'; ?>>BACHELOR OF ELEMENTARY EDUCATION</option>
                    </select>
                </div>
                <div class="input-container">
                    <i class="fas fa-graduation-cap"></i>
                    <select id="Year_Level" name="Year_Level" required>
                        <option value="" disabled>Select a Year Level</option>
                        <option value="1st Year" <?php if ($yearLevel == '1st Year') echo 'selected'; ?>>1st Year</option>
                        <option value="2nd Year" <?php if ($yearLevel == '2nd Year') echo 'selected'; ?>>2nd Year</option>
                        <option value="3rd Year" <?php if ($yearLevel == '3rd Year') echo 'selected'; ?>>3rd Year</option>
                        <option value="4th Year" <?php if ($yearLevel == '4th Year') echo 'selected'; ?>>4th Year</option>
                    </select>
                </div>
                <div class="input-container">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="Email" name="Email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="input-container">
                    <i class="fas fa-home"></i>
                    <input type="text" id="Address" name="Address" placeholder="Address" value="<?php echo htmlspecialchars($address); ?>" required>
                </div>
                <button type="submit">Save Changes</button>
            </form>
        </div>
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

        document.addEventListener('DOMContentLoaded', function() {
            const idnoInput = document.getElementById('Idno');
            idnoInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 8);
            });

            const nameInputs = ['Lastname', 'Firstname', 'Midname'];
            nameInputs.forEach(function(id) {
                const input = document.getElementById(id);
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
                });
            });

            const logoutLink = document.querySelector("a[href='logout.php']");
            if (logoutLink) {
                logoutLink.addEventListener("click", function(e) {
                    e.preventDefault();
                    fetch("../login.php", {
                        method: "POST"
                    })
                    .then(response => {
                        if (response.ok) {
                            window.location.href = "login.php";
                        } else {
                            console.error("Logout failed");
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                    });
                });
            }
        });
    </script>
</body>
</html>