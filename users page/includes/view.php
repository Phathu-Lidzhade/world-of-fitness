<?php
// view.php - View
declare(strict_types=1);

// Extract data for the view
$user = $data['user'] ?? null;
$classes = $data['classes'] ?? [];
$events = $data['events'] ?? [];
$recs = $data['recs'] ?? [];
$plan = $data['plan'] ?? null;
$payments = $data['payments'] ?? [];
$paidClassIds = $data['paidClassIds'] ?? [];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?= $cssPath ?>">
  <title>World of Fitness - User Dashboard</title>
  
  <!-- Fallback inline styles -->
  <style>
    /* Critical CSS for banner */
    .world-fitness-banner {
      background: #000000;
      color: #ffffff;
      padding: 20px 0;
      text-align: center;
      border-bottom: 4px solid #d10000;
    }
    .world-fitness-banner h1 {
      font-family: 'Oswald', Arial, sans-serif;
      font-size: 3rem;
      font-weight: 700;
      margin: 0;
    }
    .fitness-text {
      color: #d10000;
    }
    .data-debug {
      background: #ffeb3b;
      padding: 10px;
      margin: 10px 0;
      border-left: 4px solid #ff9800;
      display: none; /* Set to block for debugging */
    }
    .class-day-group {
      margin-bottom: 15px;
    }
    .class-day-header {
      color: #d10000;
      font-weight: 600;
      margin-bottom: 5px;
      font-size: 14px;
    }
    .class-item {
      margin-left: 15px;
      margin-bottom: 8px;
      font-size: 13px;
    }
    .class-time {
      color: #666;
      font-size: 12px;
    }
    .class-room {
      color: #888;
      font-size: 11px;
      font-style: italic;
    }
    .no-classes-message {
      color: #d10000;
      font-size: 12px;
      margin-top: 5px;
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

<!-- Dashboard Content -->
<div class="dashboard container">
  <header class="dashboard-header">
    <?php if ($user): ?>
      <h1>Welcome, <?= htmlspecialchars($user['firstName'] . ' ' . $user['surname']) ?></h1>
    <?php else: ?>
      <h1>Welcome, User</h1>
    <?php endif; ?>
    <form class="logout" action="../api/logout.php" method="post">
      <button class="btn primary" type="submit" title="Logout">Logout</button>
    </form>
  </header>

  <div class="dash-grid">
    <!-- Profile Widget -->
    <div class="widget" data-panel="profile">
      <div class="profile">
        <?php if ($user): ?>
          <div class="avatar"><?= strtoupper(substr($user['firstName'],0,1) . substr($user['surname'],0,1)) ?></div>
          <div>
            <h3>Profile</h3>
            <p class="meta"><?= htmlspecialchars($user['email']) ?> • <?= htmlspecialchars($user['numberPhone']) ?></p>
            <!--<div class="more">View full profile</div>-->
          </div>
        <?php else: ?>
          <div class="avatar">?</div>
          <div>
            <h3>Profile</h3>
            <p class="meta">User information not available</p>
            <div class="more">View full profile</div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Recommendations Widget -->
    <div class="widget" data-panel="recommendations">
      <h3>Workout Recommendations</h3>
      <?php if (empty($recs)): ?>
        <p class="meta">No recommendations available at the moment.</p>
      <?php else: ?>
        <?php foreach(array_slice($recs,0,3) as $r): ?>
          <p class="meta">
            <strong><?= htmlspecialchars($r['title'] ?? 'Untitled') ?></strong>, 
            <?= htmlspecialchars($r['summary'] ?? 'No description') ?>
          </p>
        <?php endforeach; ?>
      <?php endif; ?>
      <!--<div class="more">See more recommendations</div>-->
    </div>

    <!-- Classes Widget -->
    <div class="widget" data-panel="classes">
      <h3>Your Enrolled Classes</h3>
      <?php if (empty($classes)): ?>
        <p class="meta">You are not enrolled in any classes yet.</p>
        <?php if ($plan): ?>
          <p class="no-classes-message">
            You have a <?= htmlspecialchars($plan['title']) ?> plan but no class enrollments.<br>
            Classes should be automatically assigned. Please contact support if this doesn't update soon.
          </p>
        <?php else: ?>
          <p class="no-classes-message">
            Purchase a training plan to get automatically enrolled in classes.
          </p>
        <?php endif; ?>
      <?php else: ?>
        <?php 
        // Group classes by day for better organization
        $classesByDay = [];
        foreach($classes as $c) {
            $day = $c['day_of_week'];
            if (!isset($classesByDay[$day])) {
                $classesByDay[$day] = [];
            }
            $classesByDay[$day][] = $c;
        }
        
        // Define day order
        $dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        ?>
        
        <?php foreach($dayOrder as $day): ?>
          <?php if (isset($classesByDay[$day])): ?>
            <div class="class-day-group">
              <div class="class-day-header"><?= $day ?></div>
              <?php foreach($classesByDay[$day] as $c): ?>
                <div class="class-item">
                  <strong><?= htmlspecialchars($c['title'] ?? 'Untitled Class') ?></strong>
                  <div class="class-time">
                    <?= date('H:i', strtotime($c['start_time'] ?? '00:00')) ?> - 
                    <?= date('H:i', strtotime($c['end_time'] ?? '00:00')) ?>
                  </div>
                  <?php if (!empty($c['room'])): ?>
                    <div class="class-room">📍 <?= htmlspecialchars($c['room']) ?></div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>
      <div class="more">
    <a href="classes.php" style="color: inherit; text-decoration: none;">
        View all classes
    </a>
</div>
    </div>

    <!-- Calendar Widget -->
    <div class="widget" data-panel="calendar">
      <h3>Upcoming Classes This Week</h3>
      <?php if (empty($events)): ?>
        <p class="meta">No classes scheduled for this week.</p>
        <?php if ($plan): ?>
          <p class="no-classes-message">
            You have a plan but no upcoming classes showing.<br>
            This should update automatically when you're enrolled in classes.
          </p>
        <?php endif; ?>
      <?php else: ?>
        <?php 
        // Group events by day
        $eventsByDay = [];
        foreach($events as $event) {
            $day = $event['day_of_week'];
            if (!isset($eventsByDay[$day])) {
                $eventsByDay[$day] = [];
            }
            $eventsByDay[$day][] = $event;
        }
        
        // Show classes for the next few days in order
        $daysToShow = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $shown = 0;
        $maxToShow = 4; // Maximum days to show in the widget
        ?>
        
        <?php foreach($daysToShow as $day): ?>
          <?php if (isset($eventsByDay[$day]) && $shown < $maxToShow): ?>
            <div class="class-day-group">
              <div class="class-day-header"><?= $day ?></div>
              <?php foreach($eventsByDay[$day] as $event): ?>
                <div class="class-item">
                  <strong><?= htmlspecialchars($event['title'] ?? 'Class') ?></strong>
                  <div class="class-time">
                    <?= date('H:i', strtotime($event['start_time'] ?? '00:00')) ?> - 
                    <?= date('H:i', strtotime($event['end_time'] ?? '00:00')) ?>
                  </div>
                  <?php if (!empty($event['room'])): ?>
                    <div class="class-room">📍 <?= htmlspecialchars($event['room']) ?></div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
            <?php $shown++; ?>
          <?php endif; ?>
        <?php endforeach; ?>
        
        <?php if ($shown === 0): ?>
          <p class="meta">No upcoming classes in the next few days.</p>
        <?php endif; ?>
        
        <?php if (count($events) > 0): ?>
          <p class="meta" style="margin-top: 10px; font-size: 12px;">
            Total enrolled classes: <?= count($events) ?>
          </p>
        <?php endif; ?>
      <?php endif; ?>
      <div class="more">
    <a href="classes.php" style="color: inherit; text-decoration: none;">
        View full schedule
    </a>
</div>
    </div>

    <!-- Health Tips Widget -->
    <div class="widget" data-panel="tips">
      <h3>Health Tips</h3>
      <p class="meta">Drink more water, get enough sleep, and add mobility work.</p>
      <p class="meta">Eat balanced meals, with protein, carbohdrates and healthy fats.</p>
      <p class="meta">Warm up for 5 to 10 mins before exercising to prevent injuries.</p>
      <!--<div class="more">More tips</div>-->
    </div>

    <!-- Workout Plan Widget -->
    <div class="widget" data-panel="plan">
      <h3>Your Workout Plan</h3>
      <?php if ($plan): ?>
        <p class="meta">
          <strong><?= htmlspecialchars($plan['title'] ?? 'No Title') ?></strong><br>
          <?= htmlspecialchars($plan['description'] ?? 'No description available') ?>
        </p>
        <p class="meta">
          <strong>Price:</strong> R<?= number_format((float)($plan['price'] ?? 0), 2) ?><br>
          <?php if ($plan['expires_at']): ?>
            <strong>Expires:</strong> <?= date('F j, Y', strtotime($plan['expires_at'])) ?>
          <?php endif; ?>
        </p>
      <?php else: ?>
        <p class="meta">You don't have an active plan.</p>
      <?php endif; ?>
      <div class="more">
        <a href="../payment/index.php" style="color: inherit; text-decoration: none;">
          <?= $plan ? 'Change Plan' : 'Get Plan' ?>
        </a>
      </div>
    </div>
  </div>

  <section class="payments-section">
    <h2>Recent Payments</h2>
    <?php if (empty($payments)): ?>
      <p>No payments yet.</p>
    <?php else: ?>
      <ul class="payments">
        <?php foreach($payments as $p): ?>
          <li>
            <?= htmlspecialchars($p['plan_title'] ?? $p['class_title'] ?? 'Purchase') ?> —
            R<?= number_format((float)($p['amount'] ?? 0), 2) ?>,
            <?= htmlspecialchars($p['paid_at'] ?? 'Unknown date') ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>
</div>

<!-- Modal for widget details -->
<div id="dashModal" class="modal" aria-hidden="true">
  <div class="panel">
    <button class="close-btn" id="closeModal">Close</button>
    <div id="modalContent"></div>
  </div>
</div>

<script src="../home/js/wow.min.js"></script>
<script> new WOW().init(); </script>
<script src="../home/js/dashboard.js"></script>
<script>
  window.DASH_DATA = <?= json_encode($data, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;
</script>

</body>
</html>