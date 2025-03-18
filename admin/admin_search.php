<?php
session_start();
require '../db.php';

// Check if admin is not logged in, redirect to login page
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../login.php");
    exit;
}


// Add time-in handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['time_in'])) {
    $idno = $_POST['idno'];
    $fullName = $_POST['full_name'];
    $purpose = $_POST['purpose'];
    $laboratory = $_POST['laboratory'];
    
    // Check if student already has an active session
    $checkStmt = $conn->prepare("SELECT * FROM curr_sitin WHERE IDNO = ? AND STATUS = 'Active'");
    $checkStmt->bind_param("i", $idno);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Student already has an active sit-in session.";
    } else {
        // Insert new sit-in record
        $stmt = $conn->prepare("INSERT INTO curr_sitin (IDNO, FULL_NAME, PURPOSE, LABORATORY, TIME_IN, DATE, STATUS) VALUES (?, ?, ?, ?, NOW(), CURDATE(), 'Active')");
        $stmt->bind_param("isss", $idno, $fullName, $purpose, $laboratory);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Time-in recorded successfully.";
            header("Location: admin_sitin.php");
            exit();
        } else {
            $_SESSION['error'] = "Error recording time-in.";
        }
    }
}

// Only fetch student data when search button is clicked via POST
$student = null;
$searched = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search']) && !empty($_POST['search'])) {
    $searched = true;
    $stmt = $conn->prepare("SELECT * FROM users WHERE IDNO = ?");
    $stmt->bind_param("s", $_POST['search']);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Search</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="../logo/ccs.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
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
            <img src="../images/image.jpg" alt="Logo" class="w-24 h-24 rounded-full border-2 border-white object-cover mb-2">
            <p class="text-white font-bold text-lg mb-3">Admin</p>
        </div>

        <nav class="flex flex-col space-y-0.5 px-2">
            <div class="overflow-hidden">
                <a href="admin_dashboard.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-home w-6 text-base"></i>
                    <span class="text-sm font-medium">HOME</span>
                </a>
            </div>
            <div class="overflow-hidden">
                <a href="admin_search.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-search w-6 text-base"></i>
                    <span class="text-sm font-medium">SEARCH</span>
                </a>
            </div>
            <div class="overflow-hidden">
                <a href="admin_sitin.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-user-check w-6 text-base"></i>
                    <span class="text-sm font-medium">SIT-IN</span>
                </a>
            </div>
            <div class="overflow-hidden">
                <a href="admin_sitinrec.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-book w-6 text-base"></i>
                    <span class="text-sm font-medium">VIEW SIT-IN RECORDS</span>
                </a>
            </div>
            <div class="overflow-hidden">
                <a href="admin_studlist.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-list w-6 text-base"></i>
                    <span class="text-sm font-medium">VIEW LIST OF STUDENT</span>
                </a>
            </div>
            <div class="overflow-hidden">
                <a href="admin_report.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-chart-line w-6 text-base"></i>
                    <span class="text-sm font-medium">SIT-IN REPORT</span>
                </a>
            </div>
            <div class="overflow-hidden">
                <a href="#" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-comments w-6 text-base"></i>
                    <span class="text-sm font-medium">VIEW FEEDBACKS</span>
                </a>
            </div>
            <div class="overflow-hidden">
                <a href="#" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-chart-pie w-6 text-base"></i>
                    <span class="text-sm font-medium">VIEW DAILY ANALYTICS</span>
                </a>
            </div>
            <div class="overflow-hidden">
                <a href="#" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-calendar-check w-6 text-base"></i>
                    <span class="text-sm font-medium">RESERVATION/APPROVAL</span>
                </a>
            </div>
        </nav>

        <div class="mt-3 px-2 pb-2">
            <a href="../logout.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                <i class="fas fa-sign-out-alt w-6 text-base"></i>
                <span class="text-sm font-medium">LOG OUT</span>
            </a>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="px-8 py-8 w-full flex flex-wrap gap-8">
        <div class="flex-1 min-w-[400px] bg-white rounded-xl shadow-lg overflow-hidden h-[700px] border border-[rgba(255,255,255,1)]">
            <div class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white p-4 flex items-center justify-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <i class="fas fa-search text-2xl mr-4 relative z-10"></i>
                <h2 class="text-xl font-bold tracking-wider uppercase relative z-10">Search Student</h2>
            </div>
            <div class="p-6 h-[calc(100%-4rem)] flex flex-col">
                <div class="mb-6">
                    <form method="POST" action="" class="space-y-3">
                        <div class="flex gap-2">
                            <input type="text" name="search" placeholder="Enter ID Number..." 
                                   class="flex-1 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white rounded-lg hover:opacity-90 group">
                                <i class="fas fa-search group-hover:text-black transition-colors"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Student Results Area -->
                <div class="flex-1 overflow-y-auto">
                    <div class="space-y-6 pr-2">
                        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $searched): ?>
                            <?php if ($student): ?>
                                <!-- Student Card -->
                                <div class="bg-gradient-to-br from-white to-gray-50 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border-l-4 border-purple-500">
                                    <!-- Student Header -->
                                    <div class="flex flex-col lg:flex-row items-center gap-8">
                                        <div class="relative group">
                                            <?php if (!empty($student['UPLOAD_IMAGE'])): ?>
                                                <img src="../images/<?php echo htmlspecialchars($student['UPLOAD_IMAGE']); ?>" 
                                                     alt="Student Photo"
                                                     class="w-32 h-32 rounded-2xl object-cover shadow-md group-hover:scale-105 transition-transform duration-300"
                                                     onerror="this.src='../images/image.jpg'">
                                            <?php else: ?>
                                                <img src="../images/image.jpg"
                                                     alt="Default Photo"
                                                     class="w-32 h-32 rounded-2xl object-cover shadow-md group-hover:scale-105 transition-transform duration-300">
                                            <?php endif; ?>
                                            <div class="absolute -bottom-2 -right-2 bg-green-400 rounded-full p-2">
                                                <i class="fas fa-check text-white"></i>
                                            </div>
                                        </div>

                                        <div class="flex-1 text-center lg:text-left">
                                            <h3 class="text-2xl font-bold text-gray-800 mb-2">
                                                <?php echo htmlspecialchars($student['FIRST_NAME'] . ' ' . $student['LAST_NAME']); ?>
                                            </h3>
                                            <div class="inline-block bg-purple-100 text-purple-700 px-4 py-1 rounded-full font-medium mb-2">
                                                <?php echo htmlspecialchars($student['IDNO']); ?>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                                <div class="bg-blue-50 p-3 rounded-xl">
                                                    <p class="text-blue-600 text-sm font-medium">Course</p>
                                                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($student['COURSE']); ?></p>
                                                </div>
                                                <div class="bg-purple-50 p-3 rounded-xl">
                                                    <p class="text-purple-600 text-sm font-medium">Year Level</p>
                                                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($student['YEAR_LEVEL']); ?></p>
                                                </div>
                                                <div class="bg-pink-50 p-3 rounded-xl">
                                                    <p class="text-pink-600 text-sm font-medium">Email</p>
                                                    <p class="font-semibold text-gray-800 truncate"><?php echo htmlspecialchars($student['EMAIL']); ?></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="w-full lg:w-48 bg-white p-4 rounded-xl shadow-sm">
                                            <div class="text-center mb-3">
                                                <p class="text-sm text-gray-600">Sessions Remaining</p>
                                                <p class="text-3xl font-bold text-purple-600"><?php echo htmlspecialchars($student['SESSION']); ?></p>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
                                                <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-2.5 rounded-full transition-all duration-500" 
                                                     style="width: <?php echo ($student['SESSION'] / 30) * 100; ?>%"></div>
                                            </div>
                                            <p class="text-xs text-gray-500 text-center">
                                                <?php echo htmlspecialchars($student['SESSION']); ?>/30 sessions
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Register Session Section -->
                                    <div class="mt-8 pt-6 border-t border-gray-200">
                                        <h4 class="text-lg font-semibold mb-4 flex items-center text-purple-700">
                                            <i class="fas fa-clipboard-list mr-2"></i>
                                            New Sit-In Session Registration
                                        </h4>
                                        
                                        <?php if (isset($_SESSION['error'])): ?>
                                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-r-lg">
                                                <?php 
                                                    echo $_SESSION['error'];
                                                    unset($_SESSION['error']);
                                                ?>
                                            </div>
                                        <?php endif; ?>

                                        <form method="POST" action="" class="space-y-4">
                                            <input type="hidden" name="idno" value="<?php echo htmlspecialchars($student['IDNO']); ?>">
                                            <input type="hidden" name="full_name" value="<?php echo htmlspecialchars($student['FIRST_NAME'] . ' ' . $student['LAST_NAME']); ?>">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div class="relative">
                                                    <select name="purpose" class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl appearance-none cursor-pointer hover:border-purple-500 transition-colors" required>
                                                        <option value=""disabled selected>Select Purpose</option>
                                                        <option value="C Programming">C Programming</option>
                                                        <option value="C++ Programming">C++ Programming</option>
                                                        <option value="C# Programming">C# Programming</option>
                                                        <option value="Java Programming">Java Programming</option>
                                                        <option value="Python Programming">Python Programming</option>
                                                        <option value="Other">Other</option>
                                                    </select>
                                                    <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none text-gray-500">
                                                        <i class="fas fa-chevron-down"></i>
                                                    </div>
                                                </div>
                                                <div class="relative">
                                                    <select name="laboratory" class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl appearance-none cursor-pointer hover:border-purple-500 transition-colors" required>
                                                        <option value=""disabled selected>Select Laboratory</option>
                                                        <option value="Lab 524">Lab 524</option>
                                                        <option value="Lab 526">Lab 526</option>
                                                        <option value="Lab 528">Lab 528</option>
                                                        <option value="Lab 530">Lab 530</option>
                                                        <option value="Lab 542">Lab 542</option>
                                                        <option value="Lab 544">Lab 544</option>
                                                    </select>
                                                    <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none text-gray-500">
                                                        <i class="fas fa-chevron-down"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex justify-end">
                                                <button type="submit" name="time_in" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-xl hover:opacity-90 transform hover:scale-105 transition-all duration-300 flex items-center gap-2 shadow-lg">
                                                    <i class="fas fa-clock"></i>
                                                    Time - In
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-8">
                                    <div class="bg-red-50 p-6 rounded-xl inline-block">
                                        <i class="fas fa-user-times text-4xl text-red-400 mb-3"></i>
                                        <p class="text-gray-600">No student found with ID Number: <span class="font-semibold"><?php echo htmlspecialchars($_POST['search']); ?></span></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
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