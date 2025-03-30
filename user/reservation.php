<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
    
}

$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$firstName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'Guest';

if ($userId) {
    $stmt = $conn->prepare("SELECT UPLOAD_IMAGE, IDNO, FIRST_NAME, LAST_NAME FROM users WHERE STUD_NUM = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($userImage, $idNumber, $firstName, $lastName);
    $stmt->fetch();
    $stmt->close();
    
    $profileImage = !empty($userImage) ? '../images/' . $userImage : "../images/image.jpg";
    $studentName = $firstName . ' ' . $lastName;
} else {
    $profileImage = "../images/image.jpg";
    $idNumber = '';
    $studentName = 'Guest';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="../logo/ccs.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif']
                    },
                }
            }
        }
    </script>
    <title>Reservation</title>
    <style>
        /* Add gradient text class for the footer */
        .gradient-text {
            background: linear-gradient(to right, #ec4899, #a855f7, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: inline-block;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-900 via-purple-800 to-pink-700 min-h-screen font-poppins">
    <!-- Header -->
    <div class="text-center text-white font-bold text-2xl py-4 relative shadow-lg" style="background: linear-gradient(to bottom right, rgb(49, 46, 129), rgb(107, 33, 168), rgb(190, 24, 93))">
        CCS SIT-IN MONITORING SYSTEM
        <div class="absolute top-4 left-6 cursor-pointer" onclick="toggleNav(this)">
            <div class="bar1 w-8 h-1 bg-white my-1 transition-all duration-300"></div>
            <div class="bar2 w-8 h-1 bg-white my-1 transition-all duration-300"></div>
            <div class="bar3 w-8 h-1 bg-white my-1 transition-all duration-300"></div>
        </div>
    </div>

    <!-- Side Navigation -->
    <div id="mySidenav" class="fixed top-0 left-0 h-screen w-72 bg-gradient-to-b from-indigo-900 to-purple-800 transform -translate-x-full transition-transform duration-300 ease-in-out z-50 shadow-xl overflow-y-auto">
        <div class="absolute top-0 right-0 m-3">
            <button onclick="closeNav()" class="text-white hover:text-pink-200 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="flex flex-col items-center mt-6">
            <div class="relative">
                <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile" class="w-20 h-20 rounded-full border-4 border-white/30 object-cover shadow-lg">
                <div class="absolute bottom-0 right-0 bg-green-500 w-3 h-3 rounded-full border-2 border-white"></div>
            </div>
            <p class="text-white font-semibold text-lg mt-2 mb-0"><?php echo htmlspecialchars($firstName); ?></p>
            <p class="text-purple-200 text-xs mb-3">Student</p>
        </div>

        <div class="px-2 py-2">
            <nav class="flex flex-col space-y-1">
                <a href="dashboard.php" class="group px-3 py-2 text-white/90 hover:bg-white/10 rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-home w-5 mr-2 text-center"></i>
                    <span class="font-medium group-hover:translate-x-1 transition-transform">HOME</span>
                </a>
                <a href="profile.php" class="group px-3 py-2 text-white/90 hover:bg-white/10 rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-user w-5 mr-2 text-center"></i>
                    <span class="font-medium group-hover:translate-x-1 transition-transform">PROFILE</span>
                </a>
                <a href="edit.php" class="group px-3 py-2 text-white/90 hover:bg-white/10 rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-edit w-5 mr-2 text-center"></i>
                    <span class="font-medium group-hover:translate-x-1 transition-transform">EDIT</span>
                </a>
                <a href="history.php" class="group px-3 py-2 text-white/90 hover:bg-white/10 rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-history w-5 mr-2 text-center"></i>
                    <span class="font-medium group-hover:translate-x-1 transition-transform">HISTORY</span>
                </a>
                <a href="reservation.php" class="group px-3 py-2 text-white/90 bg-white/20 rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-calendar-alt w-5 mr-2 text-center"></i>
                    <span class="font-medium">RESERVATION</span>
                </a>
                
                <div class="border-t border-white/10 my-2"></div>
                
                <a href="../logout.php" class="group px-3 py-2 text-white/90 hover:bg-red-500/20 rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-sign-out-alt w-5 mr-2 text-center"></i>
                    <span class="font-medium group-hover:translate-x-1 transition-transform">LOG OUT</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Reservation Form Container -->
    <div class="w-11/12 md:w-8/12 mx-auto my-8 bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
    <div class="text-white p-4 flex items-center justify-center relative overflow-hidden" style="background: linear-gradient(to bottom right, rgb(49, 46, 129), rgb(107, 33, 168), rgb(190, 24, 93))">
            <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-16 h-16 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            <i class="fas fa-calendar-alt text-2xl mr-4 relative z-10"></i>
            <h2 class="text-xl font-bold tracking-wider uppercase relative z-10">Reservation</h2>
        </div>
        <form action="reservation.php" method="post" class="p-8 space-y-6">
            <!-- Form Groups -->
            <div class="space-y-4">
                <!-- ID Number -->
                <div class="flex items-center gap-4">
                    <label for="id_number" class="w-40 font-medium text-gray-700">ID Number:</label>
                    <input type="text" id="id_number" name="id_number" value="<?php echo htmlspecialchars($idNumber); ?>" 
                        class="flex-1 p-3 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed focus:ring-2 focus:ring-purple-500/50" readonly>
                </div>
                
                <!-- Student Name -->
                <div class="flex items-center gap-4">
                    <label for="student_name" class="w-40 font-medium text-gray-700">Student Name:</label>
                    <input type="text" id="student_name" name="student_name" value="<?php echo htmlspecialchars($studentName); ?>" 
                        class="flex-1 p-3 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed focus:ring-2 focus:ring-purple-500/50" readonly>
                </div>
                
                <!-- Purpose -->
                <div class="flex items-center gap-4">
                    <label for="purpose" class="w-40 font-medium text-gray-700">Purpose:</label>
                    <select id="purpose" name="purpose" 
                        class="flex-1 p-3 border border-gray-300 rounded-lg bg-white hover:border-purple-400 focus:ring-2 focus:ring-purple-500/50 transition-colors duration-200">
                        <option value="" disabled selected>Select a Purpose</option>
                        <option value="C Programming">C Programming</option>
                        <option value="C++ Programming">C++ Programming</option>
                        <option value="C# Programming">C# Programming</option>
                        <option value="Java Programming">Java Programming</option>
                        <option value="Python Programming">Python Programming</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <!-- Lab -->
                <div class="flex items-center gap-4">
                    <label for="lab" class="w-40 font-medium text-gray-700">Lab:</label>
                    <select id="lab" name="lab" 
                        class="flex-1 p-3 border border-gray-300 rounded-lg bg-white hover:border-purple-400 focus:ring-2 focus:ring-purple-500/50 transition-colors duration-200">
                        <option value="" disabled selected>Select a Lab</option>
                        <?php foreach ([524, 526, 528, 530, 542, 544] as $labNumber): ?>
                            <option value="<?php echo $labNumber; ?>"><?php echo $labNumber; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Available PC -->
                <div class="flex items-center gap-4">
                    <label for="available_pc" class="w-40 font-medium text-gray-700">Available PC:</label>
                    <select id="available_pc" name="available_pc" 
                        class="flex-1 p-3 border border-gray-300 rounded-lg bg-white hover:border-purple-400 focus:ring-2 focus:ring-purple-500/50 transition-colors duration-200">
                        <option value="" disabled selected>Select a PC</option>
                        <?php for ($i = 1; $i <= 50; $i++): ?>
                            <option value="<?php echo $i; ?>">PC <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <!-- Time In -->
                <div class="flex items-center gap-4">
                    <label for="time_in" class="w-40 font-medium text-gray-700">Time In:</label>
                    <input type="time" id="time_in" name="time_in" 
                        class="flex-1 p-3 border border-gray-300 rounded-lg bg-white hover:border-purple-400 focus:ring-2 focus:ring-purple-500/50 transition-colors duration-200">
                </div>
                
                <!-- Date -->
                <div class="flex items-center gap-4">
                    <label for="date" class="w-40 font-medium text-gray-700">Date:</label>
                    <input type="date" id="date" name="date" 
                        class="flex-1 p-3 border border-gray-300 rounded-lg bg-white hover:border-purple-400 focus:ring-2 focus:ring-purple-500/50 transition-colors duration-200">
                </div>
                
                <!-- Remaining Session -->
                <div class="flex items-center gap-4">
                    <label for="remaining_session" class="w-40 font-medium text-gray-700">Remaining Session:</label>
                    <input type="text" id="remaining_session" name="remaining_session" value="30" 
                        class="flex-1 p-3 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed focus:ring-2 focus:ring-purple-500/50" readonly>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="flex justify-center pt-4">
                <button type="submit" 
                    class="px-8 py-3 bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white font-semibold rounded-lg 
                    hover:shadow-lg transform hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500/50">
                    Reserve Now
                </button>
            </div>
        </form>
    </div>

    <div class="py-4 px-6 bg-white/95 backdrop-blur-sm mt-8 relative">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500"></div>
        <p class="text-center text-sm text-gray-600">
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