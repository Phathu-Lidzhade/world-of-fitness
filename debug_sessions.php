<?php
// test_logout_path.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$logout_path = __DIR__ . '/api/logout.php';
echo "<h2>Logout Path Test</h2>";
echo "<p>Logout file path: " . $logout_path . "</p>";
echo "<p>File exists: " . (file_exists($logout_path) ? "✅ YES" : "❌ NO") . "</p>";

if (file_exists($logout_path)) {
    echo "<p>File content preview:</p>";
    echo "<pre>";
    echo htmlspecialchars(file_get_contents($logout_path));
    echo "</pre>";
}
?>