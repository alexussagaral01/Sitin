<?php
session_start();
require '../db.php'; // Add database connection

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
    <title>Admin Dashboard</title>
    <style>
        body {
            background-image: linear-gradient(104.1deg, rgba(0,61,100,1) 13.6%, rgba(47,127,164,1) 49.4%, rgba(30,198,198,1) 93.3%);
            background-attachment: fixed;
        }

        .logo, .student-info img {
            width: 150px; 
            height: 150px; 
            display: block;
            margin-left: auto;
            margin-right: auto;
            border: 1px solid black;
            border-radius: 50%; 
            object-fit: cover; 
        }

        .sidenav {
            height: 100%;
            width: 250px;
            position: fixed;
            z-index: 1;
            top: 0;
            left: -250px;
            background-color: white; 
            overflow-x: hidden;
            padding-top: 20px;
            font-family: 'Roboto', sans-serif;
            font-size: 18px;
            transition: 0.3s;
        }

        .sidenav.show {
            left: 0;
        }

        .sidenav a {
            padding: 8px 8px 8px 16px;
            text-decoration: none;
            font-size: 15px;
            color: black; 
            display: flex;
            align-items: center;
            position: relative;
            padding-left: 16px; 
        }

        .sidenav a i {
            font-size: 15px; 
            margin-right: 10px; 
        }

        .sidenav a:hover {
            background-color: black; 
            color: white; 
            transform: scale(1.05); 
            transition: transform 0.3s, background-color 0.3s, color 0.3s; 
        }

        .sidenav a:hover i {
            color: white; 
        }

        .sidenav a::before {
            content: '';
            position: absolute;
            left: -10px; 
            top: 0;
            bottom: 0;
            width: 5px; 
            background-color: transparent;
            transition: background-color 0.3s;
        }

        .sidenav a:hover::before {
            background-color: black; 
        }

        .user-name {
            color: #000; 
            text-align: center;
            font-family: 'Roboto', sans-serif;
            font-size: 22px;
            font-weight: bold; 
        }

        .container {
            display: inline-block;
            cursor: pointer;
            position: absolute;
            top: 15px; 
            left: 25px; 
        }

        .bar1, .bar2, .bar3 {
            width: 35px;
            height: 5px;
            background-color: black; 
            margin: 6px 0;
            transition: 0.4s;
        }

        .closebtn {
            position: absolute;
            top: 0; 
            right: 0; 
            padding: 10px 15px;
            font-size: 36px;
            cursor: pointer;
            color: #818181;
        }

        .closebtn:hover {
            color: #000; 
        }

        .header {
            text-align: center;
            background-color: white; 
            color: black; 
            font-family: 'Roboto', sans-serif;
            font-size: 25px; 
            font-weight: bold; 
            padding: 10px; 
        }

        .logout-section {
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
        }

        .logout-section a {
            color:  black;
        }

        .logout-section a:hover {
            background-color: black; 
            color: white; 
        }

        .logout-section a:hover i {
            color: white; 
        }

        /* Updated dashboard content */
        .charts-container {
            display: flex;
            justify-content: space-between;
            margin: 30px 20px;
            gap: 20px;
        }

        .chart-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            width: 48%; /* Set width to approximately half the container */
            height: 350px;
        }

        .chart-title {
            font-family: 'Roboto', sans-serif;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
            color: #003d64;
        }

        .chart-wrapper {
            height: 280px;
            position: relative;
        }

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

        /* Data table styles */
        .table-container {
            margin-top: 30px;
            padding: 0 20px;
        }

        .table-controls {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            align-items: center;
            font-family: 'Roboto', sans-serif;
        }

        .entries-selector {
            display: flex;
            align-items: center;
        }

        .entries-selector select {
            margin: 0 5px;
            padding: 3px 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .search-box {
            display: flex;
            align-items: center;
        }

        .search-box label {
            margin-right: 5px;
        }

        .search-box input {
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            width: 200px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Roboto', sans-serif;
        }

        .data-table th {
            background-color: #f2f2f2;
            color: #000;
            font-weight: 500;
            text-align: left;
            padding: 12px 10px;
            border: 1px solid #ddd;
        }

        .data-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }

        .pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 15px;
            font-family: 'Roboto', sans-serif;
        }

        .pagination-info {
            color: #666;
        }

        .pagination-buttons a {
            display: inline-block;
            padding: 5px 10px;
            margin: 0 2px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
            background-color: #f9f9f9;
        }

        .pagination-buttons a.active {
            background-color: #003d64;
            color: white;
            border-color: #003d64;
        }

        .pagination-buttons a:hover:not(.active) {
            background-color: #ddd;
        }

        .action-button {
            padding: 6px 12px;
            background-color: #024d80;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .action-button:hover {
            background-color: #036199;
        }
    </style>
