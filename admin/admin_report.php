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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

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
    <script>
        // Convert your logo to base64
        const ucLogo = '<?php 
            $logo_path = "../logo/ccs.png";
            $type = pathinfo($logo_path, PATHINFO_EXTENSION);
            $data = file_get_contents($logo_path);
            echo 'data:image/' . $type . ';base64,' . base64_encode($data);
        ?>';
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

        function getTableStyle() {
            return {
                header: {
                    fillColor: '#312E81',
                    color: '#ffffff',
                    fontSize: 11,
                    bold: true,
                    alignment: 'left',
                    margin: [5, 7, 5, 7]
                },
                cell: {
                    fontSize: 10,
                    alignment: 'left',
                    padding: 7,
                    borderColor: '#E5E7EB'
                },
                alternateRow: '#F9FAFB'
            };
        }

        $(document).ready(function() {
            $('#reportsTable').DataTable({
                dom: '<"flex flex-wrap gap-4 mb-6"B>',
                buttons: [
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv text-xl"></i> CSV',
                        className: 'px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-lg',
                        title: 'CCS Laboratory Report',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel text-xl"></i> Excel',
                        className: 'px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg shadow-lg',
                        title: 'CCS Laboratory Report',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf text-xl"></i> PDF',
                        className: 'px-6 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg shadow-lg',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        title: function() {
                            return '\n\nUNIVERSITY OF CEBU\nCollege of Computer Studies\nLaboratory Sit-in Report\n\n';
                        },
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        },
                        customize: function(doc) {
                            const tableStyle = getTableStyle();
                            
                            // Style header
                            doc.styles.tableHeader = tableStyle.header;
                            
                            // Style cells and add alternating rows
                            doc.content[1].table.body.forEach(function(row, i) {
                                row.forEach(function(cell) {
                                    cell.alignment = 'left';
                                    cell.margin = [5, 3, 5, 3];
                                    if (i !== 0) {
                                        cell.fontSize = 10;
                                        cell.fillColor = i % 2 === 0 ? tableStyle.alternateRow : null;
                                    }
                                });
                            });

                            // Set column widths
                            doc.content[1].table.widths = [
                                '15%', // ID
                                '20%', // Name
                                '15%', // Purpose
                                '15%', // Lab
                                '12%', // Time In
                                '12%', // Time Out
                                '11%'  // Date
                            ];

                            doc.pageMargins = [30, 30, 30, 30];
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print text-xl"></i> Print',
                        className: 'px-6 py-2.5 bg-gray-700 hover:bg-gray-800 text-white rounded-lg shadow-lg',
                        title: '',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        },
                        customize: function(win) {
                            // Add logo and headers with improved styling
                            $(win.document.body).prepend(`
                                <div style="text-align:center;margin:20px 0;">
                                    <img src="${ucLogo}" style="width:100px;height:100px;margin:0 auto 15px;display:block;">
                                    <div style="margin-bottom:20px;">
                                        <h1 style="font-size:24px;font-weight:bold;margin:10px 0;color:#312E81;">UNIVERSITY OF CEBU</h1>
                                        <h2 style="font-size:18px;margin:5px 0;color:#4338CA;">College of Computer Studies</h2>
                                        <h3 style="font-size:16px;margin:5px 0;color:#1F2937;">Laboratory Sit-in Report</h3>
                                    </div>
                                </div>
                            `);

                            // Style the table
                            $(win.document.body).find('table')
                                .addClass('display')
                                .css('font-size', '13px')
                                .css('margin', '0 auto')
                                .css('width', '100%');

                            // Add zebra striping
                            $(win.document.body).find('tr:nth-child(odd)').css('background-color', '#f8f9fa');
                            
                            // Add some padding
                            $(win.document.body).css('padding', '20px');    
                        }
                    }
                ],
                paging: false,
                searching: false,
                info: false,
                processing: true
            });
        });
    </script>
</body>
</html>