<?php
// Start session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="./logo/ccs.png" type="image/x-icon">
<title>LabTrack - Sit-In Laboratory Management System</title>
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
theme: {
extend: {
colors: {
primary: '#6366f1',
secondary: '#8b5cf6',
accent: '#d946ef'
},
fontFamily: {
sans: ['Inter', 'sans-serif'],
},
}
}
}
</script>
<!-- Inter Font -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body {
font-family: 'Inter', sans-serif;
}

.gradient-primary {
background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
}

.gradient-blue {
background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
}

.gradient-green {
background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
}

.gradient-purple {
background: linear-gradient(135deg, #8b5cf6 0%, #d946ef 100%);
}

.gradient-amber {
background: linear-gradient(135deg, #d946ef 0%, #ec4899 100%);
}

.gradient-text {
background-clip: text;
-webkit-background-clip: text;
color: transparent;
background-image: linear-gradient(to right, #6366f1, #d946ef);
}

.glass-card {
background: rgba(255, 255, 255, 0.9);
backdrop-filter: blur(10px);
border: 1px solid rgba(255, 255, 255, 0.2);
}

.lab-pattern {
background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%236366f1' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.animate-float {
animation: float 3s ease-in-out infinite;
}

@keyframes float {
0% { transform: translateY(0px); }
50% { transform: translateY(-10px); }
100% { transform: translateY(0px); }
}
</style>
</head>
<body class="bg-gray-50 lab-pattern">
<!-- Navigation -->
<nav class="glass-card backdrop-blur-lg shadow-sm sticky top-0 z-50">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex justify-between h-16">
<div class="flex items-center">
<div class="flex-shrink-0 flex items-center">
<i class="fas fa-flask text-indigo-500 text-2xl mr-2"></i>
<span class="text-xl font-bold">Lab<span class="gradient-text">Track</span></span>
</div>
<div class="hidden md:ml-10 md:flex md:items-center md:space-x-8">
<a href="#" class="text-gray-800 hover:text-indigo-600 border-b-2 border-indigo-500 px-3 py-2 text-sm font-medium">Home</a>
<a href="Students.php" class="text-gray-600 hover:text-indigo-600 px-3 py-2 text-sm font-medium">Students</a>
<a href="#" class="text-gray-600 hover:text-indigo-600 px-3 py-2 text-sm font-medium">Laboratory Sessions</a>
<a href="#" class="text-gray-600 hover:text-indigo-600 px-3 py-2 text-sm font-medium">Equipment</a>
<a href="#" class="text-gray-600 hover:text-indigo-600 px-3 py-2 text-sm font-medium">Reports</a>
</div>
</div>
<div class="hidden md:flex items-center">
<button class="gradient-primary rounded-md px-5 py-2 text-sm font-medium text-white hover:opacity-90 transition-opacity duration-200">
Login
</button>
</div>
<div class="flex items-center md:hidden">
<button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
<i class="fas fa-bars text-xl"></i>
</button>
</div>
</div>
</div>
</nav>

<!-- Hero Section -->
<div class="relative overflow-hidden">
<div class="gradient-primary absolute inset-0 -z-10 transform-gpu overflow-hidden"></div>
<div class="absolute inset-0 -z-10 bg-white/30 backdrop-blur-[2px]"></div>

<!-- Gradient Patterns (purely decorative) -->
<div class="absolute top-1/4 -left-10 w-72 h-72 bg-indigo-500/30 rounded-full mix-blend-multiply filter blur-2xl opacity-70 animate-float"></div>
<div class="absolute top-1/3 left-1/2 w-72 h-72 bg-purple-500/30 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-float" style="animation-delay: 2s;"></div>
<div class="absolute bottom-1/4 right-1/4 w-60 h-60 bg-pink-500/30 rounded-full mix-blend-multiply filter blur-2xl opacity-70 animate-float" style="animation-delay: 1s;"></div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
<div class="grid md:grid-cols-2 gap-12 items-center">
<div class="text-center md:text-left">
<h1 class="text-4xl sm:text-5xl font-extrabold text-white leading-tight">
Modern Sit-In Laboratory Management
</h1>
<p class="mt-6 text-xl text-indigo-50">
Streamline lab sessions, track student attendance, and manage equipment efficiently with our comprehensive laboratory management system.
</p>
<div class="mt-10 flex flex-wrap gap-4 justify-center md:justify-start">
<a href="Students.php" class="glass-card px-6 py-3 rounded-lg text-indigo-600 font-medium hover:bg-white/95 transition-all duration-200 shadow-md">
Manage Students
</a>
<a href="#features" class="px-6 py-3 rounded-lg bg-white/20 text-white border border-white/30 font-medium backdrop-blur-sm hover:bg-white/30 transition-all duration-200">
Explore Features
</a>
</div>
<div class="mt-8 flex items-center justify-center md:justify-start text-white/70 text-sm">
<i class="fas fa-shield-alt mr-2"></i>
<span>Secure data storage</span>
<i class="fas fa-check-circle mx-2"></i>
<span>GDPR Compliant</span>
</div>
</div>
<div class="relative">
<div class="relative h-[500px] w-full overflow-hidden rounded-2xl shadow-2xl">
<div class="glass-card absolute inset-0 p-6 flex flex-col">
<div class="flex justify-between items-center border-b border-gray-200 pb-4">
<h3 class="font-semibold text-gray-800">Laboratory Session</h3>
<div class="flex space-x-1">
<div class="h-3 w-3 bg-red-400 rounded-full"></div>
<div class="h-3 w-3 bg-yellow-400 rounded-full"></div>
<div class="h-3 w-3 bg-green-400 rounded-full"></div>
</div>
</div>
<div class="mt-4">
<div class="mb-4">
<span class="block text-sm font-medium text-gray-700">Session Details</span>
<div class="mt-1 grid grid-cols-2 gap-4">
<div class="bg-gray-50 rounded-md p-3">
<span class="block text-xs text-gray-500">Laboratory</span>
<span class="block text-sm font-medium">Computer Science Lab 101</span>
</div>
<div class="bg-gray-50 rounded-md p-3">
<span class="block text-xs text-gray-500">Date & Time</span>
<span class="block text-sm font-medium">Mon, 10:00 AM - 12:00 PM</span>
</div>
</div>
</div>
<div class="mb-4">
<span class="block text-sm font-medium text-gray-700">Students Present</span>
<div class="mt-2 space-y-2">
<div class="flex items-center bg-indigo-50 p-2 rounded-md">
<div class="h-8 w-8 rounded-full bg-gradient-to-r from-indigo-400 to-indigo-600 flex items-center justify-center text-white text-xs">JD</div>
<div class="ml-3">
<span class="text-sm font-medium">John Doe</span>
<span class="block text-xs text-gray-500">CS-2023-0045</span>
</div>
<span class="ml-auto text-xs text-indigo-600 font-medium">Present</span>
</div>
<div class="flex items-center bg-indigo-50 p-2 rounded-md">
<div class="h-8 w-8 rounded-full bg-gradient-to-r from-purple-400 to-purple-600 flex items-center justify-center text-white text-xs">AS</div>
<div class="ml-3">
<span class="text-sm font-medium">Anna Smith</span>
<span class="block text-xs text-gray-500">CS-2023-0062</span>
</div>
<span class="ml-auto text-xs text-indigo-600 font-medium">Present</span>
</div>
<div class="flex items-center bg-red-50 p-2 rounded-md">
<div class="h-8 w-8 rounded-full bg-gradient-to-r from-gray-400 to-gray-600 flex items-center justify-center text-white text-xs">MB</div>
<div class="ml-3">
<span class="text-sm font-medium">Mike Brown</span>
<span class="block text-xs text-gray-500">CS-2023-0078</span>
</div>
<span class="ml-auto text-xs text-red-600 font-medium">Absent</span>
</div>
</div>
</div>
<div class="pt-3 border-t border-gray-200 mt-4">
<div class="flex justify-between">
<button class="text-indigo-600 text-sm font-medium hover:text-indigo-700">Mark All Present</button>
<button class="gradient-purple text-white px-3 py-1 rounded-md text-sm hover:opacity-90">End Session</button>
</div>
</div>
</div>
</div>
</div>
<!-- Decorative elements -->
<div class="absolute -bottom-6 -right-6 h-24 w-24 bg-white/30 backdrop-blur-lg rounded-2xl z-0"></div>
<div class="absolute -top-6 -left-6 h-16 w-16 bg-white/20 backdrop-blur-lg rounded-xl z-0"></div>
</div>
</div>
</div>

<!-- Wave separator -->
<div class="absolute bottom-0 left-0 right-0">
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" fill="#fff">
<path d="M0,96L60,80C120,64,240,32,360,21.3C480,11,600,21,720,42.7C840,64,960,96,1080,96C1200,96,1320,64,1380,48L1440,32L1440,120L1380,120C1320,120,1200,120,1080,120C960,120,840,120,720,120C600,120,480,120,360,120C240,120,120,120,60,120L0,120Z"></path>
</svg>
</div>
</div>

<!-- Key Features Section -->
<div id="features" class="py-16 bg-white">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-16">
<h2 class="text-3xl font-bold gradient-text inline-block">Streamlined Laboratory Management</h2>
<p class="mt-4 text-xl text-gray-600 max-w-3xl mx-auto">
Our system simplifies the management of sit-in laboratory sessions, equipment tracking, and student attendance.
</p>
</div>

<div class="grid md:grid-cols-3 gap-8">
<!-- Feature 1 - Student Tracking -->
<div class="bg-white rounded-xl shadow-md overflow-hidden transition-transform duration-300 hover:-translate-y-1">
<div class="h-2 gradient-blue"></div>
<div class="p-6">
<div class="w-12 h-12 rounded-lg gradient-blue flex items-center justify-center mb-4">
<i class="fas fa-users text-white text-xl"></i>
</div>
<h3 class="text-lg font-semibold text-gray-800 mb-3">Student Attendance</h3>
<p class="text-gray-600 mb-4">
Track student presence in lab sessions with easy check-in/check-out functionality and automatic reporting.
</p>
<a href="Students.php" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
<span>Manage Students</span>
<i class="fas fa-arrow-right ml-2 text-sm"></i>
</a>
</div>
</div>

<!-- Feature 2 - Lab Sessions -->
<div class="bg-white rounded-xl shadow-md overflow-hidden transition-transform duration-300 hover:-translate-y-1">
<div class="h-2 gradient-purple"></div>
<div class="p-6">
<div class="w-12 h-12 rounded-lg gradient-purple flex items-center justify-center mb-4">
<i class="fas fa-flask text-white text-xl"></i>
</div>
<h3 class="text-lg font-semibold text-gray-800 mb-3">Session Management</h3>
<p class="text-gray-600 mb-4">
Create and manage laboratory sessions, assign instructors, and set capacity limits for each lab.
</p>
<a href="#" class="inline-flex items-center text-purple-600 hover:text-purple-800 font-medium">
<span>Schedule Sessions</span>
<i class="fas fa-arrow-right ml-2 text-sm"></i>
</a>
</div>
</div>

<!-- Feature 3 - Equipment Tracking -->
<div class="bg-white rounded-xl shadow-md overflow-hidden transition-transform duration-300 hover:-translate-y-1">
<div class="h-2 gradient-amber"></div>
<div class="p-6">
<div class="w-12 h-12 rounded-lg gradient-amber flex items-center justify-center mb-4">
<i class="fas fa-laptop text-white text-xl"></i>
</div>
<h3 class="text-lg font-semibold text-gray-800 mb-3">Equipment Inventory</h3>
<p class="text-gray-600 mb-4">
Keep track of laboratory equipment usage, maintenance schedules, and availability for each session.
</p>
<a href="#" class="inline-flex items-center text-pink-500 hover:text-pink-700 font-medium">
<span>View Inventory</span>
<i class="fas fa-arrow-right ml-2 text-sm"></i>
</a>
</div>
</div>
</div>
</div>
</div>

<!-- How It Works Section with Timeline -->
<div class="py-16 bg-gray-50">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-16">
<h2 class="text-3xl font-bold gradient-text inline-block">How It Works</h2>
<p class="mt-4 text-xl text-gray-600 max-w-3xl mx-auto">
A simplified workflow for managing laboratory sessions and student attendance
</p>
</div>

<div class="relative">
<!-- Vertical line for timeline -->
<div class="hidden md:block absolute left-1/2 transform -translate-x-1/2 h-full w-0.5 bg-gray-200"></div>

<!-- Timeline items -->
<div class="space-y-16">
<!-- Step 1 -->
<div class="relative">
<div class="hidden md:block absolute top-5 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-12 h-12 rounded-full gradient-blue flex items-center justify-center z-10">
<span class="text-white font-bold">1</span>
</div>
<div class="grid md:grid-cols-2 gap-8 items-center">
<div class="md:text-right">
<h3 class="text-xl font-bold text-gray-800">Schedule a Laboratory Session</h3>
<p class="mt-2 text-gray-600">
Create a new laboratory session by selecting the lab room, date, time, and assigning an instructor.
</p>
</div>
<div class="md:pl-16">
<div class="glass-card p-5 rounded-lg shadow-md">
<div class="bg-indigo-50 p-4 rounded-lg">
<div class="flex items-center justify-between mb-4">
<h4 class="font-medium text-indigo-800">New Session</h4>
<span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full">Computer Lab</span>
</div>
<div class="space-y-3">
<div>
<label class="block text-xs text-gray-500">Laboratory</label>
<select class="mt-1 w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-sm">
<option>Computer Science Lab 101</option>
<option>Computer Science Lab 102</option>
</select>
</div>
<div class="grid grid-cols-2 gap-3">
<div>
<label class="block text-xs text-gray-500">Date</label>
<input type="date" class="mt-1 w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-sm">
</div>
<div>
<label class="block text-xs text-gray-500">Time</label>
<input type="time" class="mt-1 w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-sm">
</div>
</div>
<button class="w-full gradient-blue text-white py-2 rounded-md text-sm mt-2">
Schedule Session
</button>
</div>
</div>
</div>
</div>
</div>
</div>

<!-- Step 2 -->
<div class="relative">
<div class="hidden md:block absolute top-5 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-12 h-12 rounded-full gradient-purple flex items-center justify-center z-10">
<span class="text-white font-bold">2</span>
</div>
<div class="grid md:grid-cols-2 gap-8 items-center">
<div class="md:order-last md:text-left">
<h3 class="text-xl font-bold text-gray-800">Student Check-In</h3>
<p class="mt-2 text-gray-600">
Students arrive at the laboratory and check-in using their ID numbers or QR codes.
</p>
</div>
<div class="md:order-first md:pr-16">
<div class="glass-card p-5 rounded-lg shadow-md">
<div class="bg-purple-50 p-4 rounded-lg">
<h4 class="font-medium text-purple-800 mb-3">Student Check-In</h4>
<div class="space-y-3">
<div>
<label class="block text-xs text-gray-500">Student ID</label>
<div class="flex mt-1">
<input type="text" class="flex-1 bg-white border border-gray-300 rounded-l-md px-3 py-2 text-sm" placeholder="Enter ID number">
<button class="bg-purple-600 text-white px-3 rounded-r-md">
<i class="fas fa-qrcode"></i>
</button>
</div>
</div>
<div class="border border-purple-200 p-3 rounded-lg bg-white">
<div class="flex items-center justify-between">
<div class="flex items-center">
<div class="h-8 w-8 rounded-full bg-gradient-to-r from-purple-400 to-purple-600 flex items-center justify-center text-white text-xs">AS</div>
<div class="ml-3">
<span class="text-sm font-medium">Anna Smith</span>
<span class="block text-xs text-gray-500">CS-2023-0062</span>
</div>
</div>
<span class="text-xs gradient-purple text-white px-2 py-1 rounded-full">Checked In</span>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>

<!-- Step 3 -->
<div class="relative">
<div class="hidden md:block absolute top-5 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-12 h-12 rounded-full gradient-amber flex items-center justify-center z-10">
<span class="text-white font-bold">3</span>
</div>
<div class="grid md:grid-cols-2 gap-8 items-center">
<div class="md:text-right">
<h3 class="text-xl font-bold text-gray-800">Monitor Session Activity</h3>
<p class="mt-2 text-gray-600">
Track student attendance, equipment usage, and overall session progress in real-time.
</p>
</div>
<div class="md:pl-16">
<div class="glass-card p-5 rounded-lg shadow-md">
<div class="bg-pink-50 p-4 rounded-lg">
<div class="flex items-center justify-between mb-4">
<h4 class="font-medium text-pink-800">Session Overview</h4>
<span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Active</span>
</div>
<div class="space-y-3">
<div class="flex justify-between text-sm">
<span class="text-gray-600">Attendance:</span>
<span class="text-gray-900 font-medium">15/20 students</span>
</div>
<div class="w-full bg-gray-200 rounded-full h-2">
<div class="gradient-primary h-2 rounded-full" style="width: 75%"></div>
</div>
<div class="flex justify-between text-sm">
<span class="text-gray-600">Time Elapsed:</span>
<span class="text-gray-900 font-medium">45 min / 2 hrs</span>
</div>
<div class="w-full bg-gray-200 rounded-full h-2">
<div class="gradient-primary h-2 rounded-full" style="width: 38%"></div>
</div>
<div class="flex justify-between text-sm">
<span class="text-gray-600">Equipment Usage:</span>
<span class="text-gray-900 font-medium">18/20 computers</span>
</div>
<div class="w-full bg-gray-200 rounded-full h-2">
<div class="gradient-primary h-2 rounded-full" style="width: 90%"></div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>

<!-- Step 4 -->
<div class="relative">
<div class="hidden md:block absolute top-5 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-12 h-12 rounded-full gradient-primary flex items-center justify-center z-10">
<span class="text-white font-bold">4</span>
</div>
<div class="grid md:grid-cols-2 gap-8 items-center">
<div class="md:order-last md:text-left">
<h3 class="text-xl font-bold text-gray-800">Generate Reports</h3>
<p class="mt-2 text-gray-600">
Access detailed reports on attendance, equipment usage, and laboratory utilization.
</p>
</div>
<div class="md:order-first md:pr-16">
<div class="glass-card p-5 rounded-lg shadow-md">
<div class="bg-indigo-50 p-4 rounded-lg">
<h4 class="font-medium text-indigo-800 mb-3">Report Generation</h4>
<div class="space-y-3">
<div class="flex items-center justify-between bg-white p-2 rounded-md border border-indigo-100">
<span class="text-sm">Attendance Report</span>
<button class="text-indigo-600 hover:text-indigo-800">
<i class="fas fa-download"></i>
</button>
</div>
<div class="flex items-center justify-between bg-white p-2 rounded-md border border-indigo-100">
<span class="text-sm">Equipment Usage Report</span>
<button class="text-indigo-600 hover:text-indigo-800">
<i class="fas fa-download"></i>
</button>
</div>
<div class="flex items-center justify-between bg-white p-2 rounded-md border border-indigo-100">
<span class="text-sm">Session Summary</span>
<button class="text-indigo-600 hover:text-indigo-800">
<i class="fas fa-download"></i>
</button>
</div>
<button class="w-full gradient-primary text-white py-2 rounded-md text-sm mt-2">
Generate Custom Report
</button>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>

<!-- Stats Section -->
<div class="gradient-primary py-16 relative overflow-hidden">
<!-- Decorative elements -->
<div class="absolute top-1/4 -left-10 w-72 h-72 bg-indigo-500/30 rounded-full mix-blend-multiply filter blur-2xl opacity-70 animate-float"></div>
<div class="absolute top-1/3 left-1/2 w-72 h-72 bg-purple-500/30 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-float" style="animation-delay: 2s;"></div>
<div class="absolute bottom-1/4 right-1/4 w-60 h-60 bg-pink-500/30 rounded-full mix-blend-multiply filter blur-2xl opacity-70 animate-float" style="animation-delay: 1s;"></div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
<div class="text-center mb-12">
<h2 class="text-3xl font-bold text-white">LabTrack by Numbers</h2>
<p class="mt-4 text-xl text-indigo-100 max-w-3xl mx-auto">
Our system is trusted by educational institutions worldwide.
</p>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
<div class="glass-card backdrop-blur-lg rounded-lg p-6 text-white">
<div class="text-4xl font-bold mb-2">500+</div>
<div class="text-indigo-100">Laboratories</div>
</div>
<div class="glass-card backdrop-blur-lg rounded-lg p-6 text-white">
<div class="text-4xl font-bold mb-2">50k+</div>
<div class="text-indigo-100">Students</div>
</div>
<div class="glass-card backdrop-blur-lg rounded-lg p-6 text-white">
<div class="text-4xl font-bold mb-2">10k+</div>
<div class="text-indigo-100">Monthly Sessions</div>
</div>
<div class="glass-card backdrop-blur-lg rounded-lg p-6 text-white">
<div class="text-4xl font-bold mb-2">98%</div>
<div class="text-indigo-100">Satisfaction</div>
</div>
</div>
</div>
</div>

<!-- Lab Types Section -->
<div class="py-16 bg-white">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-12">
<h2 class="text-3xl font-bold gradient-text inline-block">Compatible with All Lab Types</h2>
<p class="mt-4 text-xl text-gray-600 max-w-3xl mx-auto">
Our system is designed to work with various laboratory environments across different disciplines.
</p>
</div>

<div class="grid md:grid-cols-3 gap-8">
<!-- Computer Labs -->
<div class="rounded-xl overflow-hidden shadow-md transition-transform duration-300 hover:-translate-y-1">
<div class="h-48 gradient-blue flex items-center justify-center">
<i class="fas fa-laptop-code text-5xl text-white"></i>
</div>
<div class="p-6 bg-white">
<h3 class="text-xl font-bold text-gray-800">Computer Science Labs</h3>
<p class="mt-2 text-gray-600">
Track computer usage, software installations, and student programming sessions.
</p>
<a href="#" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 mt-4 text-sm font-medium">
<span>Learn more</span>
<i class="fas fa-arrow-right ml-2"></i>
</a>
</div>
</div>

<!-- Chemistry Labs -->
<div class="rounded-xl overflow-hidden shadow-md transition-transform duration-300 hover:-translate-y-1">
<div class="h-48 gradient-purple flex items-center justify-center">
<i class="fas fa-flask text-5xl text-white"></i>
</div>
<div class="p-6 bg-white">
<h3 class="text-xl font-bold text-gray-800">Chemistry Labs</h3>
<p class="mt-2 text-gray-600">
Manage chemical inventory, experiment schedules, and safety procedures.
</p>
<a href="#" class="inline-flex items-center text-purple-600 hover:text-purple-800 mt-4 text-sm font-medium">
<span>Learn more</span>
<i class="fas fa-arrow-right ml-2"></i>
</a>
</div>
</div>

<!-- Physics Labs -->
<div class="rounded-xl overflow-hidden shadow-md transition-transform duration-300 hover:-translate-y-1">
<div class="h-48 gradient-amber flex items-center justify-center">
<i class="fas fa-atom text-5xl text-white"></i>
</div>
<div class="p-6 bg-white">
<h3 class="text-xl font-bold text-gray-800">Physics Labs</h3>
<p class="mt-2 text-gray-600">
Keep track of physics equipment, experimental setups, and student group assignments.
</p>
<a href="#" class="inline-flex items-center text-pink-600 hover:text-pink-800 mt-4 text-sm font-medium">
<span>Learn more</span>
<i class="fas fa-arrow-right ml-2"></i>
</a>
</div>
</div>
</div>
</div>
</div>

<!-- Call to Action -->
<div class="py-16 bg-gray-50">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="glass-card rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm">
<div class="gradient-primary p-1">
<div class="bg-white rounded-xl">
<div class="grid md:grid-cols-2">
<div class="p-10 flex items-center">
<div>
<h2 class="text-3xl font-extrabold text-gray-900">Ready to Transform Your Laboratory Management?</h2>
<p class="mt-4 text-lg text-gray-600">
Join hundreds of educational institutions that trust LabTrack for managing their sit-in laboratory sessions.
</p>
<div class="mt-8 flex flex-wrap gap-4">
<a href="Students.php" class="gradient-primary px-6 py-3 rounded-lg text-white font-medium hover:opacity-90 shadow-md transition-all duration-200">
Start Managing Now
</a>
<a href="#" class="px-6 py-3 rounded-lg bg-gray-100 text-gray-800 font-medium hover:bg-gray-200 transition-all duration-200">
Schedule a Demo
</a>
</div>
</div>
</div>
<div class="gradient-primary p-10 flex items-center justify-center">
<div class="text-center">
<div class="h-24 w-24 rounded-full bg-white/20 backdrop-blur-lg flex items-center justify-center mx-auto mb-6">
<i class="fas fa-laptop-code text-4xl text-white"></i>
</div>
<h3 class="text-xl font-bold text-white mb-2">Student-Centered Design</h3>
<p class="text-indigo-100 max-w-xs mx-auto">
Built with students and instructors in mind, our system prioritizes ease of use and efficiency.
</p>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>

<!-- Footer -->
<footer class="bg-gray-900 text-white">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
<div class="grid md:grid-cols-4 gap-8">
<div>
<div class="flex items-center">
<i class="fas fa-flask text-indigo-400 text-2xl mr-2"></i>
<span class="text-xl font-bold">Lab<span class="gradient-text">Track</span></span>
</div>
<p class="mt-4 text-gray-400">
Modernizing laboratory session management for educational institutions.
</p>
<div class="mt-6 flex space-x-4">
<a href="#" class="text-gray-400 hover:text-white transition-colors">
<i class="fab fa-facebook"></i>
</a>
<a href="#" class="text-gray-400 hover:text-white transition-colors">
<i class="fab fa-twitter"></i>
</a>
<a href="#" class="text-gray-400 hover:text-white transition-colors">
<i class="fab fa-linkedin"></i>
</a>
</div>
</div>

<div>
<h3 class="text-sm font-semibold uppercase tracking-wider">Features</h3>
<ul class="mt-4 space-y-2">
<li><a href="Students.php" class="text-gray-400 hover:text-white transition-colors">Student Management</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition-colors">Lab Sessions</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition-colors">Equipment Tracking</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition-colors">Reports & Analytics</a></li>
</ul>
</div>

<div>
<h3 class="text-sm font-semibold uppercase tracking-wider">Resources</h3>
<ul class="mt-4 space-y-2">
<li><a href="#" class="text-gray-400 hover:text-white transition-colors">Documentation</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition-colors">API Reference</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition-colors">Help Center</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition-colors">System Updates</a></li>
</ul>
</div>

<div>
<h3 class="text-sm font-semibold uppercase tracking-wider">Company</h3>
<ul class="mt-4 space-y-2">
<li><a href="#" class="text-gray-400 hover:text-white transition-colors">About</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition-colors">Careers</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy</a></li>
</ul>
</div>
</div>

<div class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
<p class="text-sm text-gray-400">&copy; 2023 LabTrack. All rights reserved.</p>
<div class="mt-4 md:mt-0 flex flex-wrap gap-4">
<a href="#" class="text-sm text-gray-400 hover:text-white transition-colors">Privacy Policy</a>
<a href="#" class="text-sm text-gray-400 hover:text-white transition-colors">Terms of Service</a>
<a href="#" class="text-sm text-gray-400 hover:text-white transition-colors">Cookie Policy</a>
</div>
</div>
</div>
</footer>

<script>
// Add any JavaScript functionality here
</script>
</body>
</html>