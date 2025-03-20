<?php
session_start();
require '../db.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../login.php");
    exit;
}

// Handle student addition via POST request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Idno'])) {
    $idno = $_POST['Idno'];
    $lastname = $_POST['Lastname'];
    $firstname = $_POST['Firstname'];
    $midname = $_POST['Midname'];
    $course = $_POST['Course'];
    $year_level = $_POST['Year_Level'];
    $username = $_POST['Username'];
    $password = password_hash($_POST['Password'], PASSWORD_DEFAULT);

    // Prepare statement to insert new student
    $stmt = $conn->prepare("INSERT INTO users (idno, last_name, first_name, mid_name, course, year_level, user_name, password_hash, session) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 30)");
    $stmt->bind_param("ssssssss", $idno, $lastname, $firstname, $midname, $course, $year_level, $username, $password);

    if ($stmt->execute()) {
        // Set a success flag in session
        $_SESSION['student_added'] = true;
        header("Location: admin_studlist.php");
        exit;
    } else {
        $_SESSION['student_added'] = false;
        header("Location: admin_studlist.php");
        exit;
    }

    $stmt->close();
}

// Initialize search query
$search = isset($_POST['search']) ? mysqli_real_escape_string($conn, $_POST['search']) : '';

// Modify query to include search
$query = "SELECT * FROM users";
if (!empty($search)) {
    $query .= " WHERE IDNO LIKE '%$search%' 
                OR LAST_NAME LIKE '%$search%' 
                OR FIRST_NAME LIKE '%$search%' 
                OR COURSE LIKE '%$search%'
                OR YEAR_LEVEL LIKE '%$search%'";
}
$result = mysqli_query($conn, $query);
$total_records = mysqli_num_rows($result);
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
    <!-- Add SweetAlert CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Admin Student List</title>
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

    <div class="content-container w-11/12 mx-auto my-8 bg-white p-6 rounded-lg shadow-lg overflow-hidden border border-gray-200">
        <div class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white p-4 flex items-center justify-center relative overflow-hidden -mx-6 -mt-6 mb-6">
            <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <i class="fas fa-list text-2xl mr-4 relative z-10"></i>
            <h2 class="text-xl font-bold tracking-wider uppercase relative z-10">Student Information</h2>
        </div>
        
        <div class="p-6">
            <!-- Buttons moved above the search bar and entry selector -->
            <div class="flex mb-6">
                <button id="addStudentBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Add New Student
                </button>

                <div id="addStudentModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
                    <div class="flex justify-between items-center border-b pb-3">
                        <h2 class="text-xl font-semibold">Add Student</h2>
                        <button id="closeModal" class="text-gray-500 hover:text-gray-700">&times;</button>
                    </div>

                    <form id="addStudentForm" method="POST" action="admin_studlist.php" class="mt-4">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Left Column -->
                            <div>
                                <div class="mb-3">
                                    <label for="Idno" class="block text-gray-700 font-medium">ID Number</label>
                                    <input type="text" id="Idno" name="Idno" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                </div>
                                <div class="mb-3">
                                    <label for="Lastname" class="block text-gray-700 font-medium">Last Name</label>
                                    <input type="text" id="Lastname" name="Lastname" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                </div>
                                <div class="mb-3">
                                    <label for="Firstname" class="block text-gray-700 font-medium">First Name</label>
                                    <input type="text" id="Firstname" name="Firstname" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                </div>
                                <div class="mb-3">
                                    <label for="Midname" class="block text-gray-700 font-medium">Middle Name</label>
                                    <input type="text" id="Midname" name="Midname" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div>
                                <div class="mb-3">
                                    <label for="Course" class="block text-gray-700 font-medium">Course</label>
                                    <select id="Course" name="Course" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                        <option value="" disabled selected>Select Course</option>
                                        <option value="BS IN ACCOUNTANCY">BS IN ACCOUNTANCY</option>
                                        <option value="BS IN BUSINESS ADMINISTRATION">BS IN BUSINESS ADMINISTRATION</option>
                                        <option value="BS IN CRIMINOLOGY">BS IN CRIMINOLOGY</option>
                                        <option value="BS IN CUSTOMS ADMINISTRATION">BS IN CUSTOMS ADMINISTRATION</option>
                                        <option value="BS IN INFORMATION TECHNOLOGY">BS IN INFORMATION TECHNOLOGY</option>
                                        <option value="BS IN COMPUTER SCIENCE">BS IN COMPUTER SCIENCE</option>
                                        <option value="BS IN OFFICE ADMINISTRATION">BS IN OFFICE ADMINISTRATION</option>
                                        <option value="BS IN SOCIAL WORK">BS IN SOCIAL WORK</option>
                                        <option value="BACHELOR OF SECONDARY EDUCATION">BACHELOR OF SECONDARY EDUCATION</option>
                                        <option value="BACHELOR OF ELEMENTARY EDUCATION">BACHELOR OF ELEMENTARY EDUCATION</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="Year_Level" class="block text-gray-700 font-medium">Year Level</label>
                                    <select id="Year_Level" name="Year_Level" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                        <option value="" disabled selected>Select Year Level</option>
                                        <option value="1st Year">1st Year</option>
                                        <option value="2nd Year">2nd Year</option>
                                        <option value="3rd Year">3rd Year</option>
                                        <option value="4th Year">4th Year</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="Username" class="block text-gray-700 font-medium">Username</label>
                                    <input type="text" id="Username" name="Username" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                </div>
                                <div class="mb-3">
                                    <label for="Password" class="block text-gray-700 font-medium">Password</label>
                                    <input type="password" id="Password" name="Password" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2 mt-4">
                            <button type="button" id="cancelBtn" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg">
                                Cancel
                            </button>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                                Add Student
                            </button>
                        </div>
                    </form>
                </div>
            </div>

                <button id="resetSessionBtn" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded ml-2">Reset All Session</button>
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
                    <form method="POST" class="flex items-center">
                        <span class="mr-2">Search:</span>
                        <input type="text" 
                               name="search"
                               value="<?php echo htmlspecialchars($search); ?>"
                               class="border rounded px-2 py-1">
                        <button type="submit" class="ml-2 bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white px-4 py-1 rounded hover:opacity-90">
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
                            <th class="px-6 py-3 text-left">Year Level</th>
                            <th class="px-6 py-3 text-left">Course</th>
                            <th class="px-6 py-3 text-left">Remaining Session</th>
                            <th class="px-6 py-3 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="bg-white">
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $fullName = $row['LAST_NAME'] . ', ' . $row['FIRST_NAME'] . ' ' . $row['MID_NAME'];
                                echo "<tr class='hover:bg-gray-100'>";
                                echo "<td class='px-6 py-4'>" . $row['IDNO'] . "</td>";
                                echo "<td class='px-6 py-4'>" . $fullName . "</td>";
                                echo "<td class='px-6 py-4'>" . $row['YEAR_LEVEL'] . "</td>";
                                echo "<td class='px-6 py-4'>" . $row['COURSE'] . "</td>";
                                echo "<td class='px-6 py-4'>" . $row['SESSION'] . "</td>";
                                echo "<td class='px-6 py-4'>
                                        <a href='edit_student.php?id=" . $row['STUD_NUM'] . "' class='text-blue-500 hover:text-blue-700 mr-3'>
                                            <i class='fas fa-edit'></i>
                                        </a>
                                        <a href='javascript:void(0);' class='text-red-500 hover:text-red-700' 
                                           onclick='confirmDelete(" . $row['STUD_NUM'] . ")'>
                                            <i class='fas fa-trash-alt'></i>
                                        </a>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='px-6 py-4 text-center text-gray-500 italic'>No data available</td></tr>";
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
    
    <script>
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

            for (let row of rows) {
                let found = false;
                const cells = row.getElementsByTagName('td');
                
                for (let cell of cells) {
                    const text = cell.textContent || cell.innerText;
                    if (text.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                
                row.style.display = found ? '' : 'none';
            }
        }

        function toggleNav(x) {
            document.getElementById("mySidenav").classList.toggle("-translate-x-0");
            document.getElementById("mySidenav").classList.toggle("-translate-x-full");
        }

        function closeNav() {
            document.getElementById("mySidenav").classList.remove("-translate-x-0");
            document.getElementById("mySidenav").classList.add("-translate-x-full");
        }
        
        // SweetAlert confirmation for delete
        function confirmDelete(studentId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete_student.php?id=' + studentId + '&confirmed=true';
                }
            });
        }
        
        // Reset all sessions with SweetAlert
        document.getElementById('resetSessionBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Reset All Sessions?',
                text: "This will reset ALL student sessions back to 30. Are you sure?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reset all!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Use fetch API to call reset_all_session.php
                    fetch('reset_all_session.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                title: 'Success!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Reload the page to refresh the table data
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong with the request.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        console.error('Error:', error);
                    });
                }
            });
        });

        // Open modal
        document.getElementById('addStudentBtn').addEventListener('click', () => {
            document.getElementById('addStudentModal').classList.remove('hidden');
            document.getElementById('addStudentModal').classList.add('flex');
        });

        // Close modal
        document.getElementById('closeModal').addEventListener('click', () => {
            document.getElementById('addStudentModal').classList.add('hidden');
            document.getElementById('addStudentModal').classList.remove('flex');
        });

        document.getElementById('cancelBtn').addEventListener('click', () => {
            document.getElementById('addStudentModal').classList.add('hidden');
            document.getElementById('addStudentModal').classList.remove('flex');
        });

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === document.getElementById('addStudentModal')) {
                document.getElementById('addStudentModal').classList.add('hidden');
                document.getElementById('addStudentModal').classList.remove('flex');
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Check for student added success message
            <?php if(isset($_SESSION['student_added']) && $_SESSION['student_added'] === true): ?>
                Swal.fire({
                    title: 'Success!',
                    text: 'Student has been added successfully',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'admin_studlist.php';
                    }
                });
                <?php unset($_SESSION['student_added']); // Clear the session variable ?>
            <?php endif; ?>
            
            // Form validation for ID number
            const idnoInput = document.getElementById('Idno');
            idnoInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 8);
            });

            // Name input validation (letters only)
            const nameInputs = ['Lastname', 'Firstname', 'Midname'];
            nameInputs.forEach(function(id) {
                const input = document.getElementById(id);
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
                });
            });

            // Form validation
            const form = document.getElementById('addStudentForm');
            form.addEventListener('submit', function(event) {
                // Validate ID number length
                if (idnoInput.value.length !== 8) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Error!',
                        text: 'ID Number must be exactly 8 digits.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
                
                // Let the form submit naturally if validation passes
                return true;
            });
        });
    </script>
</body>
</html>