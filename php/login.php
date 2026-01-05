<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'dbConnection.php';

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $db = new Database();
        $conn = $db->getConnection();

        // Get user by username first
        $stmt = $conn->prepare("SELECT userID, username, role, password FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        $user = null;
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
        }

        if ($user && password_verify($password, $user['password'])) {
            // Check Access Permission (Deny Guard)
            $allowedRoles = ['SSEDMMO Admin', 'SSEDMMO Staff'];
            if (!in_array($user['role'], $allowedRoles)) {
                $error = "Access Denied: You do not have permission to access the web dashboard.";

                // Log failed login attempt due to restriction
                $logStmt = $conn->prepare("INSERT INTO accesslog (userID, action, description) VALUES (?, 'failed_login', 'Access denied for role: " . $user['role'] . "')");
                $logStmt->bind_param("s", $user['userID']);
                $logStmt->execute();
            } else {
                $_SESSION['user_id'] = $user['userID'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Log successful login
                $logStmt = $conn->prepare("INSERT INTO accesslog (userID, action, description) VALUES (?, 'login', 'User logged in successfully')");
                $logStmt->bind_param("s", $user['userID']);
                $logStmt->execute();

                header("Location: dashboard.php");
                exit;
            }
        } else {
            // Log failed login attempt (Unified for wrong password or non-existent user)
            $logStmt = $conn->prepare("INSERT INTO accesslog (userID, action, description) VALUES (NULL, 'failed_login', ?)");
            $failedDesc = "Failed login attempt for username: " . $username;
            $logStmt->bind_param("s", $failedDesc);
            $logStmt->execute();

            $error = "Invalid username or password";
        }

        $db->closeConnection();
    } else {
        $error = "Please fill in all fields";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Vehicle Gate Pass</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">

                <h1><span class="highlight-blue">Vehicle Gate Pass</span></h1>
            </div>

            <form method="POST" class="login-form">
                <h2>Login</h2>

                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="show-password-btn" onclick="togglePassword()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>
    <script src="../js/login.js"></script>
</body>

</html>