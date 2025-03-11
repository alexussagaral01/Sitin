<?php

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idno = $_POST['Idno'];
    $lastname = $_POST['Lastname'];
    $firstname = $_POST['Firstname'];
    $midname = $_POST['Midname'];
    $course = $_POST['Course'];
    $year_level = $_POST['Year_Level'];
    $username = $_POST['Username'];
    $password = password_hash($_POST['Password'], PASSWORD_DEFAULT);
    $defaultImage = 'image.jpg'; 

    $sql = "INSERT INTO users (IDNO, LAST_NAME, FIRST_NAME, MID_NAME, COURSE, YEAR_LEVEL, USER_NAME, PASSWORD_HASH, UPLOAD_IMAGE) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssss", $idno, $lastname, $firstname, $midname, $course, $year_level, $username, $password, $defaultImage);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Registration successful!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
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
    <link rel="icon" href="logo/ccs.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Registration</title>
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

        .w3-input {
            padding-right: 30px;
            border-radius: 15px; 
            border: 1px solid black;
        }

        .input-container {
            position: relative;
        }

        .input-container i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: gray;
        }

        .input-container input,
        .input-container select {
            padding-left: 40px; 
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="script.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const idnoInput = document.getElementById('Idno');
            idnoInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 8);
            });

            const form = document.getElementById('registerForm');
            form.addEventListener('submit', function(event) {
                if (idnoInput.value.length !== 8) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid ID Number',
                        text: 'ID Number must be exactly 8 digits.'
                    });
                }
            });

            const nameInputs = ['Lastname', 'Firstname', 'Midname'];
            nameInputs.forEach(function(id) {
                const input = document.getElementById(id);
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
                });
            });

            const loginLink = document.querySelector("a[href='login.php']");
            loginLink.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = 'login.php';
            });
        });
    </script>
</head>
<body>
    <div class="w3-display-container w3-animate-zoom">
        <div class="w3-display-middle w3-card w3-white w3-padding" style="max-width: 500px; width: 90%; padding: 30px; border-radius: 15px; border: 1px solid black; box-shadow: 0 0 20px 5px rgba(0, 0, 0, 0.4);">
            <h2 class="w3-center" style="font-family: 'Roboto', sans-serif; font-weight: bold">Student Registration</h2>
            <form id="registerForm" method="POST" action="">
                <div class="w3-margin-bottom input-container">
                    <i class="fa fa-id-card"></i>
                    <label for="Idno"></label>
                    <input type="text" id="Idno" name="Idno" class="w3-input w3-border w3-serif" placeholder="Id Number" required>
                </div>

                <div class="w3-margin-bottom input-container">
                    <i class="fa fa-user"></i>
                    <label for="Lastname"></label>
                    <input type="text" id="Lastname" name="Lastname" class="w3-input w3-border w3-serif" placeholder="Last Name" required>
                </div>

                <div class="w3-margin-bottom input-container">
                    <i class="fa fa-user"></i>
                    <label for="Firstname"></label>
                    <input type="text" id="Firstname" name="Firstname" class="w3-input w3-border w3-serif" placeholder="First Name" required>
                </div>

                <div class="w3-margin-bottom input-container">
                    <i class="fa fa-user"></i>
                    <label for="Midname"></label>
                    <input type="text" id="Midname" name="Midname" class="w3-input w3-border w3-serif" placeholder="Middle Name">
                </div>

                <div class="w3-margin-bottom input-container">
                    <i class="fa fa-book"></i>
                    <label for="Course"></label>
                    <select id="Course" name="Course" class="w3-input w3-border w3-serif" required>
                        <option value="" disabled selected>Select a Course</option>
                        <option value="BS IN ACCOUNTANCY">BS IN ACCOUNTANCY</option>
                        <option value="BS IN BUSINESS ADMINISTRATION">BS IN BUSINESS ADMINISTRATION</option>
                        <option value="BS IN CRIMINOLOGY">BS IN CRIMINOLOGY</option>
                        <option value="BS IN CUSTOMS ADMINISTRATION">BS IN CUSTOMS ADMINISTRATION</option>
                        <option value="BS IN INFORMATION TECHNOLOGY">BS IN INFORMATION TECHNOLOGY</option>
                        <option value="BS IN COMPUTER SCIENCE">BS IN COMPUTER SCIENCE</option>
                        <option value="BS IN OFFICE ADMINISTRATION">BS IN OFFICE ADMINISTRATION</option>
                        <option value="BS IN SOCIAL WORK">BS IN SOCIAL WORK</option>
                        <option value="BACHELOR OF SECONDARY EDUCATION">BACHELOR OF SECONDARY EDUCATION</option>
                        <option value="BACHELOR OF ELEMENTARY EDUCATION">BACHELOR OF ELEMENTARY EDUCATION</option>
                    </select>
                </div>
                
                <div class="w3-margin-bottom input-container">
                    <i class="fa fa-graduation-cap"></i>
                    <label for="Year_Level"></label>
                    <select id="Year_Level" name="Year_Level" class="w3-input w3-border w3-serif" required>
                        <option value="" disabled selected>Select a Year Level</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>

                <div class="w3-margin-bottom input-container">
                    <i class="fa fa-user"></i>
                    <label for="Username"></label>
                    <input type="text" id="Username" name="Username" class="w3-input w3-border w3-serif" placeholder="User Name" required>
                </div>

                <div class="w3-margin-bottom input-container">
                    <i class="fa fa-lock"></i>
                    <label for="Password"></label>
                    <input type="password" id="Password" name="Password" class="w3-input w3-border w3-serif" placeholder="Password" required>
                </div>

                <div class="w3-center">
                    <button class="w3-button w3-blue" type="submit">Register</button>
                </div>

                <div class="w3-center w3-margin-top">
                    <p>Already have account?<a href="login.php" class="w3-text-blue">Log In</a></p>
                </div>
            </form> 
        </div>
    </div>
</body>
</html>