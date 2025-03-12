<?php
session_start();
require '../db.php';

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="../logo/ccs.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Reservation</title>
    <style>
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

    <!-- Reservation Form Container -->
    <div class="max-w-2xl mx-auto mt-8 mb-8 bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-200/50 backdrop-blur-sm">
        <div class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white p-6 text-center">
            <h2 class="text-2xl font-bold uppercase tracking-wider flex items-center justify-center">
                <i class="fas fa-calendar-alt mr-3"></i>
                Reservation
            </h2>
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
    
    <script>
        function toggleNav(x) {
            x.classList.toggle("change");
            document.getElementById("mySidenav").classList.toggle("-translate-x-full");
        }

        function closeNav() {
            document.getElementById("mySidenav").classList.add("-translate-x-full");
            document.querySelector(".change")?.classList.remove("change");
        }

    </script>
</body>
</html>