</head>   
<body>
    <div class="header">
        CCS SIT-IN MONITORING SYSTEM
    </div>
    <div class="container" onclick="toggleNav(this)">
        <div class="bar1"></div>
        <div class="bar2"></div>
        <div class="bar3"></div>
    </div>
    <div class="sidenav" id="mySidenav">
        <span class="closebtn" onclick="closeNav()">&times;</span>
        <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Logo" class="logo">
        <p class="user-name"><?php echo htmlspecialchars($firstName); ?></p>
        <a href="admin_dashboard.php"><i class="fas fa-home"></i> HOME</a>
        <a href="admin_search.php"><i class="fas fa-search"></i> SEARCH</a>
        <a href="admin_sitin.php"><i class="fas fa-user-check"></i> SIT-IN</a>
        <a href="admin_sitinrec.php"><i class="fas fa-book"></i> VIEW SIT-IN RECORDS</a>
        <a href="admin_studlist.php"><i class="fas fa-list"></i> VIEW LIST OF STUDENT</a>
        <a href="#"><i class="fas fa-chart-line"></i> SIT-IN REPORT</a>
        <a href="#"><i class="fas fa-comments"></i> VIEW FEEDBACKS</a>
        <a href="#"><i class="fas fa-chart-pie"></i> VIEW DAILY ANALYTICS</a>
        <a href="#"><i class="fas fa-calendar-check"></i> RESERVATION/APPROVAL</a>

        <div class="logout-section">
          <a href="../login.php"><i class="fas fa-sign-out-alt"></i> LOG OUT</a>
        </div>
    </div>

    <div class="content-container">
        <div class="history-header">Current Sit-in Records</div>  
        
        <!-- Charts container -->
        <div class="charts-container">
            <div class="chart-card">
                <div class="chart-title">Programming Languages Distribution</div>
                <div class="chart-wrapper">
                    <canvas id="pieChart1"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <div class="chart-title">Room Distribution</div>
                <div class="chart-wrapper">
                    <canvas id="pieChart2"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Data Table section -->
        <div class="table-container">
            <div class="table-controls">
                <div class="entries-selector">
                    Show
                    <select>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                </div>
                
                <div class="search-box">
                    <label>Search:</label>
                    <input type="text" placeholder="Search...">
                </div>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Sit ID Number</th>
                        <th>ID Number</th>
                        <th>Name</th>
                        <th>Purpose</th>
                        <th>Sit Lab</th>
                        <th>Session</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="8" class="no-data">No data available</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="pagination">
                <div class="pagination-info">
                    Showing 1 to 1 of 1 entry
                </div>
                <div class="pagination-buttons">
                    <a href="#">«</a>
                    <a href="#" class="active">1</a>
                    <a href="#">»</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script>
        function toggleNav(x) {
            x.classList.toggle("change");
            document.getElementById("mySidenav").classList.toggle("show");
        }

        function closeNav() {
            document.getElementById("mySidenav").classList.remove("show");
            document.querySelector(".container").classList.remove("change");
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