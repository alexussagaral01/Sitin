<?php
session_start();
require '../db.php'; // Updated path

$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$firstName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'Guest';

if ($userId) {
    $stmt = $conn->prepare("SELECT UPLOAD_IMAGE FROM users WHERE STUD_NUM = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($userImage);
    $stmt->fetch();
    $stmt->close();
    
    $profileImage = !empty($userImage) ? '../images/' . $userImage : "../images/image.jpg";
} else {
    $profileImage = "../images/image.jpg";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="../logo/ccs.png" type="image/x-icon"> <!-- Updated path -->
    <title>History</title>
    <style>
        body {
        background-image: linear-gradient(104.1deg, rgba(0,61,100,1) 13.6%, rgba(47,127,164,1) 49.4%, rgba(30,198,198,1) 93.3%);
        background-attachment: fixed;
        }

        .logo {
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

        /* New styles for history table */
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
            margin: -20px -20px 20px -20px; /* Adjusted to match your container's padding */
            border-radius: 8px 8px 0 0; 
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-family: 'Roboto', sans-serif;
        }

        .table-controls {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            align-items: center;
        }

        .entries-select {
            display: flex;
            align-items: center;
        }

        .entries-select select {
            margin: 0 5px;
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .search-box {
            display: flex;
            align-items: center;
        }

        .search-box input {
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            margin-left: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #024d80;
            color: white;
            font-weight: normal;
            cursor: pointer;
        }

        th:hover {
            background-color: #036199;
        }

        th i {
            margin-left: 5px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .pagination {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
        }

        .pagination a {
            color: black;
            float: left;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin: 0 4px;
        }

        .pagination a.active {
            background-color: #024d80;
            color: white;
            border: 1px solid #024d80;
        }

        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }

        .no-data {
            text-align: center;
            padding: 15px;
            color: #666;
            font-style: italic;
        }

        .showing-entries {
            margin-top: 10px;
            color: #666;
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
        <a href="dashboard.php"><i class="fas fa-home"></i> HOME</a>
        <a href="profile.php"><i class="fas fa-user"></i> PROFILE</a>
        <a href="edit.php"><i class="fas fa-edit"></i> EDIT</a>
        <a href="history.php"><i class="fas fa-history"></i> HISTORY</a>
        <a href="reservation.php"><i class="fas fa-calendar-alt"></i> RESERVATION</a>

        <div class="logout-section">
            <a href="../login.php"><i class="fas fa-sign-out-alt"></i> LOG OUT</a>
        </div>
    </div>
    
    <div class="content-container">
        <div class="history-header">History Information</div>       
        <div class="table-controls">
            <div class="entries-select">
                <form id="entriesForm" method="GET">
                    <select name="entries" onchange="this.form.submit()">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries per page
                </form>
            </div>
            
            <div class="search-box">
                <form method="GET" action="">
                    <input type="hidden" name="entries" value="10">
                    Search: <input type="text" name="search" value="">
                </form>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID Number <i class="fas fa-sort"></i></th>
                    <th>Name <i class="fas fa-sort"></i></th>
                    <th>Sit Purpose <i class="fas fa-sort"></i></th>
                    <th>Laboratory <i class="fas fa-sort"></i></th>
                    <th>Login <i class="fas fa-sort"></i></th>
                    <th>Logout <i class="fas fa-sort"></i></th>
                    <th>Date <i class="fas fa-sort"></i></th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
        
        <div class="showing-entries">
            Not data available
        </div>
        
        <div class="pagination">
            <a href="?page=1&entries=10&search=">&laquo;</a>
            <a href="?page=1&entries=10&search=">&lt;</a>
            <a href="?page=1&entries=10&search=" class="active">1</a>
            <a href="?page=1&entries=10&search=">&gt;</a>
            <a href="?page=1&entries=10&search=">&raquo;</a>
        </div>
    </div>
    
    <script>
        function toggleNav(x) {
            x.classList.toggle("change");
            document.getElementById("mySidenav").classList.toggle("show");
        }

        function closeNav() {
            document.getElementById("mySidenav").classList.remove("show");
            document.querySelector(".container").classList.remove("change");
        }

        // Auto-submit search form on input change
        document.querySelector('.search-box input').addEventListener('input', function() {
            this.form.submit();
        });
    </script>
</body>
</html>