<?php
session_start();
require '../db.php'; // Updated path

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
    
}

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
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.all.min.js"></script>
    <title>History</title>
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
            <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Logo" class="w-24 h-24 rounded-full border-2 border-white object-cover mb-2">
            <p class="text-white font-bold text-lg mb-3"><?php echo htmlspecialchars($firstName); ?></p>
        </div>

        <nav class="flex flex-col space-y-0.5 px-2">
            <div class="overflow-hidden">
                <a href="dashboard.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-home w-6 text-base"></i>
                    <span class="text-sm font-medium">HOME</span>
                </a>
            </div>
            <div class="overflow-hidden">
                <a href="profile.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-user w-6 text-base"></i>
                    <span class="text-sm font-medium">PROFILE</span>
                </a>
            </div>
            <div class="overflow-hidden">
                <a href="edit.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-edit w-6 text-base"></i>
                    <span class="text-sm font-medium">EDIT</span>
                </a>
            </div>
            <div class="overflow-hidden">
                <a href="history.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-history w-6 text-base"></i>
                    <span class="text-sm font-medium">HISTORY</span>
                </a>
            </div>
            <div class="overflow-hidden">
                <a href="reservation.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                    <i class="fas fa-calendar-alt w-6 text-base"></i>
                    <span class="text-sm font-medium">RESERVATION</span>
                </a>
            </div>

        <div class="overflow-hidden">
            <a href="../logout.php" class="px-3 py-2 text-white hover:bg-white/20 hover:translate-x-1 transition-all duration-200 flex items-center w-full rounded">
                <i class="fas fa-sign-out-alt w-6 text-base"></i>
                <span class="text-sm font-medium">LOG OUT</span> <!-- Updated font size and weight -->
            </a>
        </div>
    </div>

    <!-- History Content -->
    <div class="container mx-auto px-4 mt-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
            <div class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white p-4 flex items-center justify-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-16 h-16 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                <i class="fas fa-history text-2xl mr-4 relative z-10"></i>
                <h2 class="text-xl font-bold tracking-wider uppercase relative z-10">History Information</h2>
            </div>
            
            <div class="p-6">
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
                                <th class="px-6 py-3 text-left">Purpose</th>
                                <th class="px-6 py-3 text-left">Laboratory</th>
                                <th class="px-6 py-3 text-left">Time In</th>
                                <th class="px-6 py-3 text-left">Time Out</th>
                                <th class="px-6 py-3 text-left">Date</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        // Get user's IDNO
                        $getUserQuery = "SELECT IDNO FROM users WHERE STUD_NUM = ?";
                        $stmt = $conn->prepare($getUserQuery);
                        $stmt->bind_param("i", $_SESSION['user_id']);
                        $stmt->execute();
                        $userResult = $stmt->get_result();
                        $userData = $userResult->fetch_assoc();
                        $stmt->close();

                        if ($userData) {
                            $userIdno = $userData['IDNO'];
                            
                            // Get total count of records for this user
                            $total_query = "SELECT COUNT(*) as total FROM curr_sitin WHERE IDNO = ?";
                            $stmt = $conn->prepare($total_query);
                            $stmt->bind_param("i", $userIdno);
                            $stmt->execute();
                            $total_result = $stmt->get_result();
                            $total_row = $total_result->fetch_assoc();
                            $total_entries = $total_row['total'];
                            $stmt->close();

                            // Set entries per page (default 10)
                            $entries_per_page = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;
                            $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $offset = ($current_page - 1) * $entries_per_page;

                            // Fetch sit-in history with pagination
                            $query = "SELECT * FROM curr_sitin WHERE IDNO = ? ORDER BY DATE DESC, TIME_IN DESC LIMIT ? OFFSET ?";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("iii", $userIdno, $entries_per_page, $offset);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr class='hover:bg-gray-50'>";
                                    echo "<td class='px-6 py-4'>" . htmlspecialchars($row['IDNO']) . "</td>";
                                    echo "<td class='px-6 py-4'>" . htmlspecialchars($row['FULL_NAME']) . "</td>";
                                    echo "<td class='px-6 py-4'>" . htmlspecialchars($row['PURPOSE']) . "</td>";
                                    echo "<td class='px-6 py-4'>" . htmlspecialchars($row['LABORATORY']) . "</td>";
                                    // Convert time format
                                    $timeIn = date('h:i A', strtotime($row['TIME_IN']));
                                    $timeOut = $row['TIME_OUT'] ? date('h:i A', strtotime($row['TIME_OUT'])) : '-';
                                    echo "<td class='px-6 py-4'>" . $timeIn . "</td>";
                                    echo "<td class='px-6 py-4'>" . $timeOut . "</td>";
                                    echo "<td class='px-6 py-4'>" . htmlspecialchars($row['DATE']) . "</td>";
                                    echo "<td class='px-6 py-4'>" . htmlspecialchars($row['STATUS']) . "</td>";
                                    echo "<td class='px-6 py-4'>";
                                    echo "<button onclick=\"openFeedbackModal(" . $row['SITIN_ID'] . ", '" . $row['LABORATORY'] . "')\" 
                                            class='bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-sm transition duration-200'>
                                            Feedback
                                          </button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9' class='px-6 py-4 text-center'>";
                                echo "<div class='text-gray-500 italic'>No sit-in records found</div>";
                                echo "<div class='text-sm text-gray-400 mt-1'>Your sit-in history will appear here once you start using the facilities</div>";
                                echo "</td></tr>";
                            }
                            $stmt->close();
                        } else {
                            echo "<tr><td colspan='9' class='px-6 py-4 text-center'>";
                            echo "<div class='text-gray-500 italic'>Welcome new user!</div>";
                            echo "<div class='text-sm text-gray-400 mt-1'>Your sit-in history will be displayed here after your first facility use</div>";
                            echo "</td></tr>";
                        }
                        ?>
                    </table>
                </div>

                <div class="flex justify-between items-center mt-4">
                    <div class="text-gray-600">
                        <?php
                        $start_entry = $total_entries > 0 ? $offset + 1 : 0;
                        $end_entry = min($offset + $entries_per_page, $total_entries);
                        echo "Showing $start_entry to $end_entry of $total_entries entries";
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

    <!-- Feedback Modal -->
    <div id="feedbackModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-96">
            <h2 class="text-xl font-bold mb-4">Submit Feedback</h2>
            <form id="feedbackForm" onsubmit="submitFeedback(event)">
                <input type="hidden" id="sitinId" name="sitinId">
                <input type="hidden" id="laboratory" name="laboratory">
                <textarea id="feedbackText" name="feedback" rows="4" 
                    class="w-full p-2 border rounded mb-4" 
                    placeholder="Enter your feedback here..." 
                    required></textarea>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeFeedbackModal()" 
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit" 
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Submit</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="py-3 px-6 bg-white relative mt-8">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500"></div>
        <p class="text-center text-xs text-gray-600">
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

        function openFeedbackModal(sitinId, laboratory) {
            document.getElementById('feedbackModal').classList.remove('hidden');
            document.getElementById('feedbackModal').classList.add('flex');
            document.getElementById('sitinId').value = sitinId;
            document.getElementById('laboratory').value = laboratory;
        }

        function closeFeedbackModal() {
            document.getElementById('feedbackModal').classList.add('hidden');
            document.getElementById('feedbackModal').classList.remove('flex');
        }

        function submitFeedback(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById('feedbackForm'));

            fetch('feedback_submit.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // SweetAlert success message
                    Swal.fire({
                        title: 'Success!',
                        text: 'Feedback Successfully Submitted',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6',
                        timer: 3000,
                        timerProgressBar: true
                    });
                    closeFeedbackModal();
                    // Optionally refresh the page or update UI
                    // setTimeout(() => location.reload(), 2000);
                } else {
                    // SweetAlert error message
                    Swal.fire({
                        title: 'Error!',
                        text: 'Error submitting feedback: ' + data.message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                }
            })
            .catch(error => {
                // SweetAlert error message for network issues
                Swal.fire({
                    title: 'Error!',
                    text: 'Network error submitting feedback. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                });
            });
        }
    </script>
</body>
</html>