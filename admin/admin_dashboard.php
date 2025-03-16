<?php
session_start();
require '../db.php'; // Add database connection

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

// Get actual statistics from database
$totalStudents = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM users");
if ($row = $result->fetch_assoc()) {
    $totalStudents = $row['total'];
}

// Get current active sit-ins
$currentSitIns = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM curr_sitin WHERE STATUS = 'Active' AND TIME_OUT IS NULL");
if ($row = $result->fetch_assoc()) {
    $currentSitIns = $row['total'];
}

// Get total sit-ins (including completed ones)
$totalSitIns = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM curr_sitin");
if ($row = $result->fetch_assoc()) {
    $totalSitIns = $row['total'];
}

// Initialize program counts correctly based on PURPOSE enum values
$programCounts = [
    'C Programming' => 0,
    'C++ Programming' => 0,
    'C# Programming' => 0,
    'Java Programming' => 0,
    'Python Programming' => 0,
    'Other' => 0
];

$result = $conn->query("SELECT PURPOSE, COUNT(*) as count FROM curr_sitin GROUP BY PURPOSE");
while ($row = $result->fetch_assoc()) {
    $purpose = $row['PURPOSE'];
    if (array_key_exists($purpose, $programCounts)) {
        $programCounts[$purpose] = $row['count'];
    } else {
        $programCounts['Other'] += $row['count'];
    }
}

// Convert to JavaScript array
$programCountsJSON = json_encode(array_values($programCounts));

// Get students by year level
$yearLevelCounts = [
    '1st Year' => 0,
    '2nd Year' => 0,
    '3rd Year' => 0,
    '4th Year' => 0
];

$result = $conn->query("SELECT YEAR_LEVEL, COUNT(*) as count FROM users GROUP BY YEAR_LEVEL");
while ($row = $result->fetch_assoc()) {
    if (isset($yearLevelCounts[$row['YEAR_LEVEL']])) {
        $yearLevelCounts[$row['YEAR_LEVEL']] = $row['count'];
    }
}

