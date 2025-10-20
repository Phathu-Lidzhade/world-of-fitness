<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $firstName = $_POST["firstName"] ?? '';
  $surname = $_POST["surname"] ?? '';
  $numberPhone = $_POST["numberPhone"] ?? '';
  $idNumber = $_POST["idNumber"] ?? '';
  $email = $_POST["email"] ?? '';
  $pwd = $_POST["password"] ?? '';
  $confirmPassword = $_POST["confirmPassword"] ?? '';

  try {
    
    require_once "../../api/dbh.php";
    require_once "model.php";
    require_once "view.php";
    require_once "contr.php";

    //Error handlers
    $errors = [];

    // Empty field checks
    if (empty($firstName)) {
      $errors['firstName'] = "First name is required.";
    }
    if (empty($surname)) {
      $errors['surname'] = "Surname is required.";
    }
    if (empty($numberPhone)) {
      $errors['numberPhone'] = "Phone number is required.";
    }
    if (empty($idNumber)) {
      $errors['idNumber'] = "ID number is required.";
    }
    if (empty($email)) {
      $errors['email'] = "Email is required.";
    }
    if (empty($pwd)) {
      $errors['password'] = "Password is required.";
    }

    // Format and business rules
    if (!empty($email) && is_email_invalid($email)) {
      $errors['email'] = "Please enter a valid email address.";
    }

    if (!empty($numberPhone) && !is_sa_phone_simple($numberPhone)) {
      $errors['numberPhone'] = "Phone must be 10 digits and start with 0.";
    }  

    if (!empty($idNumber) && is_idNumber_invalid($idNumber)) {
      $errors['idNumber'] = "Invalid South African ID number.";
    }

    if (!empty($email) && is_email_taken($pdo, $email)) {
      $errors['email'] = "This email is already registered.";
    }

    require_once "../../api/config_session.php";

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        // Save old values (don't save passwords)
        $_SESSION['old'] = [
            'firstName' => $firstName,
            'surname' => $surname,
            'numberPhone' => $numberPhone,
            'idNumber' => $idNumber,
            'email' => $email
        ];
        header("Location: ../account.php");
        die();
    }

    if ($pwd == $confirmPassword) {
      //$pwd = password_hash($pwd, PASSWORD_DEFAULT);
      create_user($pdo, $firstName, $surname, $numberPhone, $idNumber, $pwd, $email);
  
      header("Location: ../../login/login.php");

      $pdo = null;
      $stmt = null;
      die();
    }
    else {
      // use session error + redirect instead of echo
      require_once "../../api/config_session.php";
      $_SESSION['errors'] = ['confirmPassword' => 'Password does not match'];
      $_SESSION['old'] = [
          'firstName' => $firstName,
          'surname' => $surname,
          'numberPhone' => $numberPhone,
          'idNumber' => $idNumber,
          'email' => $email
      ];
      header("Location: ../account.php");
      die();
    }

  } catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
  }
  

}else {
  header("Location: ../account.php");
  die();
}

/*
    if (is_input_empty($firstName, $surname, $numberPhone, $idNumber, $pwd, $email)) {
      $errors['general'] = "All fields are required.";
    }
    if (is_email_invalid($email)) {
      $errors['email'] = "Please enter a valid email address.";
    }
    if (is_idNumber_invalid($idNumber)) {
      $errors['idNumber'] = "Invalid South African ID number.";
    }
    if (is_email_taken($pdo, $email)) {
      $errors['email'] = "This email is already registered.";
    }*/