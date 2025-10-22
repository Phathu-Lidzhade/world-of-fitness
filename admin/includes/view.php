<?php
// admin/includes/view.php

// Extract data passed from controller
$users = $data['users'] ?? [];
$enrollments = $data['enrollments'] ?? [];
$totalUsers = $data['totalUsers'] ?? 0;
$totalEnrollments = $data['totalEnrollments'] ?? 0;

// Debug the data
error_log("View - Users count: " . count($users));
error_log("View - Enrollments count: " . count($enrollments));
error_log("View - Total Users: $totalUsers, Total Enrollments: $totalEnrollments");
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - World of Fitness</title>
  
  <!-- admin-specific styles -->
  <link rel="stylesheet" href="../style.css">
  
  <style>
    /* All your existing CSS styles here - keep them exactly as you had */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin: 30px 0;
    }
    
    .stat-card {
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      border-left: 4px solid #d10000;
      text-align: center;
    }
    
    .stat-number {
      font-family: 'Oswald', sans-serif;
      font-size: 2.5rem;
      font-weight: 700;
      color: #d10000;
      margin: 10px 0;
    }
    
    .stat-label {
      color: #666;
      font-size: 1rem;
      font-weight: 600;
    }
    
    .section-title {
      font-family: 'Oswald', sans-serif;
      font-size: 1.8rem;
      color: #000;
      margin: 30px 0 20px 0;
      border-bottom: 2px solid #d10000;
      padding-bottom: 10px;
    }
    
    .table-container {
      background: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      margin-bottom: 30px;
      overflow-x: auto;
    }
    
    .data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }
    
    .data-table th {
      background: #f8f9fa;
      padding: 15px 12px;
      text-align: left;
      font-weight: 600;
      color: #000;
      border-bottom: 2px solid #d10000;
      font-family: 'Oswald', sans-serif;
    }
    
    .data-table td {
      padding: 12px;
      border-bottom: 1px solid #e9ecef;
      vertical-align: top;
    }
    
    .data-table tr:hover {
      background: #f8f9fa;
    }
    
    .enrollment-count {
      background: #d10000;
      color: white;
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 600;
    }
    
    .no-data {
      text-align: center;
      padding: 40px;
      color: #666;
      font-style: italic;
    }
    
    .admin-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 2px solid #e9ecef;
    }
    
    .admin-actions {
      display: flex;
      gap: 10px;
    }
    
    .btn {
      padding: 10px 20px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
      font-family: 'Montserrat', sans-serif;
    }
    
    .btn-primary {
      background: linear-gradient(135deg, #d10000, #a00000);
      color: white;
    }
    
    .btn-secondary {
      background: #6c757d;
      color: white;
    }
    
    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    
    .user-avatar {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      background: #d10000;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      font-size: 14px;
      margin-right: 10px;
    }
    
    .user-info {
      display: flex;
      align-items: center;
    }
    
    .class-details {
      font-size: 12px;
      color: #666;
      margin-top: 5px;
    }

    /* World of Fitness Banner */
    .world-fitness-banner {
      background: #000000 !important;
      color: #ffffff;
      padding: 20px 0;
      text-align: center;
      border-bottom: 4px solid #d10000;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
      width: 100%;
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
      color: #d10000 !important;
      text-shadow: 0 0 10px rgba(209, 0, 0, 0.5);
    }

    body {
      background: #f8fafc;
      margin: 0;
      padding: 0;
      font-family: 'Montserrat', Arial, sans-serif;
    }

    .admin-wrap {
      max-width: 1200px;
      margin: 0 auto;
      padding: 30px 20px;
      min-height: calc(100vh - 120px);
    }
  </style>
</head>
<body>


  <!-- World of Fitness Banner - BLACK BACKGROUND WITH RED FITNESS -->
  <div class="world-fitness-banner" style="background: #000000 !important;">
    <div class="banner-content">
      <h1><span class="world-text" style="color: #ffffff;">WORLD OF </span><span class="fitness-text" style="color: #d10000 !important;">FITNESS</span></h1>
    </div>
  </div>

<div class="admin-wrap">
  <div class="admin-header">
    <h1 style="font-family: 'Oswald', sans-serif; font-size: 2.2rem; color: #000; margin: 0;">Admin Dashboard</h1>
    <div class="admin-actions">
      <a href="#" class="btn btn-secondary">Settings</a>

      <form class="logout" action="/GymWebsite-main/api/logout.php" method="post">
          <button class="btn btn-primary" type="submit" title="Logout">Logout</button>
      </form>

    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-label">Total Users</div>
      <div class="stat-number"><?= $totalUsers ?></div>
      <div style="font-size: 12px; color: #666; margin-top: 5px;">
        <?= count($users) ?> users loaded
      </div>
    </div>
    
    <div class="stat-card">
      <div class="stat-label">Total Enrollments</div>
      <div class="stat-number"><?= $totalEnrollments ?></div>
    </div>
    
    <div class="stat-card">
      <div class="stat-label">Active Classes</div>
      <div class="stat-number"><?= count($enrollments) ?></div>
    </div>
  </div>

  <!-- Debug Information -->
  <?php if (empty($users) || empty($enrollments)): ?>
    <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
      <h3 style="margin: 0 0 10px 0; color: #856404;">Debug Information</h3>
      <p style="margin: 5px 0; color: #856404;">
        Users in database: <?= $totalUsers ?><br>
        Users loaded: <?= count($users) ?><br>
        Enrollments in database: <?= $totalEnrollments ?><br>
        Classes loaded: <?= count($enrollments) ?>
      </p>
    </div>
  <?php endif; ?>

  <!-- Class Enrollments Section -->
  <h2 class="section-title">Class Enrollments</h2>
  <div class="table-container">
    <?php if (empty($enrollments)): ?>
      <div class="no-data">
        <h3>No class enrollment data available</h3>
        <p>There are either no classes in the database or no enrollments.</p>
      </div>
    <?php else: ?>
      <table class="data-table">
        <thead>
          <tr>
            <th>Class Name</th>
            <th>Day & Time</th>
            <th>Enrolled Users</th>
            <th>Participants</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($enrollments as $e): ?>
            <tr>
              <td>
                <strong><?= htmlspecialchars($e['title'] ?? 'Unknown Class') ?></strong>
                <?php if (!empty($e['room'])): ?>
                  <div class="class-details">📍 <?= htmlspecialchars($e['room']) ?></div>
                <?php endif; ?>
              </td>
              <td>
                <?= htmlspecialchars($e['day_of_week'] ?? 'Unknown Day') ?><br>
                <div class="class-details">
                  <?= date('H:i', strtotime($e['start_time'] ?? '00:00')) ?> - <?= date('H:i', strtotime($e['end_time'] ?? '00:00')) ?>
                </div>
              </td>
              <td>
                <span class="enrollment-count"><?= (int)($e['enrolled'] ?? 0) ?> users</span>
              </td>
              <td>
                <?php if (!empty($e['participants'])): ?>
                  <?php foreach(array_slice($e['participants'], 0, 3) as $participant): ?>
                    <div class="user-info" style="margin-bottom: 5px;">
                      <div class="user-avatar">
                        <?= strtoupper(substr($participant['firstName'] ?? 'U',0,1) . substr($participant['surname'] ?? 'U',0,1)) ?>
                      </div>
                      <div>
                        <?= htmlspecialchars(($participant['firstName'] ?? 'Unknown') . ' ' . ($participant['surname'] ?? 'User')) ?>
                        <div class="class-details"><?= htmlspecialchars($participant['email'] ?? 'No email') ?></div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                  <?php if (count($e['participants']) > 3): ?>
                    <div class="class-details" style="margin-top: 5px;">
                      + <?= count($e['participants']) - 3 ?> more participants
                    </div>
                  <?php endif; ?>
                <?php else: ?>
                  <span style="color: #666; font-style: italic;">No participants</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <!-- All Users Section -->
  <h2 class="section-title">All Users (<?= $totalUsers ?>)</h2>
  <div class="table-container">
    <?php if (empty($users)): ?>
      <div class="no-data">
        <h3>No users found in database</h3>
        <p>Check your database connection and ensure the users table exists with data.</p>
      </div>
    <?php else: ?>
      <table class="data-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Contact Information</th>
            <th>Enrolled Classes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($users as $u): ?>
            <tr>
              <td>
                <div class="user-info">
                  <div class="user-avatar">
                    <?= strtoupper(substr($u['firstName'] ?? 'U',0,1) . substr($u['surname'] ?? 'U',0,1)) ?>
                  </div>
                  <div>
                    <strong><?= htmlspecialchars(($u['firstName'] ?? 'Unknown') . ' ' . ($u['surname'] ?? 'User')) ?></strong>
                    <div class="class-details">ID: <?= $u['idusers'] ?? 'N/A' ?></div>
                  </div>
                </div>
              </td>
              <td>
                <div>
                  <strong>📧 <?= htmlspecialchars($u['email'] ?? 'No email') ?></strong><br>
                  <div class="class-details">📞 <?= htmlspecialchars($u['numberPhone'] ?? 'No phone') ?></div>
                </div>
              </td>
              <td>
                <?php if (!empty($u['enrolled_classes'])): ?>
                  <?php foreach($u['enrolled_classes'] as $class): ?>
                    <div style="margin-bottom: 5px;">
                      <strong><?= htmlspecialchars($class['title'] ?? 'Unknown Class') ?></strong>
                      <div class="class-details">
                        <?= htmlspecialchars($class['day_of_week'] ?? 'Unknown Day') ?> 
                        <?= date('H:i', strtotime($class['start_time'] ?? '00:00')) ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <span style="color: #666; font-style: italic;">No class enrollments</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<script src="../../home/js/wow.min.js"></script>
<script> new WOW().init(); </script>
</body>
</html>