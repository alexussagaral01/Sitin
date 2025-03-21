<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
    
}

$firstName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'Guest';
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($userId) {
    $stmt = $conn->prepare("SELECT IDNO, LAST_NAME, FIRST_NAME, MID_NAME, COURSE, YEAR_LEVEL, EMAIL, ADDRESS, UPLOAD_IMAGE FROM users WHERE STUD_NUM = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($idNo, $lastName, $dbFirstName, $midName, $course, $yearLevel, $email, $address, $userImage);
    $stmt->fetch();
    $stmt->close();
    
    $profileImage = !empty($userImage) ? '../images/' . $userImage : "../images/image.jpg";
} else {
    $profileImage = "../images/image.jpg";
    $idNo = '';
    $lastName = '';
    $dbFirstName = '';
    $midName = '';
    $course = '';
    $yearLevel = '';
    $email = '';
    $address = '';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle profile update
    if (isset($_POST['action']) && $_POST['action'] == 'update_profile') {
        $idno = $_POST['Idno'];
        $lastname = $_POST['Lastname'];
        $firstname = $_POST['Firstname'];
        $midname = $_POST['Midname'];
        $course = $_POST['Course'];
        $year_level = $_POST['Year_Level'];
        $email = $_POST['Email'];
        $address = $_POST['Address'];
        
        $uploadImagePath = $userImage; // Keep existing image by default
        
        // Handle image upload
        if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == 0) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileType = finfo_file($fileInfo, $_FILES['profileImage']['tmp_name']);
            finfo_close($fileInfo);
            
            if (in_array($fileType, $allowedTypes)) {
                // Create images directory if it doesn't exist
                $targetDir = "../images/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                
                // Generate unique filename
                $fileName = uniqid() . '_' . basename($_FILES["profileImage"]["name"]);
                $targetFile = $targetDir . $fileName;
                
                if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetFile)) {
                    $uploadImagePath = $fileName;
                    $_SESSION['profile_image'] = $fileName;
                    
                    // Delete old image if it exists and is not the default image
                    if (!empty($userImage) && $userImage != "image.jpg" && file_exists($targetDir . $userImage)) {
                        unlink($targetDir . $userImage);
                    }
                }
            }
        }

        try {
            $stmt = $conn->prepare("UPDATE users SET IDNO = ?, LAST_NAME = ?, FIRST_NAME = ?, MID_NAME = ?, COURSE = ?, YEAR_LEVEL = ?, EMAIL = ?, ADDRESS = ?, UPLOAD_IMAGE = ? WHERE STUD_NUM = ?");
            $stmt->bind_param("sssssssssi", $idno, $lastname, $firstname, $midname, $course, $year_level, $email, $address, $uploadImagePath, $userId);
            
            if ($stmt->execute()) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Profile updated successfully.",
                    "image" => $uploadImagePath
                ]);
            } else {
                throw new Exception("Failed to update profile");
            }
            
            $stmt->close();
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
        
        exit;
    } 
    // Handle password change
    elseif (isset($_POST['action']) && $_POST['action'] == 'change_password') {
        $currentPassword = $_POST['currentPassword'];
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];
        
        // Verify passwords match
        if ($newPassword !== $confirmPassword) {
            echo json_encode([
                "status" => "error",
                "message" => "New passwords do not match."
            ]);
            exit;
        }
        
        try {
            // First verify current password
            $stmt = $conn->prepare("SELECT PASSWORD_HASH FROM users WHERE STUD_NUM = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->bind_result($hashedPassword);
            $stmt->fetch();
            $stmt->close();
            
            // Verify current password
            if (!password_verify($currentPassword, $hashedPassword)) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Current password is incorrect."
                ]);
                exit;
            }
            
            // Hash the new password
            $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update the password
            $stmt = $conn->prepare("UPDATE users SET PASSWORD_HASH = ? WHERE STUD_NUM = ?");
            $stmt->bind_param("si", $newHashedPassword, $userId);
            
            if ($stmt->execute()) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Password updated successfully."
                ]);
            } else {
                throw new Exception("Failed to update password");
            }
            
            $stmt->close();
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
        
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="../logo/ccs.png" type="image/x-icon">
    <title>Edit</title>
