<?php
session_start();
require_once 'db_connect.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get and sanitize inputs
    $username = trim($_POST['username'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($username) || strlen($username) < 3) {
        throw new Exception('Username must be at least 3 characters long');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    if (strlen($password) < 8) {
        throw new Exception('Password must be at least 8 characters long');
    }
    if ($password !== $confirmPassword) {
        throw new Exception('Passwords do not match');
    }

    // Check if email exists
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    if (!$stmt) {
        throw new Exception('Database error: ' . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "s", $email);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Database error: ' . mysqli_stmt_error($stmt));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_fetch_assoc($result)) {
        mysqli_stmt_close($stmt);
        throw new Exception('Email already registered');
    }
    mysqli_stmt_close($stmt);

    // Hash password and create user
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    if (!$stmt) {
        throw new Exception('Database error: ' . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $passwordHash);
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        throw new Exception('Failed to create account: ' . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);

    // Return success response
    echo json_encode([
        'status' => 'success',
        'message' => 'Account created successfully',
        'redirect' => 'login.html'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
