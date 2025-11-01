<?php
// payment/includes/contr.php
declare(strict_types=1);

require_once __DIR__ . '/model.php';

function processPlanPurchase(object $pdo, int $userId, int $planId): array {
    // Validate plan exists
    $plan = getPlanById($pdo, $planId);
    if (!$plan) {
        return ['success' => false, 'message' => 'Invalid plan selected'];
    }

    // Process payment - use the renamed function
    $success = createPaymentRecord($pdo, $userId, $planId, (float)$plan['price']);

    if ($success) {
        return [
            'success' => true, 
            'message' => 'Payment processed successfully! Your training plan has been activated.',
            'plan' => $plan
        ];
    } else {
        return ['success' => false, 'message' => 'Payment failed. Please try again.'];
    }
}

// Updated to accept both string and float
function formatPrice($price): string {
    // Convert to float if it's a string
    $priceValue = is_string($price) ? (float)$price : $price;
    return 'R' . number_format($priceValue, 2);
}

// Generate features based on plan title
function getPlanFeatures(string $planTitle): array {
    $features = [];
    
    switch ($planTitle) {
        case 'Body Building Training':
            $features = [
                'Strength and hypertrophy program',
                'Monthly access',
                'Professional guidance',
                'Progress tracking'
            ];
            break;
        case 'Weight Loss Training':
            $features = [
                'Cardio and conditioning program',
                'Monthly access', 
                'Nutrition guidance',
                'Progress tracking'
            ];
            break;
        case 'Strength Training':
            $features = [
                'Heavy strength program',
                'Monthly access',
                'Professional guidance',
                'Advanced techniques'
            ];
            break;
        default:
            $features = ['Monthly training program', 'Professional guidance'];
    }
    
    return $features;
}
?>