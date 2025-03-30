<?php
session_start();
require '../db.php';

// Check if admin is not logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../login.php");
    exit;
}

// Initialize pagination variables
$entries_per_page = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $entries_per_page;

// Initialize search query
$search = isset($_POST['search']) ? mysqli_real_escape_string($conn, $_POST['search']) : '';

// Get total records count
$count_query = "SELECT COUNT(*) as total FROM curr_sitin";
if (!empty($search)) {
    $count_query .= " WHERE IDNO LIKE '%$search%' 
                      OR FULL_NAME LIKE '%$search%' 
                      OR PURPOSE LIKE '%$search%' 
                      OR LABORATORY LIKE '%$search%'";
}
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $entries_per_page);

// Modify the base query to include pagination
$query = "SELECT IDNO, FULL_NAME, PURPOSE, LABORATORY, TIME_IN, TIME_OUT, DATE FROM curr_sitin";
if (!empty($search)) {
    $query .= " WHERE IDNO LIKE '%$search%' 
                OR FULL_NAME LIKE '%$search%' 
                OR PURPOSE LIKE '%$search%' 
                OR LABORATORY LIKE '%$search%'";
}
$query .= " ORDER BY DATE DESC LIMIT ? OFFSET ?";

// Use prepared statement for the main query
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $entries_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sitin Reports</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="../logo/ccs.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Add DataTables CSS and JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
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
                <img src="../images/image.jpg" alt="Logo" class="w-20 h-20 rounded-full border-4 border-white/30 object-cover shadow-lg">
                <div class="absolute bottom-0 right-0 bg-green-500 w-3 h-3 rounded-full border-2 border-white"></div>
            </div>
            <p class="text-white font-semibold text-lg mt-2 mb-0">Admin</p>
            <p class="text-purple-200 text-xs mb-3">Administrator</p>
        </div>

        <div class="px-2 py-2">
            <nav class="flex flex-col space-y-1">
                <a href="admin_dashboard.php" class="group px-3 py-2 text-white/90 hover:bg-white/20 rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-home w-5 mr-2 text-center"></i>
                    <span class="font-medium">HOME</span>
                </a>
                <a href="admin_search.php" class="group px-3 py-2 text-white/90 hover:bg-white/20 rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-search w-5 mr-2 text-center"></i>
                    <span class="font-medium">SEARCH</span>
                </a>
                <a href="admin_sitin.php" class="group px-3 py-2 text-white/90 hover:bg-white/20 rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-user-check w-5 mr-2 text-center"></i>
                    <span class="font-medium">SIT-IN</span>
                </a>
                <a href="admin_sitinrec.php" class="group px-3 py-2 text-white/90 hover:bg-white/20 rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-book w-5 mr-2 text-center"></i>
                    <span class="font-medium">VIEW SIT-IN RECORDS</span>
                </a>
                <a href="admin_studlist.php" class="group px-3 py-2 text-white/90 hover:bg-white/20 rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-list w-5 mr-2 text-center"></i>
                    <span class="font-medium">VIEW LIST OF STUDENT</span>
                </a>
                <a href="admin_report.php" class="group px-3 py-2 text-white/90 bg-white/20 rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-chart-line w-5 mr-2 text-center"></i>
                    <span class="font-medium">SIT-IN REPORT</span>
                </a>
                <a href="admin_feedback.php" class="group px-3 py-2 text-white/90 hover:bg-white/20 rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-comments w-5 mr-2 text-center"></i>
                    <span class="font-medium">VIEW FEEDBACKS</span>
                </a>
                <a href="#" class="group px-3 py-2 text-white/90 hover:bg-white/20 rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-chart-pie w-5 mr-2 text-center"></i>
                    <span class="font-medium">VIEW DAILY ANALYTICS</span>
                </a>
                <a href="#" class="group px-3 py-2 text-white/90 hover:bg-white/20 rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-calendar-check w-5 mr-2 text-center"></i>
                    <span class="font-medium">RESERVATION/APPROVAL</span>
                </a>
                <div class="border-t border-white/10 my-2"></div>
                <a href="../logout.php" class="group px-3 py-2 text-white/90 hover:bg-red-500/20 rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-sign-out-alt w-5 mr-2 text-center"></i>
                    <span class="font-medium group-hover:translate-x-1 transition-transform">LOG OUT</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 mt-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
        <div class="text-white p-4 flex items-center justify-center relative overflow-hidden" style="background: linear-gradient(to bottom right, rgb(49, 46, 129), rgb(107, 33, 168), rgb(190, 24, 93))">
                <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-16 h-16 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                <i class="fas fa-chart-line text-2xl mr-4 relative z-10"></i>
                <h2 class="text-xl font-bold tracking-wider uppercase relative z-10">Generate Reports</h2>
            </div>

            <div class="p-6">
                <!-- Date and Search Controls -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex space-x-3">
                        <input type="date" class="border rounded px-3 py-2">
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
                    <div class="dt-buttons flex space-x-3">
                        <!-- DataTables will automatically insert buttons here -->
                    </div>
                </div>

                <!-- Entries per page and Search Bar -->
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center bg-gray-50 rounded-lg p-2 shadow-sm">
                        <label class="text-gray-600 mr-2 text-sm">Show</label>
                        <select id="entriesPerPage" onchange="changeEntries(this.value)" 
                                class="bg-white border border-gray-200 rounded-md px-3 py-1.5 shadow-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 outline-none">
                            <option value="10" <?php echo $entries_per_page == 10 ? 'selected' : ''; ?>>10</option>
                            <option value="25" <?php echo $entries_per_page == 25 ? 'selected' : ''; ?>>25</option>
                            <option value="50" <?php echo $entries_per_page == 50 ? 'selected' : ''; ?>>50</option>
                            <option value="100" <?php echo $entries_per_page == 100 ? 'selected' : ''; ?>>100</option>
                        </select>
                        <span class="text-gray-600 ml-2 text-sm">entries</span>
                    </div>
                    
                    <div class="flex items-center">
                        <form method="POST" class="relative" onsubmit="return searchTable();">
                            <input type="text" name="search" id="searchInput" placeholder="Search reports..." 
                                value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>"
                                class="w-full md:w-64 pl-10 pr-4 py-2 border border-gray-200 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 outline-none">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <button type="submit" class="hidden">Search</button>
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table id="reportsTable" class="min-w-full">
                    <thead style="background: linear-gradient(to bottom right, rgb(49, 46, 129), rgb(107, 33, 168), rgb(190, 24, 93));" class="text-white">
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
                                    // Convert time to Asia/Manila timezone and 12-hour format
                                    $time_in = date('h:i A', strtotime($row['TIME_IN']));
                                    $time_out = $row['TIME_OUT'] ? date('h:i A', strtotime($row['TIME_OUT'])) : 'Active';
                                    
                                    echo "<tr class='border-b hover:bg-gray-50'>";
                                    echo "<td class='px-6 py-4'>" . $row['IDNO'] . "</td>";
                                    echo "<td class='px-6 py-4'>" . $row['FULL_NAME'] . "</td>";
                                    echo "<td class='px-6 py-4'>" . $row['PURPOSE'] . "</td>";
                                    echo "<td class='px-6 py-4'>" . $row['LABORATORY'] . "</td>";
                                    echo "<td class='px-6 py-4'>" . $time_in . "</td>";
                                    echo "<td class='px-6 py-4'>" . $time_out . "</td>";
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

                <div class="flex flex-col md:flex-row md:justify-between md:items-center mt-6 gap-4">
                    <div class="text-gray-600 text-sm">
                        <?php
                        $start_entry = $total_records > 0 ? $offset + 1 : 0;
                        $end_entry = min($offset + $entries_per_page, $total_records);
                        echo "Showing <span class='font-semibold'>$start_entry</span> to <span class='font-semibold'>$end_entry</span> of <span class='font-semibold'>$total_records</span> entries";
                        ?>
                    </div>
                    <div class="inline-flex rounded-lg shadow-sm">
                        <?php
                        // First page button
                        echo "<button onclick=\"changePage(1)\" " . ($current_page == 1 ? 'disabled' : '') . " 
                              class=\"px-3.5 py-2 text-sm bg-white border border-gray-300 rounded-l-lg hover:bg-gray-50 text-gray-500" . 
                              ($current_page == 1 ? ' opacity-50 cursor-not-allowed' : '') . "\">
                              <i class=\"fas fa-angles-left\"></i>
                          </button>";

                        // Previous page button
                        $prev_page = max(1, $current_page - 1);
                        echo "<button onclick=\"changePage($prev_page)\" " . ($current_page == 1 ? 'disabled' : '') . " 
                              class=\"px-3.5 py-2 text-sm bg-white border-t border-b border-l border-gray-300 hover:bg-gray-50 text-gray-500" . 
                              ($current_page == 1 ? ' opacity-50 cursor-not-allowed' : '') . "\">
                              <i class=\"fas fa-angle-left\"></i>
                          </button>";

                        // Page numbers
                        for($i = 1; $i <= $total_pages; $i++) {
                            if($i == $current_page) {
                                echo "<button class=\"px-3.5 py-2 text-sm bg-indigo-600 text-white border border-indigo-600\">$i</button>";
                            } else {
                                echo "<button onclick=\"changePage($i)\" 
                                      class=\"px-3.5 py-2 text-sm bg-white border border-gray-300 hover:bg-gray-50 text-gray-700\">$i</button>";
                            }
                        }

                        // Next page button
                        $next_page = min($total_pages, $current_page + 1);
                        echo "<button onclick=\"changePage($next_page)\" " . ($current_page == $total_pages ? 'disabled' : '') . "
                              class=\"px-3.5 py-2 text-sm bg-white border-t border-b border-r border-gray-300 hover:bg-gray-50 text-gray-500" . 
                              ($current_page == $total_pages ? ' opacity-50 cursor-not-allowed' : '') . "\">
                              <i class=\"fas fa-angle-right\"></i>
                          </button>";

                        // Last page button
                        echo "<button onclick=\"changePage($total_pages)\" " . ($current_page == $total_pages ? 'disabled' : '') . "
                              class=\"px-3.5 py-2 text-sm bg-white border border-gray-300 rounded-r-lg hover:bg-gray-50 text-gray-500" . 
                              ($current_page == $total_pages ? ' opacity-50 cursor-not-allowed' : '') . "\">
                              <i class=\"fas fa-angles-right\"></i>
                          </button>";
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
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

        function changeEntries(entries) {
            window.location.href = `admin_report.php?entries=${entries}&page=1`;
        }

        function changePage(page) {
            const entries = document.getElementById('entriesPerPage').value;
            window.location.href = `admin_report.php?entries=${entries}&page=${page}`;
        }

        function searchTable() {
            const searchValue = document.getElementById('searchInput').value;
            if (searchValue.length > 0) {
                document.forms[0].submit();
            }
            return false;
        }

        $(document).ready(function() {
            $('#reportsTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'csv',
                        className: 'relative inline-flex items-center px-6 py-2.5 border-2 border-blue-600 font-medium text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl transition-all duration-300 group',
                        text: `<span class="absolute w-32 h-32 -top-8 -left-2 bg-blue-600 scale-0 rounded-full group-hover:scale-100 transition-all duration-300 z-0 opacity-30"></span>
                               <span class="relative z-10 flex items-center">
                                   <i class="fas fa-file-csv mr-2 transform group-hover:scale-110 transition-transform"></i>
                                   Export CSV
                               </span>`
                    },
                    {
                        extend: 'excel',
                        className: 'relative inline-flex items-center px-6 py-2.5 border-2 border-emerald-600 font-medium text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-xl transition-all duration-300 group overflow-hidden',
                        text: `<span class="absolute inset-0 bg-emerald-600 w-0 group-hover:w-full transition-all duration-300 z-0"></span>
                               <span class="relative z-10 flex items-center">
                                   <i class="fas fa-file-excel mr-2 group-hover:animate-bounce"></i>
                                   Export Excel
                               </span>`
                    },
                    {
                        extend: 'pdf',
                        className: 'relative inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-red-500/50 hover:-translate-y-0.5',
                        text: `<span class="flex items-center">
                                   <i class="fas fa-file-pdf mr-2 animate-pulse"></i>
                                   Export PDF
                               </span>`
                    },
                    {
                        extend: 'print',
                        className: 'relative inline-flex items-center px-6 py-2.5 bg-gray-800 text-white rounded-xl transition-all duration-300 group hover:ring-2 hover:ring-offset-2 hover:ring-gray-600',
                        text: `<span class="absolute inset-0 h-full w-full bg-white/10 block scale-0 group-hover:scale-100 transition-transform duration-300 rounded-xl"></span>
                               <span class="relative z-10 flex items-center">
                                   <i class="fas fa-print mr-2 group-hover:rotate-12 transition-transform"></i>
                                   Print Report
                               </span>`
                    }
                ],
                paging: false,
                searching: false,
                info: false,
                // Add these options to remove DataTables styling
                ordering: false,
                bSort: false,
                bFilter: false,
                bLengthChange: false,
                bPaginate: false,
                bInfo: false,
                // Disable DataTables CSS
                autoWidth: false,
                initComplete: function() {
                    // Remove DataTables classes from table
                    $(this).removeClass('dataTable');
                    $('.dataTables_wrapper').addClass('bg-transparent border-0');
                }
            });

            // Move the buttons to our custom container and add spacing
            $('.dt-buttons').addClass('flex flex-wrap gap-4');
            $('.dt-button').addClass('!hover:no-underline');
        });
    </script>
</body>
</html>