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

        /* Dashboard content */
        .dashboard-content {
            margin-left: 20px;
            margin-right: 20px;
            margin-top: 50px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        /* Fixed height for both cards to maintain consistent sizing */
        .dashboard-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            width: 48%;
            height: 550px; /* Fixed height for both cards */
            display: flex;
            flex-direction: column;
            overflow: hidden; /* Hide overflow */
        }

        .card-header {
            background-color: #003865; /* Dark blue color similar to your image */
            color: white;
            padding: 12px 15px;
            font-family: 'Roboto', sans-serif;
            font-weight: bold;
            display: flex;
            align-items: center;
            font-size: 24px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .card-header i {
            margin-right: 10px;
        }

        /* Make card body take remaining space with scroll */
        .card-body {
            padding: 15px;
            flex: 1;
            overflow: hidden; /* Hide overflow */
            display: flex;
            flex-direction: column;
        }

        /* Stats section with fixed layout */
        .stats-section {
            margin-bottom: 20px;
        }

        .stats-item {
            margin-bottom: 10px;
            font-family: 'Roboto', sans-serif;
            font-size: 18px;
            display: flex;
            justify-content: space-between;
        }

        .stats-label {
            font-weight: bold;
        }

        /* Chart container with fixed height */
        .chart-container {
            width: 100%;
            height: 300px;
            position: relative;
            margin-top: 20px;
        }

        /* Make announcement form take only the space it needs */
        .announcement-form {
            margin-bottom: 20px;
            width: 100%;
            flex-shrink: 0; /* Prevent shrinking */
        }

        .announcement-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
            min-height: 100px;
            font-family: 'Roboto', sans-serif;
            box-sizing: border-box; /* This is the key fix - it includes padding in width calculation */
            max-width: 100%; /* Ensures it doesn't exceed parent width */
        }

        .announcement-form .btn-submit {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'Roboto', sans-serif;
            margin-top: 10px;
        }

        /* Give the announcement list a fixed height with scroll */
        .announcement-list {
            flex: 1; /* Take remaining space */
            overflow-y: auto; /* Add scroll when needed */
            border: 1px solid #eee;
            border-radius: 4px;
            padding: 10px;
            background-color: #f9f9f9;
        }
        
        .announcement-item {
            border-bottom: 1px solid #ddd;
            padding: 12px 0;
            margin-bottom: 5px;
        }
        
        .announcement-header {
            font-weight: bold;
            color: #003865;
            margin-bottom: 8px;
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
        }
        
        .announcement-text {
            font-family: 'Roboto', sans-serif;
            margin-bottom: 8px;
            line-height: 1.4;
            padding-left: 10px; /* Indentation for the announcement text */
        }

        /* Responsive design for mobile */
        @media (max-width: 768px) {
            .dashboard-card {
                width: 100%;
                height: auto; /* Let it be responsive on mobile */
                max-height: 550px; /* But with a maximum height */
            }
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
        <a href="#"><i class="fas fa-edit"></i> VIEW SIT-IN RECORDS</a>
        <a href="#"><i class="fas fa-list"></i> VIEW LIST OF STUDENT</a>
        <a href="#"><i class="fas fa-chart-line"></i> SIT-IN REPORT</a>
        <a href="#"><i class="fas fa-comments"></i> VIEW FEEDBACKS</a>
        <a href="#"><i class="fas fa-chart-pie"></i> VIEW DAILY ANALYTICS</a>
        <a href="#"><i class="fas fa-calendar-check"></i> RESERVATION/APPROVAL</a>

        <div class="logout-section">
          <a href="../login.php"><i class="fas fa-sign-out-alt"></i> LOG OUT</a>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="dashboard-content">
        <!-- Statistics Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <i class="fas fa-chart-bar"></i> Statistics
            </div>
            <div class="card-body">
                <div class="stats-section">
                    <div class="stats-item">
                        <span class="stats-label">Students Registered:</span>
                        <span><?php echo $totalStudents; ?></span>
                    </div>
                    <div class="stats-item">
                        <span class="stats-label">Currently Sit-In:</span>
                        <span><?php echo $currentSitIns; ?></span>
                    </div>
                    <div class="stats-item">
                        <span class="stats-label">Total Sit-Ins:</span>
                        <span><?php echo $totalSitIns; ?></span>
                    </div>
                </div>
                
                <div class="chart-container">
                    <canvas id="sitInChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Announcements Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <i class="fas fa-bullhorn"></i> Announcements
            </div>
            <div class="card-body">
                <div class="announcement-form">
                    <form action="" method="post">
                        <textarea name="new_announcement" placeholder="Type your announcement here..." required></textarea>
                        <button type="submit" class="btn-submit">Post Announcement</button>
                    </form>
                </div>

                <h3 style="font-family: 'Roboto', sans-serif;">Posted Announcements</h3>
                <div class="announcement-list">
                    <?php if (empty($announcements)): ?>
                        <p>No announcements available.</p>
                    <?php else: ?>
                        <?php foreach (array_reverse($announcements) as $announcement): ?>
                            <div class="announcement-item">
                                <div class="announcement-header">
                                    <?php echo htmlspecialchars($announcement['CREATED_BY']); ?> | <?php echo date('Y-M-d', strtotime($announcement['CREATED_DATE'])); ?>
                                </div>
                                <div class="announcement-text">
                                    <?php echo htmlspecialchars($announcement['CONTENT']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                font: {
                                    family: 'Roboto'
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Sit-In Distribution by Program',
                            font: {
                                family: 'Roboto',
                                size: 16
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>