<?php
require_once "../api/config_session.php";
require_once "includes/view.php";
$errors = getErrors('login');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Login - World of Fitness</title>
</head>
<body>

  <!-- Banner -->
  <header class="banner">
    <h1>World of <span>Fitness</span></h1>
  </header>

  <div class="login-container">
    <h2>Login to Your Account</h2>

    <form action="includes/user_login.php" method="post">

      <?= generalError($errors) ?>

      <label for="email">Email</label><br>
      <input type="email" name="email" id="email" placeholder="Enter your email">
      <?= error('email', $errors) ?>
      <br><br>

      <label for="password">Password</label><br>
      <input type="password" name="password" id="password" placeholder="Enter your password">
      <?= error('password', $errors) ?>
      <br><br>

      <button type="submit">Login</button>
    </form>

    <p>If you don't have an account, <a href="../create account/account.php">Create Account</a></p>
    <a class="home-link" href="../index.html">Home</a>
  </div>

  <?php
  unset($_SESSION['errors'], $_SESSION['old']);
  ?>
</body>
</html>
