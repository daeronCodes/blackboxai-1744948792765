<?php
require_once 'config/database.php';
require_once 'includes/header.php';

// Initialize error and success messages
$error = '';
$success = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['signup'])) {
        // Sign up logic
        $username = trim($_POST['signup_username']);
        $password = $_POST['signup_password'];
        $email = trim($_POST['signup_email']);

        if (empty($username) || empty($password) || empty($email)) {
            $error = "Please fill in all fields.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $email);

            if ($stmt->execute()) {
                $success = "Account created successfully! You can now log in.";
            } else {
                $error = "Error creating account: " . $conn->error;
            }
            $stmt->close();
        }
    } elseif (isset($_POST['login'])) {
        // Login logic
        $username = trim($_POST['login_username']);
        $password = $_POST['login_password'];

        if (empty($username) || empty($password)) {
            $error = "Please fill in all fields.";
        } else {
            $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Invalid username or password.";
                }
            } else {
                $error = "Invalid username or password.";
            }
            $stmt->close();
        }
    }
}
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Welcome!</h2>
            <p class="mt-2 text-center text-sm text-gray-600">Sign up or log in to your account</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>

        <!-- Sign Up Form -->
        <form class="space-y-6" method="POST">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Sign Up</h3>
                <label for="signup_username" class="sr-only">Username</label>
                <input id="signup_username" name="signup_username" type="text" required 
                    class="appearance-none rounded-md border border-gray-300 placeholder-gray-500 text-gray-900 w-full px-3 py-2" 
                    placeholder="Username">
            </div>
            <div>
                <label for="signup_email" class="sr-only">Email</label>
                <input id="signup_email" name="signup_email" type="email" required 
                    class="appearance-none rounded-md border border-gray-300 placeholder-gray-500 text-gray-900 w-full px-3 py-2" 
                    placeholder="Email">
            </div>
            <div>
                <label for="signup_password" class="sr-only">Password</label>
                <input id="signup_password" name="signup_password" type="password" required 
                    class="appearance-none rounded-md border border-gray-300 placeholder-gray-500 text-gray-900 w-full px-3 py-2" 
                    placeholder="Password">
            </div>
            <button type="submit" name="signup" 
                class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                Sign Up
            </button>
        </form>

        <!-- Login Form -->
        <form class="space-y-6" method="POST">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Log In</h3>
                <label for="login_username" class="sr-only">Username</label>
                <input id="login_username" name="login_username" type="text" required 
                    class="appearance-none rounded-md border border-gray-300 placeholder-gray-500 text-gray-900 w-full px-3 py-2" 
                    placeholder="Username">
            </div>
            <div>
                <label for="login_password" class="sr-only">Password</label>
                <input id="login_password" name="login_password" type="password" required 
                    class="appearance-none rounded-md border border-gray-300 placeholder-gray-500 text-gray-900 w-full px-3 py-2" 
                    placeholder="Password">
            </div>
            <button type="submit" name="login" 
                class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                Log In
            </button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
