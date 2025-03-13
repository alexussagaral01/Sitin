<?php
session_start();
require '../db.php';

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
    <title>Admin Student List</title>
    <style>
        /* Keep only necessary custom styles that aren't covered by Tailwind */
        body {
            background-image: linear-gradient(104.1deg, rgba(0,61,100,1) 13.6%, rgba(47,127,164,1) 49.4%, rgba(30,198,198,1) 93.3%);
            background-attachment: fixed;
        }

        /* Any remaining custom styles for content area */
        .content-container {
            width: 90%;
            margin: 30px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .history-header {
            background-color: #003d64;
            color: white;
            padding: 15px;
            margin: -20px -20px 20px -20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-family: 'Roboto', sans-serif;
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
            <img src="../images/image.jpg" alt="Logo" class="w-24 h-24 rounded-full border-2 border-white object-cover mb-2">
            <p class="text-white font-bold text-lg mb-3">Admin</p>
        </div>

        <nav class="flex flex-col space-y-0.5 px-2">
            <!-- Navigation items -->
            <a href="admin_dashboard.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                <i class="fas fa-home w-6 text-base"></i>
                <span class="text-sm font-medium">HOME</span>
            </a>
            <a href="admin_search.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                <i class="fas fa-search w-6 text-base"></i>
                <span class="text-sm font-medium">SEARCH</span>
            </a>
            <a href="admin_sitin.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                <i class="fas fa-user-check w-6 text-base"></i>
                <span class="text-sm font-medium">SIT-IN</span>
            </a>
            <a href="admin_sitinrec.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                <i class="fas fa-book w-6 text-base"></i>
                <span class="text-sm font-medium">VIEW SIT-IN RECORDS</span>
            </a>
            <a href="admin_studlist.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                <i class="fas fa-list w-6 text-base"></i>
                <span class="text-sm font-medium">VIEW LIST OF STUDENT</span>
            </a>
            <a href="#" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                <i class="fas fa-chart-line w-6 text-base"></i>
                <span class="text-sm font-medium">SIT-IN REPORT</span>
            </a>
            <a href="#" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                <i class="fas fa-comments w-6 text-base"></i>
                <span class="text-sm font-medium">VIEW FEEDBACKS</span>
            </a>
            <a href="#" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                <i class="fas fa-chart-pie w-6 text-base"></i>
                <span class="text-sm font-medium">VIEW DAILY ANALYTICS</span>
            </a>
            <a href="#" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                <i class="fas fa-calendar-check w-6 text-base"></i>
                <span class="text-sm font-medium">RESERVATION/APPROVAL</span>
            </a>
        </nav>

        <div class="mt-3 px-2 pb-2">
            <a href="../login.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                <i class="fas fa-sign-out-alt w-6 text-base"></i>
                <span class="text-sm font-medium">LOG OUT</span>
            </a>
        </div>
    </div>

    <div class="content-container w-11/12 mx-auto my-8 bg-white p-6 rounded-lg shadow-lg overflow-hidden border border-gray-200">
        <div class="history-header bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white py-4 px-6 -mx-6 -mt-6 mb-6 rounded-t-lg text-center text-2xl font-bold uppercase tracking-wider font-roboto">
            <h2 class="text-xl font-bold text-center">STUDENT INFORMATION</h2>
        </div>  
        
        <div class="p-6">
            <!-- Buttons moved above the search bar and entry selector -->
            <div class="flex mb-6">
                <button class="bg-blue-500 text-white px-4 py-2 rounded mr-2">Add Students</button>
                <button class="bg-red-500 text-white px-4 py-2 rounded">Reset All Session</button>
            </div>
            
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center">
                    <select class="border rounded px-2 py-1 mr-2">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span>entries per page</span>
                </div>
                <div class="flex items-center">
                    <span class="mr-2">Search:</span>
                    <input type="text" class="border rounded px-2 py-1">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white">
                        <tr>
                            <th class="px-6 py-3 text-left">ID Number</th>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Year Level</th>
                            <th class="px-6 py-3 text-left">Course</th>
                            <th class="px-6 py-3 text-left">Remaining Session</th>
                            <th class="px-6 py-3 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500 italic">No data available</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between items-center mt-4">
                <div class="text-gray-600">Showing 0 to 0 of 0 entries</div>
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