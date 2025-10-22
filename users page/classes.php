<?php
// users-page/classes.php
declare(strict_types=1);

// Use absolute paths to avoid any issues
$rootDir = dirname(__DIR__);
require_once $rootDir . '/api/config_session.php';
require_once $rootDir . '/api/dbh.php';
require_once $rootDir . '/users page/includes/model.php';
require_once $rootDir . '/users page/includes/contr.php';

// Authentication check
if (empty($_SESSION['user_idusers'])) {
    header('Location: ../login/login.php');
    exit();
}

$userId = (int)$_SESSION['user_idusers'];

// Get user data
$user = fetchUserById($pdo, $userId);
if (!$user) {
    header('Location: ../login/login.php');
    exit();
}

// Get enrolled classes and all available classes
$userClasses = fetchUserClasses($pdo, $userId);
$allClasses = fetchAllClasses($pdo);
$userPlan = fetchUserPlan($pdo, $userId);

// Handle class enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_id'])) {
    $classId = (int)$_POST['class_id'];
    $success = enrollUserInClass($pdo, $userId, $classId);
    
    if ($success) {
        $_SESSION['success_message'] = 'Successfully enrolled in class!';
        // Refresh the classes data
        $userClasses = fetchUserClasses($pdo, $userId);
    } else {
        $_SESSION['error_message'] = 'Failed to enroll in class. You may already be enrolled.';
    }
    
    header('Location: classes.php');
    exit();
}

$cssPath = 'style.css?v=' . time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>World of Fitness - Available Classes</title>
    <link rel="stylesheet" href="<?= $cssPath ?>">
    <style>
        .classes-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .class-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        
        .class-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-color: #d10000;
        }
        
        .class-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .class-title {
            font-family: 'Oswald', sans-serif;
            font-size: 1.4rem;
            color: #000;
            margin: 0;
        }
        
        .class-day {
            background: #d10000;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .class-time {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        
        .class-room {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        
        .enroll-btn {
            background: linear-gradient(135deg, #d10000, #a00000);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .enroll-btn:hover {
            background: linear-gradient(135deg, #a00000, #800000);
            transform: translateY(-2px);
        }
        
        .enroll-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .already-enrolled {
            background: #28a745;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 6px;
            font-weight: 600;
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
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .user-info {
            background: #e8f5e8;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
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

    <div class="classes-container">
        <a href="dashboard.php" class="back-to-dashboard">← Back to Dashboard</a>
        
        <header style="text-align: center; margin-bottom: 30px;">
            <h1 style="font-family: 'Oswald', sans-serif; font-size: 2.5rem; color: #000;">
                Available Classes
            </h1>
            <p style="font-size: 1.2rem; color: #666;">
                Enroll in your preferred training sessions
            </p>
        </header>

        <div class="user-info">
            <h3 style="margin: 0; color: #28a745;">Welcome, <?= htmlspecialchars($user['firstName'] . ' ' . $user['surname']) ?></h3>
            <?php if ($userPlan): ?>
                <p style="margin: 5px 0 0 0; color: #666;">
                    Your Plan: <strong><?= htmlspecialchars($userPlan['title']) ?></strong>
                    <?php if ($userPlan['expires_at']): ?>
                        (Expires: <?= date('F j, Y', strtotime($userPlan['expires_at'])) ?>)
                    <?php endif; ?>
                </p>
            <?php else: ?>
                <p style="margin: 5px 0 0 0; color: #666;">
                    You don't have an active plan. <a href="../payment/index.php" style="color: #d10000;">Get a plan</a>
                </p>
            <?php endif; ?>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                ✅ <?= $_SESSION['success_message'] ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                ❌ <?= $_SESSION['error_message'] ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <?php if (!empty($userClasses)): ?>
            <div style="background: #e7f3ff; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <h3 style="margin: 0 0 10px 0; color: #004085;">Your Enrolled Classes (<?= count($userClasses) ?>)</h3>
                <?php foreach(array_slice($userClasses, 0, 3) as $class): ?>
                    <p style="margin: 5px 0; font-size: 14px;">
                        ✅ <strong><?= htmlspecialchars($class['title']) ?></strong> - 
                        <?= htmlspecialchars($class['day_of_week']) ?> 
                        <?= date('H:i', strtotime($class['start_time'])) ?>
                    </p>
                <?php endforeach; ?>
                <?php if (count($userClasses) > 3): ?>
                    <p style="margin: 5px 0; font-size: 12px; color: #666;">
                        ... and <?= count($userClasses) - 3 ?> more classes
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="classes-grid">
            <?php 
            $enrolledClassIds = array_column($userClasses, 'id');
            foreach ($allClasses as $class): 
                $isEnrolled = in_array($class['id'], $enrolledClassIds);
            ?>
                <div class="class-card">
                    <div class="class-header">
                        <h3 class="class-title"><?= htmlspecialchars($class['title']) ?></h3>
                        <span class="class-day"><?= htmlspecialchars($class['day_of_week']) ?></span>
                    </div>
                    
                    <div class="class-time">
                        ⏰ <?= date('H:i', strtotime($class['start_time'])) ?> - <?= date('H:i', strtotime($class['end_time'])) ?>
                    </div>
                    
                    <?php if (!empty($class['room'])): ?>
                        <div class="class-room">
                            📍 <?= htmlspecialchars($class['room']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($isEnrolled): ?>
                        <div class="already-enrolled">
                            ✅ Already Enrolled
                        </div>
                    <?php else: ?>
                        <form method="POST" action="classes.php">
                            <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                            <button type="submit" class="enroll-btn">
                                Enroll in Class
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($allClasses)): ?>
            <div style="text-align: center; padding: 40px;">
                <h3>No classes available at the moment.</h3>
                <p>Please check back later or contact the gym for schedule information.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>