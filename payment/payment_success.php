<?php
// payment/payment_success.php
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

// Get plan details
$planId = filter_input(INPUT_GET, 'plan_id', FILTER_VALIDATE_INT);
if (!$planId) {
    header('Location: index.php');
    exit();
}

$plan = getPlanById($pdo, $planId);
if (!$plan) {
    header('Location: index.php?error=' . urlencode('Plan not found'));
    exit();
}

$userId = (int)$_SESSION['user_idusers'];
$userPlan = getCurrentUserPlan($pdo, $userId);

$cssPath = 'style.css?v=' . time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - World of Fitness</title>
    <link rel="stylesheet" href="<?= $cssPath ?>">
    <style>
        .success-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
        
        .success-title {
            font-family: 'Oswald', sans-serif;
            font-size: 2.5rem;
            color: #000;
            margin-bottom: 20px;
        }
        
        .plan-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 30px 0;
            text-align: left;
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #d10000, #a00000);
            color: white;
            border: none;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
            border: none;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <!-- World of Fitness Banner -->
    <div class="world-fitness-banner">
        <div class="banner-content">
            <h1><span class="world-text">WORLD OF </span><span class="fitness-text">FITNESS</span></h1>
        </div>
    </div>

    <div class="success-container">
        <div class="success-icon">✅</div>
        <h1 class="success-title">Payment Successful!</h1>
        <p style="font-size: 1.2rem; color: #666; margin-bottom: 30px;">
            Thank you for your purchase. Your training plan has been activated.
        </p>
        
        <div class="plan-details">
            <h3 style="margin-top: 0; color: #000;">Plan Details:</h3>
            <p><strong>Plan:</strong> <?= htmlspecialchars($plan['title']) ?></p>
            <p><strong>Amount Paid:</strong> <?= formatPrice($plan['price']) ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($plan['description']) ?></p>
            <?php if ($userPlan && $userPlan['expires_at']): ?>
                <p><strong>Expires:</strong> <?= date('F j, Y', strtotime($userPlan['expires_at'])) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="btn-group">
            <a href="../users page/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
            <a href="index.php" class="btn btn-secondary">View More Plans</a>
        </div>
    </div>
</body>
</html>