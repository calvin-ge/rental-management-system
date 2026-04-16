<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bike_shop";


// to Create connection
$db = new mysqli($servername, $username, $password, $dbname);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
//echo "Connected successfully";

// session start for user login
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}


//to check if useer is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}


//to get thr current user role
function getUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

// ask login to access page
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../login.php");
        exit();
    }
}

// Request admin access
function requireAdmin() {
    requireLogin();
    if (getUserRole() !== 'admin') {
        header("Location: ../user/dashboard.php");
        exit();
    }
}

// calculate late fee
function calculateLateFee($expectedDate, $actualDate, $dailyPrice) {
    $expected = new DateTime($expectedDate);
    $actual = new DateTime($actualDate);
    
    if ($actual <= $expected) {
        return ['days_late' => 0, 'fee' => 0];
    }
    
    $diff = $expected->diff($actual);
    $daysLate = $diff->days;
    $fee = $daysLate * ($dailyPrice * 0.30);
    
    return ['days_late' => $daysLate, 'fee' => $fee];
}
?>