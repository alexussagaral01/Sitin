<?php
session_start();
require '../db.php'; // Add database connection

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
                <a href="#" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
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
            <a href="../login.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                <i class="fas fa-sign-out-alt w-6 text-base"></i>
                <span class="text-sm font-medium">LOG OUT</span>
            </a>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="px-8 py-8 w-full flex flex-wrap gap-8">
        <div class="flex-1 min-w-[400px] bg-white rounded-xl shadow-lg overflow-hidden h-[700px] border border-[rgba(255,255,255,1)]">
            <div class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white p-4 flex items-center justify-center">
                <i class="fas fa-search text-2xl mr-3"></i>
                <h2 class="text-xl font-bold tracking-wider uppercase text-center">Search Student</h2>
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
                    <div class="space-y-4 pr-2">
                        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $searched): ?>
                            <?php if ($student): ?>
                                <!-- Student Card -->
                                <div class="bg-white p-6 rounded-lg shadow-md flex flex-col md:flex-row border border-gray-200">
                                    <!-- Left side with student info -->
                                    <div class="md:w-1/4 flex flex-col items-center">
                                        <?php if (!empty($student['UPLOAD_IMAGE'])): ?>
                                            <img src="../images/<?php echo htmlspecialchars($student['UPLOAD_IMAGE']); ?>" 
                                                 alt="Student Photo"
                                                 class="w-28 h-28 rounded-full object-cover border-2 border-gray-200 mb-3"
                                                 onerror="this.src='../images/image.jpg'">
                                        <?php else: ?>
                                            <img src="../images/image.jpg"
                                                 alt="Default Photo"
                                                 class="w-28 h-28 rounded-full object-cover border-2 border-gray-200 mb-3">
                                        <?php endif; ?>
                                        <h3 class="text-lg font-semibold text-center">
                                            <?php echo htmlspecialchars($student['FIRST_NAME'] . ' ' . $student['LAST_NAME']); ?>
                                        </h3>
                                        <p class="text-blue-600 font-medium text-center"><?php echo htmlspecialchars($student['IDNO']); ?></p>
                                        <div class="mt-2 bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                                            Active Student
                                        </div>
                                        
                                        <div class="w-full mt-4 px-4">
                                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                                <span>Remaining:</span>
                                                <span><?php echo htmlspecialchars($student['SESSION']); ?></span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo ($student['SESSION'] / 30) * 100; ?>%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Right side with details -->
                                    <div class="md:w-3/4 md:pl-6 mt-6 md:mt-0">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <p class="text-gray-500 text-sm">Course</p>
                                                <p class="font-medium"><?php echo htmlspecialchars($student['COURSE']); ?></p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500 text-sm">Year Level</p>
                                                <p class="font-medium"><?php echo htmlspecialchars($student['YEAR_LEVEL']); ?></p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500 text-sm">Email</p>
                                                <p class="font-medium"><?php echo htmlspecialchars($student['EMAIL']); ?></p>
                                            </div>
                                        </div>
                                        
                                        <!-- Register Session Section -->
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <h4 class="text-md font-semibold mb-3 flex items-center">
                                                <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>
                                                Register New Sit-In Session
                                            </h4>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <select class="w-full p-2 border border-gray-300 rounded-lg">
                                                        <option value="">Select Purpose</option>
                                                        <option value="research">Research</option>
                                                        <option value="assignment">Assignment</option>
                                                        <option value="project">Project</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <select class="w-full p-2 border border-gray-300 rounded-lg">
                                                        <option value="">Select Laboratory</option>
                                                        <option value="lab1">Laboratory 1</option>
                                                        <option value="lab2">Laboratory 2</option>
                                                        <option value="lab3">Laboratory 3</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-4 flex justify-end">
                                                <button class="px-4 py-2 bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white rounded-lg hover:opacity-90 flex items-center">
                                                    <i class="fas fa-clock mr-2"></i>
                                                    Time - In
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <p class="text-gray-500">No student found with ID Number: <?php echo htmlspecialchars($_POST['search']); ?></p>
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