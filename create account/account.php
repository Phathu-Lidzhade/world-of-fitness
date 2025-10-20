<?php
require_once "../api/config_session.php";
require_once "includes/view.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Create account</title>
</head>
<body>

  <!-- Banner -->
<header class="banner">
  <h1>World of <span>Fitness</span></h1>
</header>


  <div class="page-wrap">
    <div class="form-card">
      <div class="form-left">
        <p class="lead">Enter your details</p>
        <p class="sublead">Join the world of fitness today.</p>

        <form action="includes/create.php" method="post" class="signup-form">

          <div>
            <label for="firstName">First Name</label>
            <input type="text" name="firstName" id="firstName" placeholder="First Name" value="<?= old('firstName') ?>">
            <?= error('firstName') ?>
          </div>

          <div>
            <label for="surname">Surname</label>
            <input type="text" name="surname" id="surname" placeholder="Surname" value="<?= old('surname') ?>">
            <?= error('surname') ?>
          </div>

          <div>
            <label for="numberPhone">Number Phone</label>
            <input type="text" name="numberPhone" id="numberPhone" placeholder="Number phone" value="<?= old('numberPhone') ?>">
            <?= error('numberPhone') ?>
          </div>

          <div>
            <label for="idNumber">Id Number</label>
            <input type="password" name="idNumber" id="idNumber" placeholder="Id Number" value="<?= old('idNumber') ?>">
            <?= error('idNumber') ?>
          </div>

          <div class="full">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Email" value="<?= old('email') ?>">
            <?= error('email') ?>
          </div>

          <div>
            <label for="password">Password</label>
            <input type="password" name="password" id="pwd" placeholder="Password">
            <?= error('password') ?>
          </div>

          <div>
            <label for="confirmPassword">Confirm password</label>
            <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm password">
            <?= error('confirmPassword') ?>
          </div>

          <button type="submit">Create account</button>

          <p class="form-footer">
            Already have an account? <a href="../login/login.php">Login</a>
          </p>

        </form>

      </div>

      <div class="form-right">
        <p class="right-hero">Push your limits</p>
        <p class="small">Train hard, eat clean, and stay consistent. Welcome to your fitness journey!</p>
      </div>
    </div>
  </div>

<?php
unset($_SESSION['errors'], $_SESSION['old']);
?>
</body>
</html>
