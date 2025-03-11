<?php
session_start();
require '../db.php';

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
    <link rel="icon" href="../logo/ccs.png" type="image/x-icon">
    <title>Reservation</title>
    <style>
        body {
        background-image: linear-gradient(104.1deg, rgba(0,61,100,1) 13.6%, rgba(47,127,164,1) 49.4%, rgba(30,198,198,1) 93.3%);
        background-attachment: fixed;
        font-family: 'Roboto', sans-serif;
        color: black; /* Set font color to black */
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

        /* Reservation Form Styles */
        .reservation-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px 5px rgba(0, 0, 0, 0.4);
        }

        .reservation-title {
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
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            font-family: 'Roboto', sans-serif; /* Set font family to Roboto */
            color: black; /* Set font color to black */
        }

        .form-group label {
            width: 150px;
            font-weight: 500;
            color: #444;
        }

        .form-group input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f8f8f8;
            width: 100%;
        }

        .button-group {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .btn-reserve {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            min-width: 100px;
        }

        .btn-reserve:hover {
            background-color: #0056b3;
        }

        /* Special styling for date and time inputs */
        input[type="date"], input[type="time"] {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f8f8f8;
            padding: 10px;
            width: 100%;
        }

        /* Hide default date and time icons */
        input::-webkit-calendar-picker-indicator {
            opacity: 1;
            display: block;
            float: right;
            cursor: pointer;
        }

        /* Custom placeholder for date and time */
        .time-placeholder::before {
            content: "--:-- --";
            color: #777;
            position: absolute;
            pointer-events: none;
        }

        .date-placeholder::before {
            content: "dd/mm/yyyy";
            color: #777;
            position: absolute;
            pointer-events: none;
        }

        /* Success message */
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .readonly-input {
            background-color: #e9ecef;
            cursor: not-allowed;
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

    <div class="reservation-container">
        <div class="reservation-title">Reservation</div>
        <form action="reservation.php" method="post">
            <div class="form-group">
                <label for="id_number">ID Number:</label>
                <input type="text" id="id_number" name="id_number" value="22680649" class="form-control readonly-input" readonly>
            </div>
            
            <div class="form-group">
                <label for="student_name">Student Name:</label>
                <input type="text" id="student_name" name="student_name" value="Alexus Jamilo Sagaral" class="form-control readonly-input" readonly>
            </div>
            
            <div class="form-group">
                <label for="purpose">Purpose:</label>
                <input type="text" id="purpose" name="purpose" placeholder="C Programming" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="lab">Lab:</label>
                <input type="text" id="lab" name="lab" placeholder="524" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="time_in">Time In:</label>
                <input type="time" id="time_in" name="time_in" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="remaining_session">Remaining Session:</label>
                <input type="text" id="remaining_session" name="remaining_session" value="30" class="form-control readonly-input" readonly>
            </div>
            
            <div class="button-group">
                <button type="button" class="btn-reserve" style="margin: 0 auto;">Reserve</button>
            </div>
        </form>
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

        // Add date and time pickers with custom formatting
        document.addEventListener('DOMContentLoaded', function() {
            // Time picker setup
            const timeInput = document.getElementById('time_in');
            timeInput.addEventListener('focus', function() {
                // Convert text input to time input on focus
                this.type = 'time';
            });
            
            timeInput.addEventListener('blur', function() {
                // If no value is selected, convert back to text
                if (!this.value) {
                    this.type = 'text';
                }
            });
            
            // Date picker setup
            const dateInput = document.getElementById('date');
            dateInput.addEventListener('focus', function() {
                // Convert text input to date input on focus
                this.type = 'date';
            });
            
            dateInput.addEventListener('blur', function() {
                // If no value is selected, convert back to text
                if (!this.value) {
                    this.type = 'text';
                }
            });
        });
    </script>
</body>
</html>