// Convert to JavaScript array
$yearLevelJSON = json_encode(array_values($yearLevelCounts));
$yearLevelLabelsJSON = json_encode(array_keys($yearLevelCounts));
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
            <a href="../login.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                <i class="fas fa-sign-out-alt w-6 text-base"></i>
                <span class="text-sm font-medium">LOG OUT</span>
            </a>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="px-8 py-8 w-full flex flex-wrap gap-8">
        <!-- Statistics Card -->
        <div class="flex-1 min-w-[400px] bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-2xl overflow-hidden h-[700px] backdrop-blur-sm border border-white/30">
            <div class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white p-4 flex items-center justify-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <i class="fas fa-chart-bar text-2xl mr-4 relative z-10"></i>
                <h2 class="text-xl font-bold tracking-wider uppercase relative z-10">Statistics</h2>
            </div>
            <div class="p-8 h-[calc(100%-5rem)] flex flex-col">
                <!-- Stats Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <!-- Students Registered Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-4 shadow-lg border border-blue-100/50 transform hover:scale-102 transition-transform duration-300">
                        <div class="flex flex-col items-center text-center">
                            <div class="mb-2 bg-blue-500/10 p-2 rounded-full">
                                <i class="fas fa-user-graduate text-xl text-blue-600"></i>
                            </div>
                            <span class="text-3xl font-bold text-blue-600 mb-1"><?php echo $totalStudents; ?></span>
                            <span class="text-xs text-blue-600/70 font-medium uppercase tracking-wider">Students Registered</span>
                        </div>
                    </div>

                    <!-- Currently Sit-In Card -->
                    <div class="bg-gradient-to-br from-purple-50 to-white rounded-xl p-4 shadow-lg border border-purple-100/50 transform hover:scale-102 transition-transform duration-300">
                        <div class="flex flex-col items-center text-center">
                            <div class="mb-2 bg-purple-500/10 p-2 rounded-full">
                                <i class="fas fa-chair text-xl text-purple-600"></i>
                            </div>
                            <span class="text-3xl font-bold text-purple-600 mb-1"><?php echo $currentSitIns; ?></span>
                            <span class="text-xs text-purple-600/70 font-medium uppercase tracking-wider">Currently Sit-In</span>
                        </div>
                    </div>

                    <!-- Total Sit-Ins Card -->
                    <div class="bg-gradient-to-br from-green-50 to-white rounded-xl p-4 shadow-lg border border-green-100/50 transform hover:scale-102 transition-transform duration-300">
                        <div class="flex flex-col items-center text-center">
                            <div class="mb-2 bg-green-500/10 p-2 rounded-full">
                                <i class="fas fa-clipboard-list text-xl text-green-600"></i>
                            </div>
                            <span class="text-3xl font-bold text-green-600 mb-1"><?php echo $totalSitIns; ?></span>
                            <span class="text-xs text-green-600/70 font-medium uppercase tracking-wider">Total Sit-Ins</span>
                        </div>
                    </div>
                </div>

                <!-- Chart Container -->
                <div class="flex-1 relative min-h-[350px] bg-white/80 rounded-2xl p-4 shadow-inner">
                    <canvas id="sitInChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Announcements Card -->
        <div class="flex-1 min-w-[400px] bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-2xl overflow-hidden h-[700px] backdrop-blur-sm border border-white/30">
            <div class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white p-4 flex items-center justify-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <i class="fas fa-bullhorn text-2xl mr-4 relative z-10"></i>
                <h2 class="text-xl font-bold tracking-wider uppercase relative z-10">Announcements</h2>
            </div>
            <div class="p-8 h-[calc(100%-5rem)] flex flex-col">
                <div class="mb-6">
                    <form action="" method="post" class="space-y-4">
                        <textarea 
                            name="new_announcement" 
                            placeholder="Type your announcement here..." 
                            required
                            class="w-full p-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 resize-y min-h-[120px] shadow-inner bg-white/80"
                        ></textarea>
                        <button type="submit" 
                            class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white py-3 px-6 rounded-xl hover:shadow-lg transform hover:scale-105 transition-all duration-300 font-medium">
                            <i class="fas fa-paper-plane mr-2"></i>Post Announcement
                        </button>
                    </form>
                </div>

                <div class="flex-1 overflow-y-auto">
                    <h3 class="font-bold text-gray-700 mb-4 text-lg">Posted Announcements</h3>
                    <div class="space-y-4 pr-2">
                        <?php if (empty($announcements)): ?>
                            <p class="text-gray-500 text-center py-4">No announcements available.</p>
                        <?php else: ?>
                            <?php foreach ($announcements as $announcement): ?>
                                <div class="bg-white/80 rounded-xl p-5 shadow-md border border-gray-100 hover:shadow-lg transition-shadow duration-300">
                                    <div class="flex items-center text-sm font-bold text-purple-600 mb-3">
                                        <i class="fas fa-user-shield mr-2"></i>
                                        <?php echo htmlspecialchars($announcement['CREATED_BY']); ?>
                                        <span class="mx-2">•</span>
                                        <i class="far fa-calendar-alt mr-2"></i>
                                        <?php echo date('Y-M-d', strtotime($announcement['CREATED_DATE'])); ?>
                                    </div>
                                    <div class="text-gray-700 pl-4 border-l-4 border-gradient-purple">
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
    
    <!-- Year Level Chart Section -->
    <div class="px-8 pb-8 w-full">
        <!-- Year Level Chart Card -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-2xl overflow-hidden backdrop-blur-sm border border-white/30">
            <div class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white p-4 flex items-center justify-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <i class="fas fa-users text-2xl mr-4 relative z-10"></i>
                <h2 class="text-xl font-bold tracking-wider uppercase relative z-10">Students Year Level</h2>
            </div>
            <div class="p-8">
                <!-- Chart Container -->
                <div class="h-[400px] bg-white/80 rounded-2xl p-4 shadow-inner">
                    <canvas id="yearLevelChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script>
        function toggleNav(x) {
            document.getElementById("mySidenav").classList.toggle("-translate-x-0");
            document.getElementById("mySidenav").classList.toggle("-translate-x-full");
        }

        function closeNav() {
            document.getElementById("mySidenav").classList.remove("-translate-x-0");
            document.getElementById("mySidenav").classList.add("-translate-x-full");
        }
        
        // Initialize the charts
        document.addEventListener('DOMContentLoaded', function() {
            // Sit-In Distribution Pie Chart
            const sitInCtx = document.getElementById('sitInChart').getContext('2d');
            
            const programData = {
                labels: ['C Programming', 'C++ Programming', 'C# Programming', 'Java Programming', 'Python Programming', 'Other'],
                datasets: [{
                    data: <?php echo $programCountsJSON; ?>,
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
            
            new Chart(sitInCtx, {
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
            
            // Year Level Bar Chart
            const yearLevelCtx = document.getElementById('yearLevelChart').getContext('2d');
            
            const yearLevelData = {
                labels: <?php echo $yearLevelLabelsJSON; ?>,
                datasets: [{
                    label: 'College of Computer Studies Students Year Level',
                    data: <?php echo $yearLevelJSON; ?>,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.5)',  // Blue for 1st Year
                        'rgba(255, 99, 132, 0.5)',  // Red for 2nd Year
                        'rgba(75, 192, 192, 0.5)',  // Green for 3rd Year
                        'rgba(153, 102, 255, 0.5)'  // Purple for 4th Year
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1,
                    maxBarThickness: 200 // Adjusted bar thickness
                }]
            };
            
            new Chart(yearLevelCtx, {
                type: 'bar',
                data: yearLevelData,
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false,
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                stepSize: 20
                            },
                            title: {
                                display: true,
                                text: 'Number of Students' // Add y-axis label
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>