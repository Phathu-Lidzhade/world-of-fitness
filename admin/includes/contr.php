<?php
// admin/includes/contr.php
declare(strict_types=1);

require_once __DIR__ . '/model.php';

function getAdminDashboardData(object $pdo): array {
    try {
        error_log("=== START getAdminDashboardData ===");
        
        // Get total counts first
        $totalUsers = getTotalUsersCount($pdo);
        $totalEnrollments = getTotalEnrollmentsCount($pdo);
        
        error_log("Counts from database - Total Users: $totalUsers, Total Enrollments: $totalEnrollments");
        
        // Get basic data
        $users = fetchAllUsers($pdo);
        $enrollments = fetchClassEnrollments($pdo);
        
        error_log("Arrays loaded - Users: " . count($users) . ", Enrollments: " . count($enrollments));
        
        // Add participant details to each enrollment
        foreach ($enrollments as &$enrollment) {
            $enrollment['participants'] = fetchClassParticipants($pdo, (int)$enrollment['id']);
        }
        
        // Add enrolled classes to each user
        foreach ($users as &$user) {
            $user['enrolled_classes'] = fetchUserClasses($pdo, (int)$user['idusers']);
        }
        
        error_log("=== END getAdminDashboardData ===");
        
        return [
            'users' => $users,
            'enrollments' => $enrollments,
            'totalUsers' => $totalUsers,
            'totalEnrollments' => $totalEnrollments
        ];
    } catch (Exception $e) {
        error_log("Error in getAdminDashboardData: " . $e->getMessage());
        return [
            'users' => [],
            'enrollments' => [],
            'totalUsers' => 0,
            'totalEnrollments' => 0
        ];
    }
}
?>