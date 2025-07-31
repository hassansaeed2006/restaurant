<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Email and password are required'
        ]);
        exit;
    }

    try {
        // Prepare and execute the query
        $stmt = mysqli_prepare($conn, "SELECT id, email, password_hash FROM users WHERE email = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, "s", $email);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Database error: ' . mysqli_stmt_error($stmt));
        }
        
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$user) {
            echo json_encode([
                'status' => 'error',
                'message' => 'No user found with that email'
            ]);
            exit;
        }

        if (!password_verify($password, $user['password_hash'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Incorrect password'
            ]);
            exit;
        }

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['logged_in'] = true;
        $_SESSION['email'] = $user['email'];

        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful',
            'redirect' => 'introduction.php'
        ]);
    } catch (Exception $e) {
        error_log('Login error: ' . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'An error occurred during login. Please try again.'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>
