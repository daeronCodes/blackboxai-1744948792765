<?php
require_once 'config/database.php';
require_once 'includes/header.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$report_data = [];
$error_message = '';
$success_message = '';

// Fetch all officials for the dropdown
$officials_query = "SELECT * FROM officials ORDER BY last_name ASC";
$officials_result = $conn->query($officials_query);

// Handle report generation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $official_id = isset($_POST['official_id']) ? $_POST['official_id'] : '';
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

    if (empty($official_id) || empty($start_date) || empty($end_date)) {
        $error_message = "Please fill in all required fields.";
    } else {
        // Query to get attendance records with activity and official details
        $report_query = "SELECT 
            ar.*, 
            a.title as activity_title, 
            a.date as activity_date,
            a.start_time,
            a.end_time,
            a.venue,
            o.first_name,
            o.last_name,
            o.position
        FROM attendance_records ar
        JOIN activities a ON ar.activity_id = a.id
        JOIN officials o ON ar.official_id = o.id
        WHERE ar.official_id = ? 
        AND a.date BETWEEN ? AND ?
        ORDER BY a.date DESC, a.start_time ASC";

        $stmt = $conn->prepare($report_query);
        $stmt->bind_param("iss", $official_id, $start_date, $end_date);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $report_data[] = $row;
                }
                $success_message = "Report generated successfully!";
            } else {
                $error_message = "No attendance records found for the selected period.";
            }
        } else {
            $error_message = "Error generating report: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Attendance Reports</h1>

        <!-- Report Generation Form -->
        <form method="POST" class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Official</label>
                <select name="official_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Select Official --</option>
                    <?php while($official = $officials_result->fetch_assoc()): ?>
                        <option value="<?php echo $official['id']; ?>">
                            <?php echo htmlspecialchars($official['first_name'] . ' ' . $official['last_name'] . ' (' . $official['position'] . ')'); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" required 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" required 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="md:col-span-3">
                <button type="submit" class="w-full md:w-auto px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Generate Report
                </button>
            </div>
        </form>

        <!-- Error/Success Messages -->
        <?php if($error_message): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if($success_message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <!-- Report Results -->
        <?php if (!empty($report_data)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                            <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Venue</th>
                            <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In</th>
                            <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($report_data as $record): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($record['activity_title']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo date('F d, Y', strtotime($record['activity_date'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php 
                                    echo date('h:i A', strtotime($record['start_time'])) . ' - ' . 
                                         date('h:i A', strtotime($record['end_time'])); 
                                    ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($record['venue']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php echo $record['status'] === 'present' ? 'bg-green-100 text-green-800' : 
                                            ($record['status'] === 'late' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                        <?php echo ucfirst($record['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo date('h:i A', strtotime($record['time_in'])); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <?php echo htmlspecialchars($record['remarks'] ?? ''); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$conn->close();
require_once 'includes/footer.php'; 
?>
