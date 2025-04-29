<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Attendance Monitoring System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-xl font-bold text-gray-800">BAMS</span>
                    </div>
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="hidden md:ml-6 md:flex md:space-x-8">
                        <a href="dashboard.php" class="inline-flex items-center px-1 pt-1 text-gray-900 hover:text-blue-600">
                            <i class="fas fa-home mr-1"></i> Dashboard
                        </a>
                        <a href="activities.php" class="inline-flex items-center px-1 pt-1 text-gray-900 hover:text-blue-600">
                            <i class="fas fa-calendar-alt mr-1"></i> Activities
                        </a>
                        <a href="reports.php" class="inline-flex items-center px-1 pt-1 text-gray-900 hover:text-blue-600">
                            <i class="fas fa-chart-bar mr-1"></i> Reports
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if(isset($_SESSION['user_id'])): ?>
                <div class="flex items-center">
                    <div class="ml-3 relative">
                        <a href="process_logout.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
