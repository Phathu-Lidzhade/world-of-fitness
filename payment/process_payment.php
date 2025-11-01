<?php
// payment/process_payment.php
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// Validate plan ID
$planId = filter_input(INPUT_POST, 'plan_id', FILTER_VALIDATE_INT);
if (!$planId) {
    header('Location: index.php?error=' . urlencode('Invalid plan selected'));
    exit();
}

$userId = (int)$_SESSION['user_idusers'];

// Process the payment
$result = processPlanPurchase($pdo, $userId, $planId);

if ($result['success']) {
    header('Location: payment_success.php?plan_id=' . $planId);
} else {
    header('Location: index.php?error=' . urlencode($result['message']));
}
exit();
?>