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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="logo/ccs.png" type="image/x-icon">
    <title>Registration</title>
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
<body class="h-screen flex items-center justify-center" style="background-image: linear-gradient(111.3deg, rgba(74,105,187,1) 9.6%, rgba(205,77,204,1) 93.6%);">
    <div class="fixed bg-white p-8 rounded-lg shadow-lg max-w-2xl w-full" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <h2 class="text-center text-2xl font-bold mb-6">Student Registration</h2>
        <form id="registerForm" method="POST" action="">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4 relative col-span-1 md:col-span-2">
                    <i class="fa fa-id-card absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 bg-white px-2"></i>
                    <input type="text" id="Idno" name="Idno" class="w-full pl-12 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Id Number" required>
                </div>

                <div class="mb-4 relative col-span-1 md:col-span-2 grid grid-cols-3 gap-4">
                    <div class="relative">
                        <i class="fa fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 bg-white px-2"></i>
                        <input type="text" id="Lastname" name="Lastname" class="w-full pl-12 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Last Name" required>
                    </div>
                    <div class="relative">
                        <i class="fa fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 bg-white px-2"></i>
                        <input type="text" id="Firstname" name="Firstname" class="w-full pl-12 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="First Name" required>
                    </div>
                    <div class="relative">
                        <i class="fa fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 bg-white px-2"></i>
                        <input type="text" id="Midname" name="Midname" class="w-full pl-12 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Middle Name">
                    </div>
                </div>

                <div class="mb-4 relative">
                    <i class="fa fa-book absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 bg-white px-2"></i>
                    <select id="Course" name="Course" class="w-full pl-12 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
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
                
                <div class="mb-4 relative">
                    <i class="fa fa-graduation-cap absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 bg-white px-2"></i>
                    <select id="Year_Level" name="Year_Level" class="w-full pl-12 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="" disabled selected>Select a Year Level</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>

                <div class="mb-4 relative col-span-1 md:col-span-2">
                    <i class="fa fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 bg-white px-2"></i>
                    <input type="text" id="Username" name="Username" class="w-full pl-12 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="User Name" required>
                </div>

                <div class="mb-4 relative col-span-1 md:col-span-2">
                    <i class="fa fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 bg-white px-2"></i>
                    <input type="password" id="Password" name="Password" class="w-full pl-12 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Password" required>
                </div>
            </div>

            <div class="text-center">
                <button class="bg-gradient-to-r from-purple-700 to-pink-500 text-white py-2 px-4 rounded-lg hover:from-pink-500 hover:to-purple-700 hover:text-black" type="submit">Register</button>
            </div>

            <div class="text-center mt-4">
                <p>Already have an account? <a href="login.php" class="text-blue-500">Log In</a></p>
            </div>
        </form> 
    </div>
</body>
</html>