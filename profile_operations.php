<?php
session_start();
require_once 'db_connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Get the action from the request
$action = $_GET['action'] ?? '';

// If no action is specified, display the profile page
if (empty($action)) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.html');
        exit();
    }

    // Get user data from database
    $stmt = mysqli_prepare($conn, "SELECT username, email FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$user) {
        header('Location: login.html');
        exit();
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profile Page</title>
        <link rel="stylesheet" href="profile.css">
    </head>
    <body>
        <a href="introduction.php"><img src="Photos/بلبن.png" alt="b-laban" id="b-laban-logo"></a>
        <h2>Profile Page</h2>

        <div class="profile-info">
            <h3>Profile Information</h3>
            <div><strong>Name:</strong> <div id="profileName"><?php echo htmlspecialchars($user['username']); ?></div></div>
            <div><strong>Email:</strong> <div id="profileEmail"><?php echo htmlspecialchars($user['email']); ?></div></div>
        </div>

        <div>
            <button onclick="signOut()">Sign Out</button>
        </div>
        <script src="profile.js"></script>
    </body>
    </html>
    <?php
    exit();
}

// For API operations, set JSON header
header('Content-Type: application/json');

switch ($action) {
    case 'check_session':
        echo json_encode([
            'logged_in' => isset($_SESSION['user_id']),
            'user_id' => $_SESSION['user_id'] ?? null
        ]);
        break;

    case 'get_profile':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            exit();
        }

        $stmt = mysqli_prepare($conn, "SELECT username, email FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($user) {
            echo json_encode([
                'success' => true,
                'user' => [
                    'username' => $user['username'],
                    'email' => $user['email']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
        break;

    case 'change_password':
        // Note: Password change functionality was removed from the front end,
        // but keeping this case in the backend for completeness or future use.
        
        // Log the request for debugging
        error_log('Password change request received');
        
        if (!isset($_SESSION['user_id'])) {
            error_log('Password change failed: Not logged in');
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            exit();
        }

        $input = file_get_contents('php://input');
        error_log('Received data: ' . $input);
        
        $data = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('JSON decode error: ' . json_last_error_msg());
            echo json_encode(['success' => false, 'message' => 'Invalid request data']);
            exit();
        }

        if (!isset($data['currentPassword']) || !isset($data['newPassword']) || !isset($data['confirmPassword'])) {
            error_log('Password change failed: Missing required fields');
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit();
        }

        if ($data['newPassword'] !== $data['confirmPassword']) {
            error_log('Password change failed: Passwords do not match');
            echo json_encode(['success' => false, 'message' => 'New passwords do not match']);
            exit();
        }

        // Verify current password
        $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE id = ?");
        if (!$stmt) {
            error_log('Database error: ' . mysqli_error($conn));
            echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
            exit();
        }

        mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
        if (!mysqli_stmt_execute($stmt)) {
            error_log('Database error: ' . mysqli_stmt_error($stmt));
            echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_stmt_error($stmt)]);
            exit();
        }

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$user) {
            error_log('Password change failed: User not found');
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit();
        }

        if (!password_verify($data['currentPassword'], $user['password'])) {
            error_log('Password change failed: Current password incorrect');
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
            exit();
        }

        // Update password
        $hashedPassword = password_hash($data['newPassword'], PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
        if (!$stmt) {
            error_log('Database error: ' . mysqli_error($conn));
            echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
            exit();
        }

        mysqli_stmt_bind_param($stmt, "si", $hashedPassword, $_SESSION['user_id']);
        if (!mysqli_stmt_execute($stmt)) {
            error_log('Database error: ' . mysqli_stmt_error($stmt));
            echo json_encode(['success' => false, 'message' => 'Failed to update password: ' . mysqli_stmt_error($stmt)]);
            exit();
        }

        if (mysqli_affected_rows($conn) === 0) {
            error_log('Password change failed: No rows affected');
            echo json_encode(['success' => false, 'message' => 'No changes were made to the password']);
            exit();
        }

        error_log('Password change successful');
        mysqli_stmt_close($stmt);
        echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
        break;

    case 'logout':
        // Clear all session variables
        $_SESSION = array();
        // Destroy the session
        session_destroy();
        echo json_encode(['success' => true]);
        break;

    default:
        // For any other action or if action is not set (though handled by empty($action) above)
        // or if an error occurs before a case is matched, return a default error.
        // This helps ensure a JSON response even in unexpected scenarios.
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Invalid or missing action']);
        break;
}

// Add a check for any output before JSON headers are sent
// This can help catch errors or accidental output
if (headers_sent()) {
    error_log('Headers already sent. Possible output before JSON response.');
}

?> 