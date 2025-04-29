<?php
require_once 'config/database.php';
require_once 'includes/header.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get activity ID from query string
$activity_id = isset($_GET['activity_id']) ? intval($_GET['activity_id']) : 0;

// Fetch activity details
$activity_query = "SELECT * FROM activities WHERE id = ?";
$stmt = $conn->prepare($activity_query);
$stmt->bind_param("i", $activity_id);
$stmt->execute();
$activity_result = $stmt->get_result();
$activity = $activity_result->fetch_assoc();

if (!$activity) {
    echo "Activity not found.";
    exit();
}

$success_message = '';
$error_message = '';

// Handle attendance marking
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];

    // Insert attendance record
    $attendance_query = "INSERT INTO attendance_records (official_id, activity_id, status, remarks) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($attendance_query);
    $stmt->bind_param("iiss", $_SESSION['user_id'], $activity_id, $status, $remarks);

    if ($stmt->execute()) {
        $success_message = "Attendance marked successfully!";
    } else {
        $error_message = "Error marking attendance: " . $conn->error;
    }
    $stmt->close();
}
?>

<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Mark Attendance for <?php echo htmlspecialchars($activity['title']); ?></h1>

    <?php if($success_message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $success_message; ?></span>
        </div>
    <?php endif; ?>
    <?php if($error_message): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $error_message; ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Attendance Status</label>
            <select name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="present">Present</option>
                <option value="late">Late</option>
                <option value="absent">Absent</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Remarks</label>
            <textarea name="remarks" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        </div>
        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-md">
            Submit Attendance
        </button>
    </form>
</div>

<?php 
$conn->close();
require_once 'includes/footer.php'; 
?>
