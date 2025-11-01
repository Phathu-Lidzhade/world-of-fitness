<?php
// payment/includes/view.php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>World of Fitness - Training Plans</title>
    <link rel="stylesheet" href="<?= $cssPath ?>">
    <style>
        .plans-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }
        
        .plan-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 3px solid #e0e0e0;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .plan-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            border-color: #d10000;
        }
        
        .plan-card.popular {
            border-color: #d10000;
            transform: scale(1.05);
        }
        
        .plan-card.popular::before {
            content: 'MOST POPULAR';
            position: absolute;
            top: 15px;
            right: -30px;
            background: #d10000;
            color: white;
            padding: 5px 30px;
            font-size: 12px;
            font-weight: bold;
            transform: rotate(45deg);
        }
        
        .plan-header {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .plan-title {
            font-family: 'Oswald', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: #000;
            margin-bottom: 10px;
        }
        
        .plan-price {
            font-family: 'Oswald', sans-serif;
            font-size: 36px;
            font-weight: 700;
            color: #d10000;
            margin-bottom: 5px;
        }
        
        .plan-duration {
            color: #666;
            font-size: 14px;
        }
        
        .plan-features {
            list-style: none;
            padding: 0;
            margin: 25px 0;
        }
        
        .plan-features li {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            position: relative;
            padding-left: 25px;
        }
        
        .plan-features li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #d10000;
            font-weight: bold;
        }
        
        .select-plan-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #d10000, #a00000);
            color: white;
            border: none;
            border-radius: 8px;
            font-family: 'Oswald', sans-serif;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .select-plan-btn:hover {
            background: linear-gradient(135deg, #a00000, #800000);
            transform: translateY(-2px);
        }
        
        .current-plan-badge {
            background: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }
        
        .payment-history {
            margin-top: 50px;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .back-to-dashboard {
            display: inline-block;
            margin-bottom: 20px;
            color: #d10000;
            text-decoration: none;
            font-weight: 600;
        }
        
        .back-to-dashboard:hover {
            text-decoration: underline;
        }

        /* World of Fitness Banner Styles */
        .world-fitness-banner {
            background: #000000;
            color: #ffffff;
            padding: 20px 0;
            text-align: center;
            border-bottom: 4px solid #d10000;
        }
        
        .banner-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .world-fitness-banner h1 {
            font-family: 'Oswald', Arial, sans-serif;
            font-size: 3rem;
            font-weight: 700;
            margin: 0;
        }
        
        .world-text {
            color: #ffffff;
        }
        
        .fitness-text {
            color: #d10000;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .plans-grid {
                grid-template-columns: 1fr;
            }
            
            .plan-card.popular {
                transform: none;
            }
            
            .world-fitness-banner h1 {
                font-size: 2rem;
            }
            
            .plan-price {
                font-size: 28px;
            }
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

    <div class="plans-container">
        <a href="../users page/dashboard.php" class="back-to-dashboard">← Back to Dashboard</a>
        
        <header style="text-align: center; margin-bottom: 40px;">
            <h1 style="font-family: 'Oswald', sans-serif; font-size: 2.5rem; color: #000;">
                Training Plans
            </h1>
            <p style="font-size: 1.2rem; color: #666;">
                Choose your specialized training program
            </p>
            
            <?php if ($userPlan): ?>
                <div style="background: #e8f5e8; padding: 15px; border-radius: 10px; margin-top: 20px;">
                    <h3 style="margin: 0; color: #28a745;">
                        ✅ Your Current Plan: <?= htmlspecialchars($userPlan['title']) ?>
                        <span class="current-plan-badge">ACTIVE</span>
                    </h3>
                    <p style="margin: 5px 0 0 0; color: #666;">
                        <?php if ($userPlan['expires_at']): ?>
                            Expires on: <?= date('F j, Y', strtotime($userPlan['expires_at'])) ?>
                        <?php else: ?>
                            No expiration date
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
        </header>

        <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #c3e6cb;">
                ✅ Payment processed successfully! Your training plan has been activated.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #f5c6cb;">
                ❌ Error: <?= htmlspecialchars(urldecode($_GET['error'])) ?>
            </div>
        <?php endif; ?>

        <div class="plans-grid">
            <?php foreach ($plans as $index => $plan): ?>
                <div class="plan-card <?= $index === 1 ? 'popular' : '' ?>">
                    <div class="plan-header">
                        <h3 class="plan-title"><?= htmlspecialchars($plan['title']) ?></h3>
                        <div class="plan-price"><?= formatPrice($plan['price']) ?></div>
                        <div class="plan-duration">per month</div>
                    </div>
                    
                    <p style="color: #666; text-align: center; margin-bottom: 20px;">
                        <?= htmlspecialchars($plan['description']) ?>
                    </p>
                    
                    <ul class="plan-features">
                        <?php 
                        $features = getPlanFeatures($plan['title']);
                        foreach ($features as $feature): 
                        ?>
                            <li><?= htmlspecialchars($feature) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <form action="process_payment.php" method="POST">
                        <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
                        <button type="submit" class="select-plan-btn">
                            Select Plan
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Payment History -->
        <div class="payment-history">
            <h2 style="font-family: 'Oswald', sans-serif; margin-bottom: 20px;">Payment History</h2>
            <?php if (empty($userPayments)): ?>
                <p>No payment history found.</p>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;">Date</th>
                            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;">Plan</th>
                            <th style="padding: 12px; text-align: right; border-bottom: 2px solid #dee2e6;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userPayments as $payment): ?>
                            <tr>
                                <td style="padding: 12px; border-bottom: 1px solid #dee2e6;">
                                    <?= date('M j, Y', strtotime($payment['paid_at'])) ?>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid #dee2e6;">
                                    <?= htmlspecialchars($payment['plan_title'] ?? 'N/A') ?>
                                </td>
                                <td style="padding: 12px; text-align: right; border-bottom: 1px solid #dee2e6;">
                                    <strong><?= formatPrice($payment['amount']) ?></strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>