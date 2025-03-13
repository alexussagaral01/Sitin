<?php
session_start();
require '../db.php';

$firstName = isset($_SESSION['admin']) && $_SESSION['admin'] === true ? 'Admin' : (isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'Guest');
$profileImage = isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : '../images/image.jpg';
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
        /* Remove all existing burger menu and sidenav styles */
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
            <!-- Rest of navigation items -->
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

    <!-- Content Container -->
    <div class="w-[90%] mx-auto my-8 bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white p-4 text-center text-2xl font-bold uppercase tracking-wider">
            Current Sit-in Records
        </div>

        <!-- Charts Container -->
        <div class="flex flex-wrap justify-between gap-5 p-5">
            <!-- Chart Card 1 -->
            <div class="flex-1 min-w-[400px] bg-white rounded-lg shadow-md p-4">
                <div class="text-lg font-bold text-[#003d64] text-center mb-4">
                    Programming Languages Distribution
                </div>
                <div class="h-[280px] relative">
                    <canvas id="pieChart1"></canvas>
                </div>
            </div>
            
            <!-- Chart Card 2 -->
            <div class="flex-1 min-w-[400px] bg-white rounded-lg shadow-md p-4">
                <div class="text-lg font-bold text-[#003d64] text-center mb-4">
                    Room Distribution
                </div>
                <div class="h-[280px] relative">
                    <canvas id="pieChart2"></canvas>
                </div>
            </div>
        </div>

        <!-- Data Table Section -->
        <div class="p-5">
            <!-- Table Controls -->
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center space-x-2">
                    <span>Show</span>
                    <select class="border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span>entries</span>
                </div>
                
                <div class="flex items-center space-x-2">
                    <label>Search:</label>
                    <input type="text" 
                           placeholder="Search..." 
                           class="border rounded px-3 py-1 w-48 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <!-- Table -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Sit ID Number</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">ID Number</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Name</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Purpose</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Sit Lab</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Session</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500 italic">
                                No data available
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="flex justify-between items-center mt-4">
                <div class="text-sm text-gray-600">
                    Showing 1 to 1 of 1 entry
                </div>
                <div class="flex space-x-1">
                    <a href="#" class="px-3 py-1 border rounded hover:bg-gray-100 transition-colors">«</a>
                    <a href="#" class="px-3 py-1 border rounded bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white">1</a>
                    <a href="#" class="px-3 py-1 border rounded hover:bg-gray-100 transition-colors">»</a>
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
        
        // Data for the pie charts
        const data1 = {
            labels: ['C#', 'C', 'Java', 'ASP.Net', 'Php'],
            datasets: [{
                data: [0, 0, 0, 0, 100],
                backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56', '#FF9F40', '#4BC0C0']
            }]
        };

        const data2 = {
            labels: ['524', '526', '528', '530', '542'],
            datasets: [{
                data: [100, 0, 0, 0, 0],
                backgroundColor: ['#FF6384', '#FFCE56', '#FF9F40', '#36A2EB', '#9966FF']
            }]
        };

        // Configurations for the pie charts
        const config1 = {
            type: 'pie',
            data: data1,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            font: {
                                size: 12
                            }
                        }
                    },
                    title: {
                        display: false
                    }
                },
                layout: {
                    padding: 15
                }
            }
        };

        const config2 = {
            type: 'pie',
            data: data2,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            font: {
                                size: 12
                            }
                        }
                    },
                    title: {
                        display: false
                    }
                },
                layout: {
                    padding: 15
                }
            }
        };

        // Render the pie charts after the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            const pieChart1 = new Chart(
                document.getElementById('pieChart1'),
                config1
            );

            const pieChart2 = new Chart(
                document.getElementById('pieChart2'),
                config2
            );
        });
    </script>
</body>
</html>