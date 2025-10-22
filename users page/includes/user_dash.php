<?php
// user_dash.php
require_once __DIR__ . '/api/config_session.php'; // session start

if (empty($_SESSION['user_idusers'])) {
    header('Location: login/login.php');
    exit();
}

if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: admin_dash.php');
    exit();
}

require_once __DIR__ . '/api/dbh.php'; // provides $pdo
require_once __DIR__ . '/users page/model.php';
require_once __DIR__ . '/users page/contr.php';
require_once __DIR__ . '/payment page/includes/model.php';

$userId = (int) $_SESSION['user_idusers'];
$data = getUserDashboardData($pdo, $userId);

// render the dashboard view
require_once __DIR__ . '/view.php';
