<?php
// payment/index.php
declare(strict_types=1);

require_once __DIR__ . '/../api/config_session.php';
require_once __DIR__ . '/../api/dbh.php';
require_once __DIR__ . '/includes/model.php';
require_once __DIR__ . '/includes/contr.php';

// Authentication check
if (empty($_SESSION['user_idusers'])) {
    header('Location: ../login/login.php');
    exit();
}

// Get available plans and user data
$plans = getAllPlans($pdo);
$userPlan = getCurrentUserPlan($pdo, (int)$_SESSION['user_idusers']);
$userPayments = getPaymentHistoryByUser($pdo, (int)$_SESSION['user_idusers']); // Updated function name

// CSS path
$cssPath = 'style.css?v=' . time();

// Load view
require_once __DIR__ . '/includes/view.php';
?>