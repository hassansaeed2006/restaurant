<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$dbname = 'b_laban';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

// First check if we can connect to MySQL
$test_conn = @mysqli_connect($host, $username, $password);
if (!$test_conn) {
    throw new Exception("Could not connect to MySQL server. Please check if XAMPP is running.");
}

// Check if database exists
if (!mysqli_select_db($test_conn, $dbname)) {
    throw new Exception("Database 'b_laban' does not exist. Please create it first.");
}
mysqli_close($test_conn);

// Now try PDO connection
$pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $username,
    $password,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]
);

// Verify connection works
$pdo->query("SELECT 1");

// If we get here, connection is working
?>