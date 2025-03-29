<?php
session_start();
require '../db.php'; // Updated path

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
    
}

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
        /* Add gradient text class for the footer */
        .gradient-text {
            background: linear-gradient(to right, #ec4899, #a855f7, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: inline-block;
        }
        
        /* New hover effect for table rows */
        .hover-row:hover {
            background-color: rgba(99, 102, 241, 0.05);
            transform: translateX(4px);
            transition: all 0.3s ease;
        }
        
        /* Animation for profile image */
        @keyframes pulse-border {
            0% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(99, 102, 241, 0); }
            100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0); }
        }
        
        .pulse-image {
            animation: pulse-border 2s infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)]">
    <!-- Header - Kept unchanged -->
    <div class="text-center bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white font-bold text-2xl py-4 relative">
        CCS SIT-IN MONITORING SYSTEM
        <div class="absolute top-4 left-6 cursor-pointer" onclick="toggleNav(this)">
            <div class="bar1 w-8 h-1 bg-white my-1 transition-all duration-300"></div>
            <div class="bar2 w-8 h-1 bg-white my-1 transition-all duration-300"></div>
            <div class="bar3 w-8 h-1 bg-white my-1 transition-all duration-300"></div>
        </div>
    </div>

    <!-- Side Navigation - Kept unchanged -->
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
            <a href="../logout.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                <i class="fas fa-sign-out-alt w-6 text-base"></i>
                <span class="text-sm font-medium">LOG OUT</span>
            </a>
        </div>
    </div>

    <!-- Student Info Section with original header style -->
    <div class="w-11/12 md:w-8/12 mx-auto my-8 bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
        <!-- Original header style kept intact -->
        <div class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white p-4 flex items-center justify-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-16 h-16 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            <i class="fas fa-user-graduate text-2xl mr-4 relative z-10"></i>
            <h2 class="text-xl font-bold tracking-wider uppercase relative z-10">Student Information</h2>
        </div>

        <!-- Redesigned content area -->
        <div class="p-6">
            <!-- Profile image with pulse effect -->
            <div class="flex justify-center mb-6">
                <div class="w-36 h-36 rounded-full overflow-hidden border-4 border-purple-100 pulse-image">
                    <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Student Image" class="w-full h-full object-cover">
                </div>
            </div>

            <!-- Information Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl mx-auto">
                <!-- Left Column -->
                <div class="space-y-3">
                    <div class="hover-row rounded-lg p-3 border-l-4 border-indigo-400">
                        <p class="text-sm text-gray-500">ID NUMBER</p>
                        <div class="flex items-center">
                            <i class="fas fa-id-card text-indigo-400 mr-2"></i>
                            <p class="font-medium"><?php echo htmlspecialchars($idNo); ?></p>
                        </div>
                    </div>
                    
                    <div class="hover-row rounded-lg p-3 border-l-4 border-indigo-400">
                        <p class="text-sm text-gray-500">NAME</p>
                        <div class="flex items-center">
                            <i class="fas fa-user text-indigo-400 mr-2"></i>
                            <p class="font-medium"><?php echo htmlspecialchars($fullName); ?></p>
                        </div>
                    </div>
                    
                    <div class="hover-row rounded-lg p-3 border-l-4 border-indigo-400">
                        <p class="text-sm text-gray-500">YEAR LEVEL</p>
                        <div class="flex items-center">
                            <i class="fas fa-graduation-cap text-indigo-400 mr-2"></i>
                            <p class="font-medium"><?php echo htmlspecialchars($yearLevel); ?></p>
                        </div>
                    </div>
                    
                    <div class="hover-row rounded-lg p-3 border-l-4 border-indigo-400">
                        <p class="text-sm text-gray-500">COURSE</p>
                        <div class="flex items-center">
                            <i class="fas fa-book text-indigo-400 mr-2"></i>
                            <p class="font-medium"><?php echo htmlspecialchars($course); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column -->
                <div class="space-y-3">
                    <div class="hover-row rounded-lg p-3 border-l-4 border-purple-400">
                        <p class="text-sm text-gray-500">EMAIL</p>
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-purple-400 mr-2"></i>
                            <p class="font-medium"><?php echo htmlspecialchars($email); ?></p>
                        </div>
                    </div>
                    
                    <div class="hover-row rounded-lg p-3 border-l-4 border-purple-400">
                        <p class="text-sm text-gray-500">ADDRESS</p>
                        <div class="flex items-center">
                            <i class="fas fa-home text-purple-400 mr-2"></i>
                            <p class="font-medium"><?php echo htmlspecialchars($address); ?></p>
                        </div>
                    </div>
                    
                    <div class="hover-row rounded-lg p-3 border-l-4 border-purple-400">
                        <p class="text-sm text-gray-500">SESSION</p>
                        <div class="flex items-center">
                            <i class="fas fa-clock text-purple-400 mr-2"></i>
                            <p class="font-medium"><?php echo htmlspecialchars($session); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="py-3 px-6 bg-white relative mt-8">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500"></div>
        <p class="text-center text-xs text-gray-600">
            &copy; 2025 CCS Sit-in Monitoring System | <span class="gradient-text font-medium">UC - College of Computer Studies</span>
        </p>
    </div>

    <script>
        function toggleNav(x) {
            document.getElementById("mySidenav").classList.toggle("-translate-x-0");
            document.getElementById("mySidenav").classList.toggle("-translate-x-full");
        }

        function closeNav() {
            document.getElementById("mySidenav").classList.remove("-translate-x-0");
            document.getElementById("mySidenav").classList.add("-translate-x-full");
        }
    </script>
</body>
</html>