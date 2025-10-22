<?php
// test_admin_dashboard.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Bypass authentication for testing only
session_start();
$_SESSION['user_idusers'] = 1;
$_SESSION['role'] = 'admin';

echo "<h2>Testing Admin Dashboard - Bypassing Auth</h2>";

require_once 'api/dbh.php';
require_once 'admin/includes/model.php';
require_once 'admin/includes/contr.php';

try {
    $data = getAdminDashboardData($pdo);
    
    echo "<h3>Data Retrieved:</h3>";
    echo "<pre>";
    echo "Total Users: " . $data['totalUsers'] . "\n";
    echo "Users Loaded: " . count($data['users']) . "\n";
    echo "Total Enrollments: " . $data['totalEnrollments'] . "\n";
    echo "Classes Loaded: " . count($data['enrollments']) . "\n";
    echo "</pre>";
    
    // Show first user as sample
    if (!empty($data['users'])) {
        echo "<h3>First User Sample:</h3>";
        echo "<pre>";
        print_r($data['users'][0]);
        echo "</pre>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Now load the actual dashboard
require_once 'admin/admin.php';
?>