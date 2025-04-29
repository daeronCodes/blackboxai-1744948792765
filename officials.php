<?php
require_once 'config/database.php';
require_once 'includes/header.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$success_message = '';
$error_message = '';

// Handle form submission for new official
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $position = $_POST['position'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];

    if (empty($first_name) || empty($last_name) || empty($position) || empty($contact_number) || empty($email)) {
        $error_message = "Please fill in all required fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO officials (first_name, last_name, position, contact_number, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $first_name, $last_name, $position, $contact_number, $email);

        if ($stmt->execute()) {
            $success_message = "Official added successfully!";
        } else {
            $error_message = "Error adding official: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch all officials
$officials_query = "SELECT * FROM officials ORDER BY last_name ASC";
$officials_result = $conn->query($officials_query);
?>

<div class="container mx-auto px-4">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Officials Management</h1>
        <button onclick="document.getElementById('addOfficialModal').classList.remove('hidden')" 
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i> Add New Official
        </button>
    </div>

    <!-- Success/Error Messages -->
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

    <!-- Officials List -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
            <?php if($officials_result->num_rows > 0): ?>
                <?php while($official = $officials_result->fetch_assoc()): ?>
                    <div class="bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($official['first_name'] . ' ' . $official['last_name']); ?></h3>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($official['position']); ?></p>
                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($official['email']); ?></p>
                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($official['contact_number']); ?></p>
                            <div class="mt-4 flex justify-end space-x-2">
                                <button onclick="editOfficial(<?php echo $official['id']; ?>)" 
                                        class="inline-flex items-center px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-3 text-center py-8 text-gray-600">
                    No officials found. Click "Add New Official" to create one.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Official Modal -->
    <div id="addOfficialModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Add New Official</h3>
                <button onclick="document.getElementById('addOfficialModal').classList.add('hidden')" 
                        class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" name="first_name" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" name="last_name" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Position</label>
                    <input type="text" name="position" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                    <input type="text" name="contact_number" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" 
                            onclick="document.getElementById('addOfficialModal').classList.add('hidden')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-md">
                        Add Official
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editOfficial(officialId) {
    // Implement edit functionality
    alert('Edit functionality will be implemented here');
}
</script>

<?php 
$conn->close();
require_once 'includes/footer.php'; 
?>
