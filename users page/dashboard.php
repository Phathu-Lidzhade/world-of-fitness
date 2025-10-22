<?php
// dashboard.php - Controller
declare(strict_types=1);

// Use absolute paths
$rootDir = dirname(__DIR__);
require_once $rootDir . '/api/config_session.php';
require_once $rootDir . '/api/dbh.php';
require_once $rootDir . '/users page/includes/model.php';
require_once $rootDir . '/users page/includes/contr.php';

// Authentication check
if (empty($_SESSION['user_idusers'])) {
    header('Location: login/login.php');
    exit();
}

// Redirect admin users
if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: admin_dash.php');
    exit();
}

// Get user data through controller
$userId = (int) $_SESSION['user_idusers'];
$data = getUserDashboardData($pdo, $userId);

// Handle errors
if (isset($data['error'])) {
    header('HTTP/1.1 404 Not Found');
    echo "User not found";
    exit();
}

// Define CSS path with cache busting
$cssPath = 'style.css?v=' . time();

// Load view
require_once $rootDir . '/users page/includes/view.php';
?>