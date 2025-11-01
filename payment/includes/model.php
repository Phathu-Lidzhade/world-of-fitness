<?php
// payment/includes/model.php
declare(strict_types=1);

function getAllPlans(object $pdo): array {
    $sql = "SELECT id, title, price, description 
            FROM plans 
            ORDER BY price ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function getPlanById(object $pdo, int $planId): ?array {
    $sql = "SELECT id, title, price, description 
            FROM plans 
            WHERE id = :id 
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $planId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function getCurrentUserPlan(object $pdo, int $userId): ?array {
    $sql = "SELECT up.*, p.title, p.description 
            FROM user_plans up 
            JOIN plans p ON up.plan_id = p.id 
            WHERE up.user_id = :user_id 
            AND (up.expires_at IS NULL OR up.expires_at > NOW()) 
            ORDER BY up.purchased_at DESC 
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function createPaymentRecord(object $pdo, int $userId, int $planId, float $amount): bool {
    try {
        $pdo->beginTransaction();

        // Create payment record
        $sql = "INSERT INTO payments (user_id, plan_id, amount, paid_at) 
                VALUES (:user_id, :plan_id, :amount, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':plan_id', $planId, PDO::PARAM_INT);
        $stmt->bindValue(':amount', $amount);
        $stmt->execute();

        $paymentId = $pdo->lastInsertId();

        // Set expiration date (30 days from now)
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

        // Check if user already has a plan
        $existingPlan = getCurrentUserPlan($pdo, $userId);
        
        if ($existingPlan) {
            // Update existing plan
            $sql = "UPDATE user_plans 
                    SET plan_id = :plan_id, 
                        purchased_at = NOW(), 
                        expires_at = :expires_at 
                    WHERE user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':plan_id', $planId, PDO::PARAM_INT);
            $stmt->bindValue(':expires_at', $expiresAt);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        } else {
            // Create new user plan
            $sql = "INSERT INTO user_plans (user_id, plan_id, purchased_at, expires_at) 
                    VALUES (:user_id, :plan_id, NOW(), :expires_at)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':plan_id', $planId, PDO::PARAM_INT);
            $stmt->bindValue(':expires_at', $expiresAt);
        }
        
        $stmt->execute();

        // AUTO-ENROLL USER IN CLASSES BASED ON THEIR PLAN
        autoEnrollUserInClasses($pdo, $userId, $planId);

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Payment error: " . $e->getMessage());
        return false;
    }
}

// NEW FUNCTION: Auto-enroll user in classes based on their plan
function autoEnrollUserInClasses(object $pdo, int $userId, int $planId): void {
    try {
        // Get plan title to match with classes
        $plan = getPlanById($pdo, $planId);
        if (!$plan) return;

        $planTitle = $plan['title'];
        
        // Map plan titles to class patterns
        $classPatterns = [
            'Body Building Training' => ['Body Building Training'],
            'Weight Loss Training' => ['Weight Loss Training'],
            'Strength Training' => ['Strength Training']
        ];

        // Get matching classes
        if (isset($classPatterns[$planTitle])) {
            $placeholders = str_repeat('?,', count($classPatterns[$planTitle]) - 1) . '?';
            $sql = "SELECT id FROM classes WHERE title IN ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($classPatterns[$planTitle]);
            $matchingClasses = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

            // Enroll user in matching classes
            foreach ($matchingClasses as $classId) {
                enrollUserInClass($pdo, $userId, (int)$classId);
            }
        }
    } catch (Exception $e) {
        error_log("Auto-enrollment error: " . $e->getMessage());
    }
}

// Renamed to avoid conflict with users-page function
function getPaymentHistoryByUser(object $pdo, int $userId): array {
    $sql = "SELECT p.*, pl.title as plan_title, pl.description 
            FROM payments p 
            LEFT JOIN plans pl ON p.plan_id = pl.id 
            WHERE p.user_id = :user_id 
            ORDER BY p.paid_at DESC 
            LIMIT 10";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function enrollUserInClass(object $pdo, int $userId, int $classId): bool {
    try {
        // Check if already enrolled to avoid duplicates
        $checkSql = "SELECT id FROM class_enrollments WHERE user_id = :user_id AND class_id = :class_id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $checkStmt->bindValue(':class_id', $classId, PDO::PARAM_INT);
        $checkStmt->execute();
        
        if ($checkStmt->fetch()) {
            return true; // Already enrolled
        }

        $sql = "INSERT INTO class_enrollments (user_id, class_id, enrolled_at) 
                VALUES (:user_id, :class_id, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':class_id', $classId, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (Exception $e) {
        error_log("Class enrollment error: " . $e->getMessage());
        return false;
    }
}
?>