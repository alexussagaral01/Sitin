<?php
session_start();
require '../db.php';

// Check if admin is not logged in, redirect to login page
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../login.php");
    exit;
}

// Initialize search query
$search = isset($_POST['search']) ? mysqli_real_escape_string($conn, $_POST['search']) : '';

// Modify the base query to include search
$query = "SELECT IDNO, FULL_NAME, PURPOSE, LABORATORY, TIME_IN, TIME_OUT, DATE FROM curr_sitin";
if (!empty($search)) {
    $query .= " WHERE IDNO LIKE '%$search%' 
                OR FULL_NAME LIKE '%$search%' 
                OR PURPOSE LIKE '%$search%' 
                OR LABORATORY LIKE '%$search%'";
}
$query .= " ORDER BY DATE DESC";

$result = mysqli_query($conn, $query);
$total_records = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sitin Reports</title>
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

    <!-- Main Content -->
    <div class="container mx-auto px-4 mt-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
            <div class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white p-4 flex items-center justify-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <i class="fas fa-chart-line text-2xl mr-4 relative z-10"></i>
                <h2 class="text-xl font-bold tracking-wider uppercase relative z-10">Generate Reports</h2>
            </div>

            <div class="p-6">
                <!-- Date and Search Controls -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex space-x-3">
                        <input type="date" class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400">
                        <button class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white px-4 py-2 rounded hover:opacity-90 transition-opacity duration-200">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                        <button class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors duration-200">
                            <i class="fas fa-undo mr-2"></i>Reset
                        </button>
                    </div>
                </div>

                <!-- Export Options -->
                <div class="flex justify-between items-center mb-4">
                    <div class="flex space-x-2">
                        <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition-colors duration-200">CSV</button>
                        <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition-colors duration-200">Excel</button>
                        <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition-colors duration-200">PDF</button>
                        <button class="bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600 transition-colors duration-200">Print</button>
                    </div>
                </div>

                <!-- Entries per page and Search Bar -->
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center">
                        <select class="border rounded px-2 py-1 mr-2 focus:outline-none focus:ring-2 focus:ring-purple-400">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span>entries per page</span>
                    </div>
                    <div class="flex items-center">
                        <form method="POST" class="flex items-center">
                            <span class="mr-2">Search:</span>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                   class="border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-purple-400">
                            <button type="submit" class="ml-2 bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white px-4 py-1 rounded hover:opacity-90 transition-opacity duration-200">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>

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
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <?php
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr class='border-b hover:bg-gray-50'>";
                                    echo "<td class='px-6 py-4'>" . $row['IDNO'] . "</td>";
                                    echo "<td class='px-6 py-4'>" . $row['FULL_NAME'] . "</td>";
                                    echo "<td class='px-6 py-4'>" . $row['PURPOSE'] . "</td>";
                                    echo "<td class='px-6 py-4'>" . $row['LABORATORY'] . "</td>";
                                    echo "<td class='px-6 py-4'>" . $row['TIME_IN'] . "</td>";
                                    echo "<td class='px-6 py-4'>" . ($row['TIME_OUT'] ? $row['TIME_OUT'] : 'Active') . "</td>";
                                    echo "<td class='px-6 py-4'>" . $row['DATE'] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr class='border-b hover:bg-gray-50'>";
                                echo "<td colspan='7' class='px-6 py-4 text-center text-gray-500 italic'>No data available</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between items-center mt-4">
                    <div class="text-gray-600">
                        <?php 
                        $start = 1;
                        $end = $total_records;
                        echo "Showing $start to $end of $total_records entries";
                        ?>
                    </div>
                    <div class="flex space-x-1">
                        <button class="px-3 py-1 border rounded hover:bg-gray-100">&laquo;</button>
                        <button class="px-3 py-1 border rounded hover:bg-gray-100">&lt;</button>
                        <button class="px-3 py-1 border rounded bg-blue-500 text-white">1</button>
                        <button class="px-3 py-1 border rounded hover:bg-gray-100">&gt;</button>
                        <button class="px-3 py-1 border rounded hover:bg-gray-100">&raquo;</button>
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