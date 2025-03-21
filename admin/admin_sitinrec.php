<?php
session_start();
require '../db.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../login.php");
    exit;
}

// Update query to match database column names
$query = "SELECT IDNO, FULL_NAME, PURPOSE, LABORATORY, TIME_IN, TIME_OUT, DATE, STATUS FROM curr_sitin ORDER BY DATE DESC";
$result = mysqli_query($conn, $query);
$total_records = mysqli_num_rows($result);

// Add this after the existing query
$programCounts = [
    'C Programming' => 0,
    'C++ Programming' => 0,
    'C# Programming' => 0,
    'Java Programming' => 0,
    'Python Programming' => 0,
    'Other' => 0
];

$labCounts = [
    'Lab 524' => 0,
    'Lab 526' => 0,
    'Lab 528' => 0,
    'Lab 530' => 0,
    'Lab 542' => 0,
    'Lab 544' => 0
];

// Get PURPOSE statistics
$result1 = $conn->query("SELECT PURPOSE, COUNT(*) as count FROM curr_sitin GROUP BY PURPOSE");
while ($row = $result1->fetch_assoc()) {
    $purpose = $row['PURPOSE'];
    if (array_key_exists($purpose, $programCounts)) {
        $programCounts[$purpose] = $row['count'];
    } else {
        $programCounts['Other'] += $row['count'];
    }
}

// Get LABORATORY statistics
$result2 = $conn->query("SELECT LABORATORY, COUNT(*) as count FROM curr_sitin GROUP BY LABORATORY");
while ($row = $result2->fetch_assoc()) {
    $lab = $row['LABORATORY'];
    if (array_key_exists($lab, $labCounts)) {
        $labCounts[$lab] = $row['count'];
    }
}

// Convert data to ECharts format
$programData = [];
foreach ($programCounts as $name => $value) {
    $programData[] = ['value' => $value, 'name' => $name];
}

$labData = [];
foreach ($labCounts as $name => $value) {
    $labData[] = ['value' => $value, 'name' => $name];
}

