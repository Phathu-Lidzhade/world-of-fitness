<?php
// admin/model.php
declare(strict_types=1);

function fetchAllUsers(object $pdo): array {
    try {
        $sql = "SELECT idusers, firstName, surname, email, numberPhone FROM users ORDER BY firstName, surname ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!$result) {
            error_log("No users found in database");
            return [];
        }
        
        error_log("Successfully fetched " . count($result) . " users from database");
        return $result;
    } catch (PDOException $e) {
        error_log("Error fetching users: " . $e->getMessage());
        return [];
    }
}

function fetchClassEnrollments(object $pdo): array {
    try {
        $sql = "SELECT c.id, c.title, c.day_of_week, c.start_time, c.end_time, c.room, COUNT(e.user_id) AS enrolled
                FROM classes c
                LEFT JOIN class_enrollments e ON e.class_id = c.id
                GROUP BY c.id, c.title, c.day_of_week, c.start_time, c.end_time, c.room
                ORDER BY FIELD(c.day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), c.start_time";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!$result) {
            error_log("No class enrollments found in database");
            return [];
        }
        
        error_log("Successfully fetched " . count($result) . " class enrollments from database");
        return $result;
    } catch (PDOException $e) {
        error_log("Error fetching class enrollments: " . $e->getMessage());
        return [];
    }
}

function fetchClassParticipants(object $pdo, int $classId): array {
    try {
        $sql = "SELECT u.idusers, u.firstName, u.surname, u.email, u.numberPhone
                FROM users u
                JOIN class_enrollments e ON e.user_id = u.idusers
                WHERE e.class_id = :cid
                ORDER BY u.firstName, u.surname";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':cid', $classId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: [];
    } catch (PDOException $e) {
        error_log("Error fetching class participants for class $classId: " . $e->getMessage());
        return [];
    }
}

function fetchUserClasses(object $pdo, int $userId): array {
    try {
        $sql = "SELECT c.title, c.day_of_week, c.start_time, c.end_time
                FROM classes c
                JOIN class_enrollments e ON e.class_id = c.id
                WHERE e.user_id = :uid
                ORDER BY c.day_of_week, c.start_time";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: [];
    } catch (PDOException $e) {
        error_log("Error fetching user classes for user $userId: " . $e->getMessage());
        return [];
    }
}

function getTotalUsersCount(object $pdo): int {
    try {
        $sql = "SELECT COUNT(*) as count FROM users";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['count'] ?? 0);
    } catch (PDOException $e) {
        error_log("Error getting total users count: " . $e->getMessage());
        return 0;
    }
}

function getTotalEnrollmentsCount(object $pdo): int {
    try {
        $sql = "SELECT COUNT(*) as count FROM class_enrollments";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['count'] ?? 0);
    } catch (PDOException $e) {
        error_log("Error getting total enrollments count: " . $e->getMessage());
        return 0;
    }
}
?>