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
    <link rel="icon" href="../logo/ccs.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Profile</title>
    <style>
        /* Only keep necessary styles that aren't handled by Tailwind */
        .change .bar1 {
            transform: rotate(-45deg) translate(-9px, 6px);
        }
        .change .bar2 {opacity: 0;}
        .change .bar3 {
            transform: rotate(45deg) translate(-8px, -8px);
        }
    </style>
</head>
<body class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)]">
    <!-- Header -->
    <div class="text-center bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white font-bold text-2xl py-4 relative">
        CCS SIT-IN MONITORING SYSTEM
        <div class="absolute top-4 left-6 cursor-pointer" onclick="toggleNav(this)">
            <div class="bar1 w-8 h-1 bg-white my-1 transition-all duration-300"></div>
            <div class="bar2 w-8 h-1 bg-white my-1 transition-all duration-300"></div>
            <div class="bar3 w-8 h-1 bg-white my-1 transition-all duration-300"></div>
        </div>
    </div>

    <!-- Side Navigation -->
    <div id="mySidenav" class="fixed top-0 left-0 h-screen w-64 bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] transform -translate-x-full transition-transform duration-300 ease-in-out z-50 shadow-lg overflow-y-auto">
        <span class="absolute top-0 right-0 p-4 text-3xl cursor-pointer text-white hover:text-gray-200" onclick="closeNav()">&times;</span>
        
        <div class="flex flex-col items-center mt-4">
            <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Logo" class="w-24 h-24 rounded-full border-2 border-white object-cover mb-2">
            <p class="text-white font-bold text-lg mb-3"><?php echo htmlspecialchars($firstName); ?></p>
        </div>

        <nav class="flex flex-col space-y-0.5 px-2">
            <div class="overflow-hidden">
                <a href="dashboard.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-home w-6 text-base"></i>
                    <span class="text-sm font-medium">HOME</span>
                </a>
            </div>
            <div class="overflow-hidden">
                <a href="profile.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-user w-6 text-base"></i>
                    <span class="text-sm font-medium">PROFILE</span>
                </a>
            </div>
            <div class="overflow-hidden">
                <a href="edit.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-edit w-6 text-base"></i>
                    <span class="text-sm font-medium">EDIT</span>
                </a>
            </div>
            <div class="overflow-hidden">
                <a href="history.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-history w-6 text-base"></i>
                    <span class="text-sm font-medium">HISTORY</span>
                </a>
            </div>
            <div class="overflow-hidden">
                <a href="reservation.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-calendar-alt w-6 text-base"></i>
                    <span class="text-sm font-medium">RESERVATION</span>
                </a>
            </div>

        <div class="overflow-hidden">
            <a href="../login.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                <i class="fas fa-sign-out-alt w-6 text-base"></i>
                <span class="text-sm font-medium">LOG OUT</span> <!-- Updated font size and weight -->
            </a>
        </div>
    </div>

    <!-- Update the student info section -->
    <div class="w-11/12 md:w-8/12 mx-auto my-8 bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
        <div class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white py-4 px-6 text-xl font-bold uppercase tracking-wider text-center flex items-center justify-center">
            <i class="fas fa-user-graduate mr-3"></i>
            Student Information
        </div>
        <div class="p-6">
            <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Student Image" class="w-36 h-36 rounded-full border border-gray-300 mx-auto mb-6 object-cover">
            <table class="w-full max-w-2xl mx-auto">
                <tr class="border-b border-gray-200">
                    <th class="py-3 px-4 text-left flex items-center text-gray-700">
                        <i class="fas fa-id-card w-8"></i> ID NUMBER:
                    </th>
                    <td class="py-3 px-4 text-gray-800"><?php echo htmlspecialchars($idNo); ?></td>
                </tr>
                <tr class="border-b border-gray-200">
                    <th class="py-3 px-4 text-left flex items-center text-gray-700">
                        <i class="fas fa-user w-8"></i> NAME:
                    </th>
                    <td class="py-3 px-4 text-gray-800"><?php echo htmlspecialchars($fullName); ?></td>
                </tr>
                <tr class="border-b border-gray-200">
                    <th class="py-3 px-4 text-left flex items-center text-gray-700">
                        <i class="fas fa-graduation-cap w-8"></i> YEAR LEVEL:
                    </th>
                    <td class="py-3 px-4 text-gray-800"><?php echo htmlspecialchars($yearLevel); ?></td>
                </tr>
                <tr class="border-b border-gray-200">
                    <th class="py-3 px-4 text-left flex items-center text-gray-700">
                        <i class="fas fa-book w-8"></i> COURSE:
                    </th>
                    <td class="py-3 px-4 text-gray-800"><?php echo htmlspecialchars($course); ?></td>
                </tr>
                <tr class="border-b border-gray-200">
                    <th class="py-3 px-4 text-left flex items-center text-gray-700">
                        <i class="fas fa-envelope w-8"></i> EMAIL:
                    </th>
                    <td class="py-3 px-4 text-gray-800"><?php echo htmlspecialchars($email); ?></td>
                </tr>
                <tr class="border-b border-gray-200">
                    <th class="py-3 px-4 text-left flex items-center text-gray-700">
                        <i class="fas fa-home w-8"></i> ADDRESS:
                    </th>
                    <td class="py-3 px-4 text-gray-800"><?php echo htmlspecialchars($address); ?></td>
                </tr>
                <tr class="border-b border-gray-200">
                    <th class="py-3 px-4 text-left flex items-center text-gray-700">
                        <i class="fas fa-clock w-8"></i> SESSION:
                    </th>
                    <td class="py-3 px-4 text-gray-800"><?php echo htmlspecialchars($session); ?></td>
                </tr>
            </table>
        </div>
    </div>
    
    <script>
        function toggleNav(x) {
            x.classList.toggle("change");
            document.getElementById("mySidenav").classList.toggle("-translate-x-full");
        }

        function closeNav() {
            document.getElementById("mySidenav").classList.add("-translate-x-full");
            document.querySelector(".change").classList.remove("change");
        }
    </script>
</body>
</html>