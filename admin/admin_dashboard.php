<?php
session_start();
require '../db.php'; // Add database connection

$firstName = isset($_SESSION['admin']) && $_SESSION['admin'] === true ? 'Admin' : (isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'Guest');
$profileImage = isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : '../images/image.jpg';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_announcement'])) {
    $content = $_POST['new_announcement'];
    $createdBy = 'ADMIN';

    $stmt = $conn->prepare("INSERT INTO announcement (CONTENT, CREATED_DATE, CREATED_BY) VALUES (?, NOW(), ?)");
    $stmt->bind_param("ss", $content, $createdBy);
    $stmt->execute();
    $stmt->close();
    
    // Redirect to refresh the page and prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch announcements from the database
$announcements = [];
$result = $conn->query("SELECT CONTENT, CREATED_DATE, CREATED_BY FROM announcement WHERE CREATED_BY = 'ADMIN' ORDER BY CREATED_DATE DESC");
while ($row = $result->fetch_assoc()) {
    $announcements[] = $row;
}

// Sample statistics data (hardcoded for UI only)
$totalStudents = 0;
$currentSitIns = 0;
$totalSitIns = 0;
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
    <title>Admin Dashboard</title>
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
        <!-- Statistics Card -->
        <div class="flex-1 min-w-[400px] bg-white rounded-xl shadow-lg overflow-hidden h-[700px] border border-[rgba(255,255,255,1)]">
            <div class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white p-4 flex items-center">
                <i class="fas fa-chart-bar text-2xl mr-3"></i>
                <h2 class="text-xl font-bold tracking-wider uppercase">Statistics</h2>
            </div>
            <div class="p-6 h-[calc(100%-4rem)] flex flex-col">
                <!-- Stats Section -->
                <div class="mb-6 space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="font-bold text-gray-700">Students Registered:</span>
                        <span class="text-blue-600 font-semibold"><?php echo $totalStudents; ?></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="font-bold text-gray-700">Currently Sit-In:</span>
                        <span class="text-blue-600 font-semibold"><?php echo $currentSitIns; ?></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="font-bold text-gray-700">Total Sit-Ins:</span>
                        <span class="text-blue-600 font-semibold"><?php echo $totalSitIns; ?></span>
                    </div>
                </div>
                
                <!-- Chart Container -->
                <div class="flex-1 relative min-h-[350px]">
                    <canvas id="sitInChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Announcements Card -->
        <div class="flex-1 min-w-[400px] bg-white rounded-xl shadow-lg overflow-hidden h-[700px] border border-[rgba(255,255,255,1)]">
            <div class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white p-4 flex items-center">
                <i class="fas fa-bullhorn text-2xl mr-3"></i>
                <h2 class="text-xl font-bold tracking-wider uppercase">Announcements</h2>
            </div>
            <div class="p-6 h-[calc(100%-4rem)] flex flex-col">
                <div class="mb-6">
                    <form action="" method="post" class="space-y-3">
                        <textarea 
                            name="new_announcement" 
                            placeholder="Type your announcement here..." 
                            required
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-y min-h-[100px]"
                        ></textarea>
                        <button type="submit" 
                            class="bg-gradient-to-r from-purple-700 to-pink-500 text-white py-2 px-4 rounded-lg hover:from-pink-500 hover:to-purple-700 hover:text-black transition-all">
                            Post Announcement
                        </button>
                    </form>
                </div>

                <div class="flex-1 overflow-y-auto">
                    <h3 class="font-bold text-gray-700 mb-3">Posted Announcements</h3>
                    <div class="space-y-4 pr-2">
                        <?php if (empty($announcements)): ?>
                            <p class="text-gray-500 text-center py-4">No announcements available.</p>
                        <?php else: ?>
                            <?php foreach (array_reverse($announcements) as $announcement): ?>
                                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-[rgba(0,61,100,1)]">
                                    <div class="text-sm font-bold text-[rgba(0,61,100,1)] mb-2">
                                        <?php echo htmlspecialchars($announcement['CREATED_BY']); ?> | 
                                        <?php echo date('Y-M-d', strtotime($announcement['CREATED_DATE'])); ?>
                                    </div>
                                    <div class="text-gray-700 pl-2 border-l-2 border-gray-200">
                                        <?php echo htmlspecialchars($announcement['CONTENT']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script>
        function toggleNav(x) {
            x.classList.toggle("change");
            document.getElementById("mySidenav").classList.toggle("-translate-x-full");
        }

        function closeNav() {
            document.getElementById("mySidenav").classList.add("-translate-x-full");
            document.querySelector(".change")?.classList.remove("change");
        }
        
        // Initialize the chart
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('sitInChart').getContext('2d');
            
            // Sample data for UI demonstration
            const programData = {
                labels: ['C', 'C++', 'C#', 'Java', 'Python', 'Other'],
                datasets: [{
                    data: [45, 25, 15, 8, 5, 2],
                    backgroundColor: [
                        '#36A2EB', // Blue
                        '#FF6384', // Pink
                        '#FFCE56', // Yellow
                        '#4BC0C0', // Teal
                        '#9966FF', // Purple
                        '#FF9F40'  // Orange
                    ],
                    borderWidth: 1
                }]
            };
            
            new Chart(ctx, {
                type: 'pie',
                data: programData,
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 1.4, // Adjusted for better fit
                    layout: {
                        padding: {
                            left: 15,
                            right: 15,
                            top: 10,
                            bottom: 10
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'right',
                            align: 'center',
                            labels: {
                                boxWidth: 12,
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Sit-In Distribution by Program',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            padding: {
                                top: 5,
                                bottom: 15
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>