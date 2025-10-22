<?php
// model.php - Model
declare(strict_types=1);

function fetchUserById(object $pdo, int $userId): ?array {
    $sql = "SELECT idusers, firstName, surname, numberPhone, idNumber, email 
            FROM users WHERE idusers = :id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function fetchUserClasses(object $pdo, int $userId): array {
    $sql = "SELECT c.* 
            FROM classes c
            JOIN class_enrollments e ON e.class_id = c.id
            WHERE e.user_id = :uid
            ORDER BY FIELD(c.day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), c.start_time";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function fetchUserEvents(object $pdo, int $userId): array {
    $sql = "SELECT c.id, c.title, c.day_of_week, c.start_time, c.end_time, c.room
            FROM classes c
            JOIN class_enrollments e ON e.class_id = c.id
            WHERE e.user_id = :uid
            ORDER BY 
                FIELD(c.day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'),
                c.start_time ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function fetchWorkoutRecommendations(object $pdo, int $userId): array {
    $sql = "SELECT id, title, summary FROM recommendations 
            WHERE user_id = :uid OR user_id IS NULL 
            ORDER BY id DESC LIMIT 10";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function fetchUserPlan(object $pdo, int $userId): ?array {
    $sql = "SELECT up.id as user_plan_id, p.id as plan_id, p.title, p.price, p.description, up.expires_at
            FROM user_plans up
            JOIN plans p ON p.id = up.plan_id
            WHERE up.user_id = :uid
            ORDER BY up.purchased_at DESC
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function fetchPaidClassIdsByUser(object $pdo, int $userId): array {
    $sql = "SELECT class_id FROM payments WHERE user_id = :uid AND class_id IS NOT NULL";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

function fetchPaymentsByUser(object $pdo, int $userId): array {
    $sql = "SELECT p.*, pl.title as plan_title, c.title as class_title 
            FROM payments p 
            LEFT JOIN plans pl ON p.plan_id = pl.id 
            LEFT JOIN classes c ON p.class_id = c.id 
            WHERE p.user_id = :uid 
            ORDER BY p.paid_at DESC 
            LIMIT 10";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

// NEW FUNCTION: Get all available classes
function fetchAllClasses(object $pdo): array {
    $sql = "SELECT * FROM classes 
            ORDER BY FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), start_time";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

// NEW FUNCTION: Enroll user in a class
function enrollUserInClass(object $pdo, int $userId, int $classId): bool {
    try {
        // Check if already enrolled
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

// NEW FUNCTION: Auto-enroll based on plan
function autoEnrollBasedOnPlan(object $pdo, int $userId, string $planTitle): void {
    try {
        // Map plan titles to class patterns
        $classPatterns = [
            'Body Building Training' => ['Body Building Training'],
            'Weight Loss Training' => ['Weight Loss Training'],
            'Strength Training' => ['Strength Training']
        ];

        if (isset($classPatterns[$planTitle])) {
            $placeholders = str_repeat('?,', count($classPatterns[$planTitle]) - 1) . '?';
            $sql = "SELECT id FROM classes WHERE title IN ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($classPatterns[$planTitle]);
            $matchingClasses = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

            foreach ($matchingClasses as $classId) {
                enrollUserInClass($pdo, $userId, (int)$classId);
            }
        }
    } catch (Exception $e) {
        error_log("Auto-enrollment error: " . $e->getMessage());
    }
}
?>