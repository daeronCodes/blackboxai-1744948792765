<?php
require_once 'config/database.php';
require_once 'includes/header.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch upcoming activities
$upcoming_query = "SELECT * FROM activities 
                  WHERE date >= CURDATE() 
                  AND status = 'upcoming' 
                  ORDER BY date, start_time 
                  LIMIT 5";
$upcoming_result = $conn->query($upcoming_query);

// Fetch recent attendance records
$recent_attendance_query = "SELECT ar.*, o.first_name, o.last_name, a.title 
                          FROM attendance_records ar
                          JOIN officials o ON ar.official_id = o.id
                          JOIN activities a ON ar.activity_id = a.id
                          ORDER BY ar.time_in DESC 
                          LIMIT 5";
$recent_attendance_result = $conn->query($recent_attendance_query);
?>

<div class="container mx-auto px-4">
    <!-- Welcome Section -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p class="text-gray-600">Here's your activity overview</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Upcoming Activities -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                Upcoming Activities
            </h2>
            <?php if($upcoming_result->num_rows > 0): ?>
                <div class="space-y-4">
                    <?php while($activity = $upcoming_result->fetch_assoc()): ?>
                        <div class="border-l-4 border-blue-500 pl-4 py-2">
                            <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($activity['title']); ?></h3>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-clock mr-1"></i>
                                <?php 
                                echo date('F d, Y', strtotime($activity['date'])) . ' | ' . 
                                     date('h:i A', strtotime($activity['start_time'])) . ' - ' . 
                                     date('h:i A', strtotime($activity['end_time'])); 
                                ?>
                            </p>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-location-dot mr-1"></i>
                                <?php echo htmlspecialchars($activity['venue']); ?>
                            </p>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-600">No upcoming activities.</p>
            <?php endif; ?>
        </div>

        <!-- Recent Attendance -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                <i class="fas fa-clipboard-check text-green-500 mr-2"></i>
                Recent Attendance
            </h2>
            <?php if($recent_attendance_result->num_rows > 0): ?>
                <div class="space-y-4">
                    <?php while($record = $recent_attendance_result->fetch_assoc()): ?>
                        <div class="border-l-4 border-green-500 pl-4 py-2">
                            <h3 class="font-semibold text-gray-800">
                                <?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?>
                            </h3>
                            <p class="text-sm text-gray-600">
                                <?php echo htmlspecialchars($record['title']); ?>
                            </p>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-clock mr-1"></i>
                                <?php echo date('F d, Y h:i A', strtotime($record['time_in'])); ?>
                            </p>
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full 
                                <?php echo $record['status'] === 'present' ? 'bg-green-100 text-green-800' : 
                                    ($record['status'] === 'late' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                <?php echo ucfirst($record['status']); ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-600">No recent attendance records.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-6 bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="activities.php" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <i class="fas fa-calendar-plus text-blue-500 text-2xl mr-3"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">Manage Activities</h3>
                    <p class="text-sm text-gray-600">Add or edit activities</p>
                </div>
            </a>
            <a href="officials.php" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <i class="fas fa-users text-green-500 text-2xl mr-3"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">Manage Officials</h3>
                    <p class="text-sm text-gray-600">View and edit officials</p>
                </div>
            </a>
            <a href="reports.php" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                <i class="fas fa-chart-bar text-purple-500 text-2xl mr-3"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">View Reports</h3>
                    <p class="text-sm text-gray-600">Generate attendance reports</p>
                </div>
            </a>
        </div>
    </div>
</div>

<?php 
$conn->close();
require_once 'includes/footer.php'; 
?>
