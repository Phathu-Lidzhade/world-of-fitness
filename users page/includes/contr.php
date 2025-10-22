<?php
// contr.php - Controller
declare(strict_types=1);

require_once __DIR__ . '/model.php';

function getUserDashboardData(object $pdo, int $userId): array {
    $user = fetchUserById($pdo, $userId);
    if (!$user) {
        return ['error' => 'User not found'];
    }

    $classes = fetchUserClasses($pdo, $userId);
    $events = fetchUserEvents($pdo, $userId);
    $recs = fetchWorkoutRecommendations($pdo, $userId);
    $plan = fetchUserPlan($pdo, $userId);
    
    // Auto-enroll user in classes if they have a plan but no classes
    if ($plan && empty($classes)) {
        autoEnrollBasedOnPlan($pdo, $userId, $plan['title']);
        // Refresh the classes data
        $classes = fetchUserClasses($pdo, $userId);
        $events = fetchUserEvents($pdo, $userId);
    }

    // Use the function from model.php
    $payments = fetchPaymentsByUser($pdo, $userId);
    $paidClassIds = fetchPaidClassIdsByUser($pdo, $userId);

    // Get all available classes for display
    $allClasses = fetchAllClasses($pdo);

    return [
        'user' => $user,
        'classes' => $classes,
        'events' => $events,
        'recs' => $recs,
        'plan' => $plan,
        'payments' => $payments,
        'paidClassIds' => $paidClassIds,
        'allClasses' => $allClasses // Add all classes to data
    ];
}
?>