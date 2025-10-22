<?php
// admin/admin.php - MAIN ENTRY POINT
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug information
error_log("=== ADMIN DASHBOARD ACCESS ===");
error_log("Session ID: " . session_id());
error_log("Session Data: " . print_r($_SESSION, true));

// Check if user is logged in
if (empty($_SESSION['user_idusers'])) {
    error_log("USER NOT LOGGED IN - Redirecting to login");
    header('Location: ../login/login.php');
    exit();
}

// Check if user has admin role
$userRole = $_SESSION['role'] ?? '';
if ($userRole !== 'admin') {
    error_log("USER NOT ADMIN - Role: '$userRole', Redirecting to user dashboard");
    header('Location: ../users page/dashboard.php');
    exit();
}

error_log("ADMIN ACCESS GRANTED - User ID: " . $_SESSION['user_idusers'] . ", Role: " . $userRole);

// Include database and MVC components with correct paths
require_once '../api/dbh.php';
require_once 'includes/model.php';
require_once 'includes/contr.php';

try {
    $data = getAdminDashboardData($pdo);
    error_log("Data loaded successfully - Users: " . count($data['users']) . ", Classes: " . count($data['enrollments']));
} catch (Exception $e) {
    error_log("Error loading dashboard data: " . $e->getMessage());
    $data = [
        'users' => [],
        'enrollments' => [],
        'totalUsers' => 0,
        'totalEnrollments' => 0
    ];
}

// Include the view
require_once 'includes/view.php';
?>