</head>
<body class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)]">
    <div class="content-wrapper">
        <!-- Header (removed fixed positioning) -->
        <div class="text-center bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white font-bold text-2xl py-4 relative">
            CCS SIT-IN MONITORING SYSTEM
            <div class="absolute top-4 left-6 cursor-pointer" onclick="toggleNav(this)">
                <div class="bar1 w-8 h-1 bg-white my-1 transition-all duration-300"></div>
                <div class="bar2 w-8 h-1 bg-white my-1 transition-all duration-300"></div>
                <div class="bar3 w-8 h-1 bg-white my-1 transition-all duration-300"></div>
            </div>
        </div>

        <!-- Form Container - Updated with border styling -->
        <div class="mx-auto my-8 max-w-4xl">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
                <div class="bg-gradient-to-r from-[rgba(74,105,187,1)] to-[rgba(205,77,204,1)] text-white p-4 flex items-center justify-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <i class="fas fa-user-edit text-2xl mr-4 relative z-10"></i>
                    <h2 class="text-xl font-bold tracking-wider uppercase relative z-10">Edit Student Profile</h2>
                </div>

                <!-- Tabs Navigation -->
                <div class="flex justify-center border-b border-gray-200">
                    <button type="button" 
                            class="tab-button px-6 py-3 font-medium text-sm active text-blue-600 border-b-2 border-blue-600" 
                            data-tab="edit-profile">
                        <i class="fas fa-user-edit mr-2"></i>Edit Profile
                    </button>
                    <button type="button" 
                            class="tab-button px-6 py-3 font-medium text-sm text-gray-500" 
                            data-tab="change-password">
                        <i class="fas fa-key mr-2"></i>Change Password
                    </button>
                </div>

                <!-- Edit Profile Tab Content -->
                <div id="edit-profile" class="tab-content p-6 block">
                    <img src="<?php echo htmlspecialchars($profileImage); ?>" 
                         alt="Student Image" 
                         class="w-[150px] h-[150px] rounded-full object-cover mx-auto block cursor-pointer border-3 border-white shadow-[0_0_15px_rgba(0,0,0,0.2)] mb-6 hover:opacity-80 transition-opacity"
                         id="profileImage"
                         title="Click to change profile picture">
                    <input type="file" id="fileInput" name="profileImage" accept="image/*" class="hidden" form="editForm">
                    
                    <form id="editForm" method="POST" action="" class="mt-4">
                        <input type="hidden" name="action" value="update_profile">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- ID Number -->
                            <div class="mb-4 relative col-span-1 md:col-span-2">
                                <i class="fas fa-id-card absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 bg-white px-2"></i>
                                <input type="text" id="Idno" name="Idno" class="w-full pl-12 py-2 border rounded-lg focus:outline-none cursor-not-allowed focus:ring-2 focus:ring-blue-500 bg-gray-100" value="<?php echo htmlspecialchars($idNo); ?>" readonly>
                            </div>

                            <!-- Name Fields -->
                            <div class="mb-4 relative col-span-1 md:col-span-2 grid grid-cols-3 gap-4">
                                <div class="relative">
                                    <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 bg-white px-2"></i>
                                    <input type="text" id="Lastname" name="Lastname" class="w-full pl-12 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Last Name" value="<?php echo htmlspecialchars($lastName); ?>" required>
                                </div>
                                <div class="relative">
                                    <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 bg-white px-2"></i>
                                    <input type="text" id="Firstname" name="Firstname" class="w-full pl-12 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="First Name" value="<?php echo htmlspecialchars($dbFirstName); ?>" required>
                                </div>
                                <div class="relative">
                                    <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 bg-white px-2"></i>
                                    <input type="text" id="Midname" name="Midname" class="w-full pl-12 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Middle Name" value="<?php echo htmlspecialchars($midName); ?>">
                                </div>
                            </div>

                            <!-- Course -->
                            <div class="mb-4 relative">
                                <i class="fas fa-book absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 bg-white px-2"></i>
                                <select id="Course" name="Course" class="w-full pl-12 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="" disabled>Select a Course</option>
                                    <option value="BS IN ACCOUNTANCY" <?php if ($course == 'BS IN ACCOUNTANCY') echo 'selected'; ?>>BS IN ACCOUNTANCY</option>
                                    <option value="BS IN BUSINESS ADMINISTRATION" <?php if ($course == 'BS IN BUSINESS ADMINISTRATION') echo 'selected'; ?>>BS IN BUSINESS ADMINISTRATION</option>
                                    <option value="BS IN CRIMINOLOGY" <?php if ($course == 'BS IN CRIMINOLOGY') echo 'selected'; ?>>BS IN CRIMINOLOGY</option>
                                    <option value="BS IN CUSTOMS ADMINISTRATION" <?php if ($course == 'BS IN CUSTOMS ADMINISTRATION') echo 'selected'; ?>>BS IN CUSTOMS ADMINISTRATION</option>
                                    <option value="BS IN INFORMATION TECHNOLOGY" <?php if ($course == 'BS IN INFORMATION TECHNOLOGY') echo 'selected'; ?>>BS IN INFORMATION TECHNOLOGY</option>
                                    <option value="BS IN COMPUTER SCIENCE" <?php if ($course == 'BS IN COMPUTER SCIENCE') echo 'selected'; ?>>BS IN COMPUTER SCIENCE</option>
                                    <option value="BS IN OFFICE ADMINISTRATION" <?php if ($course == 'BS IN OFFICE ADMINISTRATION') echo 'selected'; ?>>BS IN OFFICE ADMINISTRATION</option>
                                    <option value="BS IN SOCIAL WORK" <?php if ($course == 'BS IN SOCIAL WORK') echo 'selected'; ?>>BS IN SOCIAL WORK</option>
                                    <option value="BACHELOR OF SECONDARY EDUCATION" <?php if ($course == 'BACHELOR OF SECONDARY EDUCATION') echo 'selected'; ?>>BACHELOR OF SECONDARY EDUCATION</option>
                                    <option value="BACHELOR OF ELEMENTARY EDUCATION" <?php if ($course == 'BACHELOR OF ELEMENTARY EDUCATION') echo 'selected'; ?>>BACHELOR OF ELEMENTARY EDUCATION</option>
                                </select>
                            </div>

                            <!-- Year Level -->
                            <div class="mb-4 relative">
                                <i class="fas fa-graduation-cap absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 bg-white px-2"></i>
                                <select id="Year_Level" name="Year_Level" class="w-full pl-12 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="" disabled>Select a Year Level</option>
                                    <option value="1st Year" <?php if ($yearLevel == '1st Year') echo 'selected'; ?>>1st Year</option>
                                    <option value="2nd Year" <?php if ($yearLevel == '2nd Year') echo 'selected'; ?>>2nd Year</option>
                                    <option value="3rd Year" <?php if ($yearLevel == '3rd Year') echo 'selected'; ?>>3rd Year</option>
                                    <option value="4th Year" <?php if ($yearLevel == '4th Year') echo 'selected'; ?>>4th Year</option>
                                </select>
                            </div>

                            <!-- Email -->
                            <div class="mb-4 relative col-span-1 md:col-span-2">
                                <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 bg-white px-2"></i>
                                <input type="email" id="Email" name="Email" class="w-full pl-12 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>

                            <!-- Address -->
                            <div class="mb-4 relative col-span-1 md:col-span-2">
                                <i class="fas fa-home absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 bg-white px-2"></i>
                                <input type="text" id="Address" name="Address" class="w-full pl-12 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Address" value="<?php echo htmlspecialchars($address); ?>" required>
                            </div>
                        </div>

                        <div class="text-center mt-6">
                            <button type="submit" class="bg-gradient-to-r from-purple-700 to-pink-500 text-white py-2 px-6 rounded-lg hover:from-pink-500 hover:to-purple-700 hover:text-black transition-all duration-300">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password Tab Content -->
                <div id="change-password" class="tab-content p-6 hidden">
                    <div class="max-w-md mx-auto">
                        <div class="text-center mb-6">
                            <i class="fas fa-key text-5xl text-purple-500 mb-3"></i>
                            <h3 class="text-xl font-semibold">Change Your Password</h3>
                            <p class="text-gray-500 text-sm">Make sure to choose a strong password</p>
                        </div>
                        
                        <form id="passwordForm" method="POST" action="" class="mt-4">
                            <input type="hidden" name="action" value="change_password">
                            
                            <!-- Current Password -->
                            <div class="mb-4 relative">
                                <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 bg-white px-2"></i>
                                <input type="password" id="currentPassword" name="currentPassword" class="w-full pl-12 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Current Password" required>
                            </div>
                            
                            <!-- New Password -->
                            <div class="mb-4 relative">
                                <i class="fas fa-key absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 bg-white px-2"></i>
                                <input type="password" id="newPassword" name="newPassword" class="w-full pl-12 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="New Password" required>
                            </div>
                            
                            <!-- Confirm New Password -->
                            <div class="mb-4 relative">
                                <i class="fas fa-check-circle absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 bg-white px-2"></i>
                                <input type="password" id="confirmPassword" name="confirmPassword" class="w-full pl-12 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Confirm New Password" required>
                            </div>
                            
                            <div class="text-center mt-6">
                                <button type="submit" class="bg-gradient-to-r from-purple-700 to-pink-500 text-white py-2 px-6 rounded-lg hover:from-pink-500 hover:to-purple-700 hover:text-black transition-all duration-300">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
    <script>
        function toggleNav(x) {
            document.getElementById("mySidenav").classList.toggle("-translate-x-0");
            document.getElementById("mySidenav").classList.toggle("-translate-x-full");
        }

        function closeNav() {
            document.getElementById("mySidenav").classList.remove("-translate-x-0");
            document.getElementById("mySidenav").classList.add("-translate-x-full");
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching functionality
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'text-blue-600', 'border-b-2', 'border-blue-600');
                        btn.classList.add('text-gray-500');
                    });
                    
                    // Add active class to clicked button
                    this.classList.add('active', 'text-blue-600', 'border-b-2', 'border-blue-600');
                    this.classList.remove('text-gray-500');
                    
                    // Hide all tab contents
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                        content.classList.remove('block');
                    });
                    
                    // Show the target tab content
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.remove('hidden');
                    document.getElementById(tabId).classList.add('block');
                });
            });

            const idnoInput = document.getElementById('Idno');
            idnoInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 8);
            });

            const nameInputs = ['Lastname', 'Firstname', 'Midname'];
            nameInputs.forEach(function(id) {
                const input = document.getElementById(id);
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
                });
            });

            const logoutLink = document.querySelector("a[href='../logout.php']");
            if (logoutLink) {
                logoutLink.addEventListener("click", function(e) {
                    e.preventDefault();
                    fetch("../login.php", {
                        method: "POST"
                    })
                    .then(response => {
                        if (response.ok) {
                            window.location.href = "../login.php";
                        } else {
                            console.error("Logout failed");
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                    });
                });
            }
        });

        // Add image upload handler
        document.getElementById('profileImage').addEventListener('click', function() {
            document.getElementById('fileInput').click();
        });

        document.getElementById('fileInput').addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profileImage').src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Update profile form submission
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message
                    }).then(() => {
                        // Update the profile image in the sidebar if it was changed
                        if (data.image) {
                            const sidebarImage = document.querySelector('#mySidenav img');
                            if (sidebarImage) {
                                sidebarImage.src = '../images/' + data.image;
                            }
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An unexpected error occurred'
                });
            });
        });

        // Password form submission
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate password fields
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (newPassword !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'New passwords do not match'
                });
                return;
            }
            
            const formData = new FormData(this);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message
                    }).then(() => {
                        // Clear password fields after successful update
                        document.getElementById('currentPassword').value = '';
                        document.getElementById('newPassword').value = '';
                        document.getElementById('confirmPassword').value = '';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An unexpected error occurred'
                });
            });
        });
    </script>
</body>
</html>