// Convert to JSON for JavaScript
$programDataJSON = json_encode($programData);
$labDataJSON = json_encode($labData);

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
    <title>Admin Sit-in Records</title>
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
                <a href="admin_report.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-chart-line w-6 text-base"></i>
                    <span class="text-sm font-medium">SIT-IN REPORT</span>
                </a>
            </div>
            <div class="overflow-hidden">
                <a href="admin_feedback.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
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

    <!-- Content Container -->
    <div class="w-[90%] mx-auto my-8 bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white p-4 flex items-center justify-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <i class="fas fa-book text-2xl mr-4 relative z-10"></i>
            <h2 class="text-xl font-bold tracking-wider uppercase relative z-10">Current Sit-in Records</h2>
        </div>

        <!-- Charts Container -->
        <div class="flex flex-wrap justify-between gap-5 p-5">
            <!-- Chart Card 1 -->
            <div class="flex-1 min-w-[400px] bg-white rounded-lg shadow-md p-4">
                <div class="text-lg font-bold text-[#003d64] text-center mb-4">
                    Programming Languages Distribution
                </div>
                <div id="programmingChart" class="h-[350px]"></div>
            </div>
            
            <!-- Chart Card 2 -->
            <div class="flex-1 min-w-[400px] bg-white rounded-lg shadow-md p-4">
                <div class="text-lg font-bold text-[#003d64] text-center mb-4">
                    Room Distribution
                </div>
                <div id="labChart" class="h-[350px]"></div>
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
                    <div class="flex items-center">
                        <input type="text" 
                            id="searchInput"
                            name="search"
                            placeholder="Search..." 
                            class="border rounded px-3 py-1 w-48 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="button" onclick="searchTable()" class="ml-2 bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white px-4 py-1 rounded hover:opacity-90">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white">
                        <tr>
                            <th class="px-6 py-3 text-left">ID Number</th>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Purpose</th>
                            <th class="px-6 py-3 text-left">Laboratory</th>
                            <th class="px-6 py-3 text-left">Time In</th>
                            <th class="px-6 py-3 text-left">Time Out</th>
                            <th class="px-6 py-3 text-left">Date</th>
                            <th class="px-6 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="bg-white">
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr class='hover:bg-gray-50'>";
                                echo "<td class='px-6 py-4'>" . htmlspecialchars($row['IDNO']) . "</td>";
                                echo "<td class='px-6 py-4'>" . htmlspecialchars($row['FULL_NAME']) . "</td>";
                                echo "<td class='px-6 py-4'>" . htmlspecialchars($row['PURPOSE']) . "</td>";
                                echo "<td class='px-6 py-4'>" . htmlspecialchars($row['LABORATORY']) . "</td>";
                                echo "<td class='px-6 py-4'>" . date('h:i A', strtotime($row['TIME_IN'])) . "</td>";
                                echo "<td class='px-6 py-4'>" . ($row['TIME_OUT'] ? date('h:i A', strtotime($row['TIME_OUT'])) : 'N/A') . "</td>";
                                echo "<td class='px-6 py-4'>" . htmlspecialchars($row['DATE']) . "</td>";
                                echo "<td class='px-6 py-4'>" . htmlspecialchars($row['STATUS']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='px-6 py-4 text-center text-gray-500 italic'>No data available</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="flex justify-between items-center mt-4">
                <div class="text-sm text-gray-600">
                    <?php 
                    $start = 1;
                    $end = $total_records;
                    echo "Showing $start to $end of $total_records entries";
                    ?>
                </div>
                <div class="flex space-x-1">
                    <a href="#" class="px-3 py-1 border rounded hover:bg-gray-100 transition-colors">«</a>
                    <a href="#" class="px-3 py-1 border rounded bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white">1</a>
                    <a href="#" class="px-3 py-1 border rounded hover:bg-gray-100 transition-colors">»</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ECharts Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
    <script>
        // SearchTable functions
        function handleKeyPress(event) {
            if (event.key === "Enter") {
                searchTable();
            }
        }

        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const tbody = document.getElementById('tableBody');
            const rows = tbody.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                let found = false;
                const cells = rows[i].getElementsByTagName('td');
                
                for (let j = 0; j < cells.length; j++) {
                    const text = cells[j].textContent || cells[j].innerText;
                    if (text.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        }

        // Add event listener for Enter key
        document.getElementById('searchInput').addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault(); // Prevent form submission
                searchTable();
            }
        });

        // Navigation functions
        function toggleNav(x) {
            document.getElementById("mySidenav").classList.toggle("-translate-x-0");
            document.getElementById("mySidenav").classList.toggle("-translate-x-full");
        }

        function closeNav() {
            document.getElementById("mySidenav").classList.remove("-translate-x-0");
            document.getElementById("mySidenav").classList.add("-translate-x-full");
        }
        
        // ECharts Implementation
        document.addEventListener('DOMContentLoaded', function() {
            // Programming Languages Chart
            const programmingChart = echarts.init(document.getElementById('programmingChart'));
            const programmingOption = {
                legend: {
                    top: 'bottom',
                    textStyle: {
                        fontSize: 12
                    }
                },
                toolbox: {
                    show: true,
                    feature: {
                        mark: { show: true },
                        dataView: { show: true, readOnly: false },
                        restore: { show: true },
                        saveAsImage: { show: true }
                    }
                },
                series: [
                    {
                        name: 'Programming Languages',
                        type: 'pie',
                        radius: [30, 140],
                        center: ['50%', '50%'],
                        roseType: 'area',
                        itemStyle: {
                            borderRadius: 8
                        },
                        label: {
                            show: false
                        },
                        emphasis: {
                            label: {
                                show: true
                            }
                        },
                        data: <?php echo $programDataJSON; ?>
                    }
                ],
                color: ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40']
            };
            programmingChart.setOption(programmingOption);
            
            // Laboratory Distribution Chart
            const labChart = echarts.init(document.getElementById('labChart'));
            const labOption = {
                legend: {
                    top: 'bottom',
                    textStyle: {
                        fontSize: 12
                    }
                },
                toolbox: {
                    show: true,
                    feature: {
                        mark: { show: true },
                        dataView: { show: true, readOnly: false },
                        restore: { show: true },
                        saveAsImage: { show: true }
                    }
                },
                series: [
                    {
                        name: 'Laboratory Distribution',
                        type: 'pie',
                        radius: [30, 140],
                        center: ['50%', '50%'],
                        roseType: 'area',
                        itemStyle: {
                            borderRadius: 8
                        },
                        label: {
                            show: false
                        },
                        emphasis: {
                            label: {
                                show: true
                            }
                        },
                        data: <?php echo $labDataJSON; ?>
                    }
                ],
                color: ['#FF6384', '#FFCE56', '#FF9F40', '#36A2EB', '#9966FF', '#4BC0C0']
            };
            labChart.setOption(labOption);

            // Handle window resize
            window.addEventListener('resize', function() {
                programmingChart.resize();
                labChart.resize();
            });
        });
    </script>
</body>
</html>